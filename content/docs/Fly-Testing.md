+++
title = "Testing"
description = "First-class, zero-overhead testing built into Fly: inline test blocks, suites, case steps, and flyp test integration."
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
string classify(const int n) {
    if n > 0 {
        out = "positive"
        test {
            assertTrue(out == "positive")
        }
    } elsif n < 0 {
        out = "negative"
        test {
            assertTrue(out == "negative")
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

Writing to any scope 1 or scope 2 variable is a **compile error**:

```fly
test {
    out = "overridden"   // error: out is read-only inside test {}
}
```

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

No boilerplate is needed. The binary exits with code `0` on success, `1` on the first
assertion failure.

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
    assertEqI(r, 5)
}
```

### Validation

Using `case` outside a suite test-method is a compile error.

---

## Assertions — `fly.test`

The `fly.test` namespace provides assertion helpers. They are auto-imported in test mode —
no `import fly.test` statement is needed.

| Function | Signature | Fails when |
|---|---|---|
| `assertTrue` | `(const bool b)` | `b` is false |
| `assertFalse` | `(const bool b)` | `b` is true |
| `assertEqI` | `(const int a, const int b)` | `a != b` |
| `assertEqL` | `(const long a, const long b)` | `a != b` |
| `assertEqStr` | `(const string a, const string b)` | strings differ |
| `assertApprox` | `(const double a, const double b)` | `|a - b| > 1e-9` |

All assertions exit the process with code `1` on failure.

```fly
void divideTest() {
    case "basic": {
        int r = divide(10, 2)
        assertEqI(r, 5)
    }
    case "negative dividend": {
        int r = divide(-6, 3)
        assertEqI(r, -2)
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

import fly.test

// Returns a string classification of n.
// test {} blocks observe intermediate state during suite runs.
string classify(const int n) {
    if n > 0 {
        out = "positive"
        test {
            assertTrue(out == "positive")
            assertTrue(n > 0)
        }
    } elsif n < 0 {
        out = "negative"
        test {
            assertTrue(out == "negative")
        }
    } else {
        out = "zero"
        test {
            assertEqI(n, 0)
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
