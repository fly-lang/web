+++
title = "Testing"
description = "First-class, zero-overhead testing built into Fly: inline test blocks, suites, case steps, and the built-in fly --suite runner."
template = "docs-page.html"
weight = 3
+++

# Testing

Fly ships a test system that is intrinsic to the language: tests live directly inside
production code, are invisible in release builds, and require no test harness or separate
build target.

---

## Overview

The system has two complementary halves:

- **`test {}` blocks** — inline observers that live inside production functions and read
  local state. They are completely stripped at the IR level in release builds (zero overhead,
  zero binary size increase).
- **`suite` declarations** — test drivers that supply inputs and activate the blocks in
  test mode.

The guiding principle is that tests are *documentation of the implementation*, not of the
public interface — so they have read-only access to everything, including private symbols.

---

## Inline test blocks — `test {}`

A `test {}` block can appear anywhere inside a function or method body.

```fly
import fly.assert.*

string classify(const int n) {
    if n > 0 {
        out = "positive"
        test {
            assertTrue(out == "positive", 1)
        }
    } elsif n < 0 {
        out = "negative"
        test {
            assertTrue(out == "negative", 2)
        }
    } else {
        out = "zero"
    }
}
```

### Scope rules

Inside a `test {}` block, three scopes are available:

| Scope | Contents | Access |
|---|---|---|
| 1 | All locals of the enclosing function, including `out` | **read-only** |
| 2 | All module-level symbols (functions, globals) | read and call |
| 3 | Fields and helper methods of the active suite | read and call |

By design, a `test {}` block is meant to be an **observer**: it should only read locals and
call helpers, never mutate the enclosing computation. Treat enclosing locals (including
`out`) as read-only:

```fly
test {
    out = "overridden"   // don't do this — test blocks should only observe
}
```

> ⚠️ Read-only enforcement is **not yet active** in the current compiler. The resolver tracks
> that it is inside a test block (an `InTestBlock` flag) but does not yet reject writes, so
> the example above currently compiles. Follow the convention until the check is wired up.

### Release vs test builds

In a release build (any compile without `--test`/`--suite`), the compiler
strips every `test {}` block at the Sema stage — no IR is emitted, no binary size increase.

In test mode (`fly --test`, implied by `fly --suite`), each block is wrapped
in a TLS-guarded branch:

```
load @__fly_test_ctx_ptr
if null → skip (production call path, no suite active)
if non-null → execute the test block
```

This means production calls to the same function — made from non-suite code even during a
test run — skip the blocks silently.

---

## `suite` declarations

A `suite` is a top-level declaration, syntactically parallel to `class`, that drives test
execution.

```fly
suite MathSuite {

    // lifecycle hooks — recognised by exact name
    void setup()    { /* runs once before all test-methods */ }
    void teardown() { /* runs once after all test-methods */ }

    // helper method — callable from case blocks
    List<int> makeRange(const int from, const int to) { ... }

    // test-method — recognised by the "Test" suffix
    void classifyTest() {
        case "positive": classify(5)
        case "negative": classify(-3)
        case "range": {
            for n in makeRange(1, 10) {
                classify(n)
            }
        }
    }
}
```

### Method roles

| Method | Recognition | Executed by runner |
|---|---|---|
| `setup()` | Exact name | Once, before all test-methods |
| `teardown()` | Exact name | Once, after all test-methods (even on failure) |
| `nameTest()` | `Test` suffix | Automatically, in declaration order |
| Any other name | — | Helper; callable from cases, never run directly |

`setup` and `teardown` are **not** language keywords — they are plain identifiers that the
compiler recognises by their exact name inside a suite. Outside a suite they are ordinary
identifiers.

> `case` blocks are only parsed inside methods whose name ends in `Test` — in
> a helper method, `case` is a syntax error.

### Suite instance state

A suite is a real instance: the runner allocates one zero-initialised object
per suite and passes it as `this` to `setup`, every test-method and
`teardown`. Instance **fields** and **helper methods** work exactly like in a
class:

```fly
suite CounterSuite {
    int calls            // shared across the suite's methods, starts at 0

    int bump() {         // helper — full `this` access
        this.calls = this.calls + 1
        out = this.calls
    }

    void stateTest() {
        case "fields persist across cases": {
            this.bump()
            this.bump()
            assertEqI(this.calls, 2, 1)
        }
    }
}
```

### Handled errors inside cases

A `handle {}` block inside a case owns its errors: a failure **caught by a
handle never fails the case** — the per-statement check is suspended inside
the guarded block, exactly matching standalone semantics. Only errors that
escape every handle reach the runner:

```fly
void recoveryTest() {
    case "fallback path": {
        handle {
            mightFail()          // a caught failure stays inside the handle
        }
        if (error) { useFallback() }
        assertTrue(ok(), 1)      // an UNCAUGHT failure here fails the case
    }
}
```

### Implicit `main()`

A suite file gets a generated `main()` that runs the suite and **reports every
case**:

```
suite <Name>
  <firstTest>
    <case label> ... ok
    <case label> ... FAIL(<code>): <message>
  <secondTest>
    ...
suite <Name>: <N> cases, <P> passed, <F> failed
```

Flow: `setup()` → each test-method (each `case` reported individually) →
`teardown()` → summary. Rules:

- A failing case does **not** stop the run: the error is reported and the next
  case executes (per-case isolation).
- Within one case the **first** failure wins: the runner checks the error after
  every statement and jumps to the case's report as soon as one is recorded.
- If `setup()` fails, it is reported (`setup ... FAIL(...)`), the test-methods
  are **skipped**, and `teardown()` still runs.
- A `fail` that escapes a test-method outside any `case` is reported as
  `body ... FAIL(...)`.
- Exit code: **0** when every case passed, **1** otherwise (the individual
  codes are in the report lines).
- The case label line is printed *before* the body runs and completed by the
  result: if a case crashes the process, the last printed text names it.

---

## `case` blocks in test-methods

Inside a test-method, `case "label": { ... }` defines a named execution step.

```fly
void classifyTest() {
    case "positive": classify(5)
    case "negative": classify(-3)
    case "zero":     classify(0)
}
```

### Semantics vs `switch`

`case` in a test-method is **not** a switch: all cases execute sequentially without
`break`. The label is a plain string used for identification, not a dispatch value.

### Error scope isolation

Each case zeroes the shared error struct on entry and consumes it (reporting
`ok` or `FAIL`) on exit, so an assertion failure in one case does not affect
subsequent cases — each case is an independent unit of verification. Within a
case, the runner checks the error after every statement: the **first** failure
ends the case immediately (later statements do not run).

### Single-statement shorthand

A one-liner body does not need braces:

```fly
case "empty string": process("")
case "nil check": {
    // multi-statement case needs braces
    int r = divide(10, 2)
    assertEqI(r, 5, 1)
}
```

### Validation

Using `case` outside a suite test-method is a compile error.

---

## Assertions — `fly.assert`

The assertion helpers live in the **`fly.assert`** namespace (standard library file
`std/lib/assert.fly`). Import it explicitly — a wildcard import brings the helpers into
scope so they can be called without a prefix:

```fly
import fly.assert.*     // assertTrue, assertEqI, … directly in scope
// or:
import fly.assert       // call as assert.assertTrue(...), assert.assertEqI(...)
```

Every assertion takes a **trailing `const int code`** argument. When the check
fails, the assertion does `fail code, <message>` — it records a language `error`
(code + descriptive message, e.g. `assertEqI: got 5, expected 7`) in the
caller's error struct. A passing assertion does nothing.

What happens to the recorded error depends on the context:

- **Inside a suite `case`**: the runner detects it right after the failing
  statement, prints `<label> ... FAIL(<code>): <message>`, and moves on to the
  next case.
- **In a plain `main()`-based test**: the error propagates to `main()`, which
  prints `error <code>: <message>` to stderr and exits with `<code>`. Note that
  `fail` is not stack unwinding — execution continues after a failed assert, so
  a later failing assert overwrites the recorded error (the reported failure is
  the LAST one).

| Function | Signature | Exits with `code` when |
|---|---|---|
| `assertTrue` | `(const bool b, const int code)` | `b` is false |
| `assertFalse` | `(const bool b, const int code)` | `b` is true |
| `assertEqI` | `(const int got, const int exp, const int code)` | `got != exp` |
| `assertEqL` | `(const long got, const long exp, const int code)` | `got != exp` |
| `assertStr` | `(const string got, const string exp, const int code)` | strings differ |
| `assertNotEmpty` | `(const string s, const int code)` | `s` is empty |
| `assertGtI` | `(const int got, const int threshold, const int code)` | `got <= threshold` |
| `assertApprox` | `(const double got, const double exp, const int code)` | `\|got - exp\| > 1e-9` |
| `assertApproxEps` | `(const double got, const double exp, const double eps, const int code)` | `\|got - exp\| > eps` |
| `errExit` | `(const int code)` | always — unconditional `fail code` |

> Use distinct codes per assertion: the code appears in the report line
> (`FAIL(<code>): <message>`) and, for `main()`-based tests, becomes the exit
> code. A suite binary always exits `0`/`1` — the codes live in the report.

```fly
import fly.assert.*

void divideTest() {
    case "basic": {
        int r = divide(10, 2)
        assertEqI(r, 5, 1)
    }
    case "negative dividend": {
        int r = divide(-6, 3)
        assertEqI(r, -2, 2)
    }
}
```

---

## Running tests — `fly --suite`

`fly` compiles a **directory** (never a list of files — see
[Command Line](@/docs/Fly-CLI.md)): the suites are discovered from the source
root, compiled into **one test executable**, and run. `fly` exits with the
run's code: **0** = every case passed, **1** = any failure.

```sh
# Every suite under the current directory, one binary, one run
fly --suite

# Every suite under ./src
fly --suite --src-dir src

# One suite by name
fly --suite=MathSuite

# One suite by fully qualified name
fly --suite=fly.math.test.MathSuite

# Every suite in a NAMESPACE (sub-namespaces included):
fly --suite=fly.math.test

# Run a single test-method ("Prova" matches "Prova" or "ProvaTest")
fly --suite=MathSuite --test=Prova
```

The `--suite=<Sel>` selector accepts three forms:

| Selector | Runs |
|---|---|
| `MathSuite` | the suite with that name |
| `fly.math.test.MathSuite` | that exact suite, disambiguated by namespace |
| `fly.math.test` | **every** suite declared in that namespace or any namespace under it |

Organise suites by namespace and whole areas become runnable in one command
(`--suite=fly.std.test`, `--suite=fly.compiler.codegen.test`, …).

Notes:

- `--suite` implies test mode (`test {}` blocks are compiled in).
- A selector matching **no** suite or namespace is an error.
- A `--test=<Method>` filter matching **zero** test-methods across the
  selected suites is a **compile error** — a typo'd filter cannot produce a
  green "0 cases" run.
- A bare `--test` (no value) compiles in test mode without running anything —
  useful for library builds whose `test {}` blocks are activated by a suite
  linked later.
- The suite/method filters are applied at compile time in the implicit
  `main()`.

---

## Complete example

**`src/math.fly`** — production code with inline tests:

```fly
namespace math

import fly.assert.*

// Returns a string classification of n.
// test {} blocks observe intermediate state during suite runs.
string classify(const int n) {
    if n > 0 {
        out = "positive"
        test {
            assertTrue(out == "positive", 1)
            assertTrue(n > 0, 2)
        }
    } elsif n < 0 {
        out = "negative"
        test {
            assertTrue(out == "negative", 3)
        }
    } else {
        out = "zero"
        test {
            assertEqI(n, 0, 4)
        }
    }
}
```

**`src/MathSuite.fly`** — the test driver:

```fly
import math

suite MathSuite {

    void setup() {
        // Initialise any shared state here.
    }

    void teardown() {
        // Clean up shared state here.
    }

    void classifyTest() {
        case "positive":  math.classify(7)
        case "negative":  math.classify(-4)
        case "zero":      math.classify(0)
    }
}
```

**Running:**

```sh
fly --suite --src-dir src
# → discovers MathSuite.fly, pulls math.fly through the import, compiles in
#   test mode and runs the suite
# → setup → classifyTest (3 cases, each reported) → teardown → summary
# → exits 0 when every case passed
```
