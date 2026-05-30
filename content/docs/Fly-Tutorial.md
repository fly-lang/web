+++
title = "Tutorial"
description = "Step-by-step practical guide to writing Fly programs — from Hello World to objects and error handling."
template = "docs-page.html"
weight = 3
+++

## Hello World

Every Fly program starts from a `main()` function. Import a module with `import`, then call its functions using the namespace prefix.

```fly
import fly.os

void main() {
    os.print("Hello, World!")
}
```

---

## Variables and types

Variables are declared with their type. An initial value is optional; uninitialized variables default to their zero value.

```fly
int count
int x = 10
bool ready = false
string name = "Fly"
```

**Built-in types:** `bool`, `byte`, `short`, `ushort`, `int`, `uint`, `long`, `ulong`, `float`, `double`, `string`.

---

## Functions

Declare a **return type** before the function name. Inside the body, assign to the special identifier `out` to produce the return value.

```fly
int double(const int n) {
    out = n * 2
}

void main() {
    int x = double(21)   // x = 42
}
```

The source reads as return-by-value, but the compiler generates a hidden by-reference output parameter — so **no copy is ever made**. You get `int x = double(21)` ergonomics with `double(21, &x)` performance.

`const` marks an input parameter (read-only). A function without a return type is void.

For multiple outputs, you can use traditional output parameters (non-`const`):

```fly
minMax(const int a, const int b, int min, int max) {
    if (a < b) {
        min = a
        max = b
    } else {
        min = b
        max = a
    }
}

void main() {
    int lo
    int hi
    minMax(3, 7, lo, hi)   // lo=3, hi=7
}
```

Or declare multiple return types and use `out[0]`, `out[1]`, …:

```fly
int,int minMax(const int a, const int b) {
    if (a < b) { out[0] = a  out[1] = b }
    else       { out[0] = b  out[1] = a }
}

void main() {
    int lo = minMax(3, 7)   // lo = 3 (first return value)
}
```

---

## Importing modules

```fly
import fly.str          // use as str.len(...)
import fly.str.*        // use unqualified: len(...)
import fly.str as s     // use as s.len(...)
```

---

## Error handling

Use `fail` inside a function to signal an error. `fail` accepts zero to three comma-separated arguments — an integer code, a string message, and/or an object instance, in any combination:

```fly
fetch(const string url) {
    if (url == "") {
        fail 404, "Not Found"   // integer code + string message
    }
}
```

**How propagation works:** every function has a hidden error-pointer parameter. When `fail` fires with no enclosing `handle` in the current function, it writes to the error struct and returns immediately. The calling code resumes at the next instruction — it does **not** unwind the stack.

```fly
void main() {
    fetch("")        // writes error 404; returns, execution continues
    fetch("/ok")     // STILL CALLED — caller is not unwound
    // main exits with code 404
}
```

Use `handle` to intercept errors. All calls inside the block share a dedicated error struct. After the block, check `if (err)`:

```fly
void main() {
    error err handle {
        fetch("")        // fails and writes error; handle body continues
        fetch("/ok")     // still called (callee fail ≠ jump in caller)
    }
    if (err) {
        // handle the error
    }
}
```

When `fail` fires **directly inside the handle body** (same function), execution jumps immediately past the remaining handle code to the check point:

```fly
void main() {
    error err handle {
        if (someCondition) {
            fail 500        // jumps to safe block; neverReached() is skipped
        }
        neverReached()
    }
    if (err) { /* code = 500 */ }
}
```

If you don't need to name the error, omit the variable:

```fly
void main() {
    handle {
        fetch("")   // error is swallowed silently
    }
}
```

To re-raise an error to the caller, use a bare `fail`:

```fly
wrapper() {
    error err handle {
        fetch("")
    }
    if (err) {
        fail    // propagate to wrapper's caller
    }
}
```

---

## Structs

A `struct` holds data fields and can extend one other struct. Structs have no virtual dispatch — access is direct.

Plain `new` on a struct allocates on the **stack**. The variable is freed automatically when the scope exits — do not call `delete`.

```fly
struct Point {
    int x
    int y
}

struct Point3D : Point {
    int z
}

void main() {
    Point3D p = new Point3D()   // stack allocation
    p.x = 1
    p.y = 2
    p.z = 3
}   // p freed automatically — no delete needed
```

---

## Classes

A `class` adds virtual method dispatch via vtable. It can extend a struct (inheriting its fields) and implement one or more interfaces.

Plain `new` on a class allocates on the **heap**. You must call `delete` to free it.

```fly
interface Drawable {
    draw()
}

class Circle : Point, Drawable {
    int radius

    draw() {
        // render the circle
    }
}

void main() {
    Circle c = new Circle()   // heap allocation
    c.x = 0
    c.y = 0
    c.radius = 5
    c.draw()
    delete c   // free the heap memory
}
```

Visibility modifiers for members: `public`, `private`, `protected`.  
Use `static` for class-level fields and methods.

---

## Memory management

Instead of managing `new`/`delete` manually, use a **smart allocation qualifier**. All three qualifiers work on both structs and classes.

| Qualifier | Storage | Freed when | Copying |
|---|---|---|---|
| `new unique` | heap | variable goes out of scope | compile-time error |
| `new shared` | heap + refcount | last reference exits scope | allowed; increments refcount |
| `new weak` | heap | each holder exits scope | allowed; no refcount — first to exit frees, rest dangle |

### `new unique` — single owner

```fly
process() {
    Point p = new unique Point()   // heap-allocated
    p.x = 10
    p.y = 42
}   // free(p) emitted automatically — no delete
```

`unique` ownership cannot be copied. Attempting to assign a `unique` variable to another variable is a compile-time error.

### `new shared` — reference-counted

The runtime stores an 8-byte reference count immediately before the object data. Copies increment the count; each scope exit decrements it. When the count reaches zero the entire block is freed.

```fly
process() {
    Point a = new shared Point()   // refcount = 1
    Point b = a                    // refcount = 2
    // …
}   // refcount → 0 → freed automatically

```

### `new weak` — untracked alias

No reference count. Every holder calls `free()` at its own scope exit. Use only when you know the lifetime is not ambiguous.

```fly
process() {
    Point a = new weak Point()
    Point b = a   // b and a share the same pointer
}   // first scope exit frees the data; other becomes dangling
```
