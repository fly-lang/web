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

In a release build (`flyp build --release`, or just `fly` without `--test`), the compiler
strips every `test {}` block at the Sema stage — no IR is emitted, no binary size increase.

In test mode (`fly --test`, triggered automatically by `flyp test`), each block is wrapped
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

### Implicit `main()`

When compiled with `fly --test`, a suite file gets a generated `main()`:

```
setup()
firstTest()
secondTest()
...
teardown()
return 0
```

No boilerplate is needed. The generated `main()` returns `0` after `teardown()`. If an
assertion fails first, it calls `proc_exit(code)` with the `code` you passed, so the binary
exits with that code (a non-zero value) and `teardown()`/remaining tests are skipped.

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

Each case block allocates its own error handler. An assertion failure in one case does not
affect subsequent cases — each case is an independent unit of verification.

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

Every assertion takes a **trailing `const int code`** argument. When the check fails, the
process exits immediately with that `code` (via `proc_exit`), so each assertion doubles as a
unique failure marker. A passing assertion does nothing.

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
| `errExit` | `(const int code)` | always — unconditional exit with `code` |

> The process exit code on failure is the `code` you pass — not a fixed `1`. Use distinct
> codes per assertion to pinpoint which check failed.

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

## Running tests — `flyp test`

### `fly.toml` configuration

Add a `[test]` section to your project manifest:

```toml
[test]
suites     = ["src/**/*Suite.fly"]
parallel   = false
timeout_ms = 5000
fail_fast  = false
```

| Key | Type | Description |
|---|---|---|
| `suites` | `string[]` | Glob patterns for suite files |
| `parallel` | `bool` | Run suites concurrently (default `false`) |
| `timeout_ms` | `int` | Per-suite timeout in milliseconds (`0` = none) |
| `fail_fast` | `bool` | Stop on first suite failure |

### CLI

```sh
# Run all suites declared in fly.toml
flyp test

# Run only MathSuite
flyp test --suite MathSuite

# Run only the classifyTest method inside MathSuite
flyp test --suite MathSuite::classifyTest

# Run only the "positive" case inside classifyTest
flyp test --suite MathSuite::classifyTest::"positive"
```

The filter is applied via the `FLY_TEST_FILTER` environment variable before executing the
compiled suite binary. The compiler flag `--test` is passed automatically.

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

**`fly.toml`**:

```toml
[package]
name    = "myapp"
version = "0.1.0"

[test]
suites = ["src/*Suite.fly"]
```

**Running:**

```sh
flyp test
# → compiles MathSuite.fly with --test
# → runs setup → classifyTest (3 cases) → teardown
# → exits 0 on success
```
