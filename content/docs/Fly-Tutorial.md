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

main() {
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

main() {
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

main() {
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

main() {
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

Use `fail` inside a function to signal an error, with an optional numeric code and message.
The error propagates automatically to the caller — no explicit rethrow is needed.

```fly
fetch(const string url) {
    if (url == "") {
        fail 404, "Not Found"
    }
}
```

Wrap calls in a `handle` block to capture any error into an `error` variable, which is populated automatically.

```fly
main() {
    error err handle {
        fetch("")
    }
    if (err) {
        // err is populated automatically — inspect code or message
    }
}
```

If you don't need to name the error, omit the variable:

```fly
main() {
    handle {
        fetch("")
    }
}
```

---

## Structs

A `struct` holds data fields. It can extend one other struct.
Structs have no virtual dispatch — access is direct and allocation-free.

```fly
struct Point {
    int x
    int y
}

struct Point3D : Point {
    int z
}

main() {
    Point3D p = new Point3D()
    p.x = 1
    p.y = 2
    p.z = 3
    delete p
}
```

---

## Classes

A `class` adds virtual method dispatch via vtable. It can extend a struct (inheriting its fields) and implement one or more interfaces.

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

main() {
    Circle c = new Circle()
    c.x = 0
    c.y = 0
    c.radius = 5
    c.draw()
    delete c
}
```

Visibility modifiers for members: `public`, `private`, `protected`.  
Use `static` for class-level fields and methods.  
Use `const` on a method to mark the receiver as read-only.

---

## Smart pointers

Instead of managing `new`/`delete` manually, use a smart allocation strategy.

| Keyword | Ownership | Freed when |
|---|---|---|
| `new unique` | Single owner | Variable goes out of scope |
| `new shared` | Reference-counted | Last owner exits scope |
| `new weak` | No ownership tracking | First owner exits scope |

```fly
process() {
    Point p = new unique Point()
    p.x = 10
    p.y = 42
}   // p freed automatically here — no delete needed
```

A `unique` object cannot be copied; `shared` copies increment the reference count.
