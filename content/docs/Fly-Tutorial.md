+++
title = "Tutorial"
description = "Step-by-step practical guide to writing Fly programs — from Hello World to classes, generics, error handling and tests. Every example compiles with Fly 0.14.6."
template = "docs-page.html"
weight = 3
+++

Every example on this page was compiled and run with `fly` 0.14.6. To try one,
save it as `main.fly` in an empty directory and run `fly -o hello` there — `fly`
compiles a **directory**, not a file (see [Command Line](@/docs/Fly-CLI.md)).

## Hello World

A program starts from `main()`. Every function declares a return type, `void`
included. Import a namespace, then call its functions through its last segment.

```fly
import fly.os.io

void main() {
    io.printLn("Hello, World!")
}
```

---

## Variables and types

A variable is declared with its type. The initial value is optional; a variable
without one holds the zero value of its type.

```fly
int count              // 0
bool ready = false
string name = "Fly"
double ratio = 0.5
```

**Built-in types:** `bool`, `byte`, `short`, `ushort`, `int`, `uint`, `long`,
`ulong`, `float`, `double`, `string`, `pointer`.

Fixed-size arrays carry their length in the type, and an out-of-range index is
caught at run time:

```fly
int[3] arr = {10, 20, 30}
int second = arr[1]     // 20
```

Statements end at the line break; there is no `;` terminator.

---

## Functions

Declare the return type before the name and hand the value back with `return`.
Every path through a value function must reach a `return` — falling off the end
is a compile error, not a silent zero.

```fly
int twice(const int n) {
    return n * 2
}
```

`const` marks a parameter as a read-only input. Parameters may have default
values:

```fly
int scale(const int n, const int factor = 10) {
    return n * factor
}

void main() {
    int a = scale(4)       // 40
    int b = scale(4, 2)    // 8
}
```

A function can return **several values**. List the types, return the values in
order, and receive them with a multi-assignment into variables that already
exist:

```fly
int, int minMax(const int a, const int b) {
    if a < b { return a, b }
    return b, a
}

void main() {
    int lo = 0
    int hi = 0
    lo, hi = minMax(7, 3)   // lo = 3, hi = 7
}
```

---

## Control flow

Conditions need no parentheses; bodies use braces.

```fly
import fly.os.io

void main() {
    int total = 0

    for int i = 0; i < 5; i++ {
        total = total + i
    }

    while total > 3 {
        total = total - 3
    }

    if total == 1 {
        io.printLn("one")
    } elsif total == 2 {
        io.printLn("two")
    } else {
        io.printLn("other")
    }

    switch total {
        case 1:
            io.printLn("one")
        default:
            io.printLn("default")
    }
}
```

> **`switch` does not fall through** (from 0.15): a `case` body never continues
> into the next one, so no `break` is needed; list several values in one case
> with `case 1, 2:`. Up to 0.14 it fell through, as in C.

Two more things differ from C and are worth knowing early:

- Bitwise and shift operators bind **looser** than comparisons, and `&&` and
  `||` share one precedence level. Mixing them without parentheses is a compile
  error (from 0.15), so write the grouping: `(a & mask) == 0`, `(x && y) || z`.
- String literals decode escapes as in C (`"\n"`, `"\t"`, `"\u{E9}"`, from 0.15);
  `"""C:\dir"""` is a raw literal, which keeps backslashes as written. Up to
  0.14 every literal was raw.

---

## Importing modules

**Namespace import** — the last segment becomes the call prefix. This is the
standard style:

```fly
import fly.str

void main() {
    int n = str.len("hello")
}
```

**Class import** — brings one class, struct or enum into scope by name:

```fly
import fly.data.List

void main() {
    List<int> l = new List<int>()
}
```

**Wildcard import** — every public symbol of a namespace, used bare:

```fly
import fly.data.*

void main() {
    Stack<int> st = new Stack<int>()
}
```

**Alias import** — a local name of your choice:

```fly
import fly.str as s

void main() {
    string t = s.convert(42)
}
```

The path follows the **namespace** declared at the top of a source file
(`namespace fly.str`), not the file system.

---

## Error handling

Fly has no exceptions. A function signals an error with `fail`, which takes up
to three arguments: an integer code, a message, an object.

```fly
import fly.str

int parsePort(const string s) {
    if str.isEmpty(s) {
        fail 22, "empty port"
    }
    return 8080
}
```

A `handle` block guards a region. After it, the implicit variable `error` tells
you whether anything inside failed:

```fly
import fly.os.io

void main() {
    handle {
        int p = parsePort("")
        io.printLn("still runs")      // see below
    }
    if error {
        io.printLn("could not parse the port")
    }
}
```

**Propagation is not stack unwinding.** This is the most important rule of the
error model:

- A `fail` written **directly inside** a `handle` body jumps past the rest of
  that body.
- A `fail` inside a **called function** makes that function return. The caller
  then **continues with the next statement** — which is why `"still runs"` is
  printed above. The error is recorded; it does not interrupt the caller.

```fly
void main() {
    handle {
        fail 500
        io.printLn("never printed")
    }
    if error { io.printLn("caught 500") }
}
```

An error that no `handle` intercepts reaches `main`, and the process exits with
the failure code after `main` finishes:

```text
error 7: bad
```

So check `error` after every `handle` whose result you rely on, and do not use a
value returned by a call that may have failed without checking.

---

## Structs

A `struct` is plain data. It can extend another struct, has no virtual
dispatch, and lives on the **stack**: it is released when its scope ends.

```fly
struct Point {
    int x
    int y
}

struct Point3D : Point {
    int z
}

void main() {
    Point3D p = new Point3D()
    p.x = 1
    p.y = 2
    p.z = 3
}   // p is released here
```

Assigning a struct to another variable does not copy it — both names refer to
the same data.

---

## Classes and interfaces

A `class` has methods with virtual dispatch and can implement interfaces. The
constructor is a method named like the class, with no return type. Members are
reached through `this`.

```fly
import fly.os.io
import fly.str

interface Shape {
    int area()
}

class Rect : Shape {
    int w
    int h

    public Rect(const int w, const int h) {
        this.w = w
        this.h = h
    }

    public int area() {
        return this.w * this.h
    }

    public void free() {}
}

void main() {
    Rect r = new Rect(3, 4)
    Shape s = r
    io.printLn(str.convert(s.area()))   // 12
    r.free()
}
```

Member visibility is `public`, `private` or `protected`; `static` makes a member
class-level; `abstract` and `final` control inheritance.

---

## Enums

```fly
enum Color { RED, GREEN, BLUE }

void main() {
    Color c = Color.GREEN
    if c == Color.GREEN {
        // …
    }
}
```

---

## Memory management

Fly has no garbage collector and no `delete` operator. Three rules cover
everything:

| Value | Where it lives | Who releases it |
|---|---|---|
| `struct` | stack | the compiler, at scope exit |
| `string` | heap buffer | the compiler: at scope exit and on reassignment |
| `class` | heap | **you**, by calling the class's `free()` method |

```fly
import fly.data.List

void main() {
    List<int> l = new List<int>()
    l.add(1)
    l.add(2)
    l.free()        // releases the list's buffer
}
```

`free()` is a convention, not a keyword: a class that owns resources declares an
ordinary `void free()` method and its user calls it. Every container in the
standard library follows it. Forgetting the call leaks; using the object
afterwards is not detected.

---

## Generics

Generic classes and functions are **monomorphized**: each concrete type gets its
own compiled implementation, with no boxing.

```fly
class Box<T> {
    T value

    public Box(T v) {
        this.value = v
    }

    public T get() {
        return this.value
    }
}

T identity<T>(const T v) {
    return v
}

void main() {
    Box<int> b = new Box<int>(42)
    int n = b.get()                  // 42
    string s = identity("fly")       // T inferred as string
}
```

A generic **function** is always called by inference — `identity<int>(10)` does
not compile, so a type parameter must appear among the parameters. Explicit type
arguments belong to generic **classes**: `new Box<int>(42)`.

---

## Collections

`fly.data` provides `List`, `Stack`, `Queue`, `Deque`, `Set`, `Map` and `Tree`,
all generic. Owned values such as strings are copied in and released by the
container.

```fly
import fly.os.io
import fly.str
import fly.data.List
import fly.data.Map

void main() {
    List<string> names = new List<string>()
    names.add("apple")
    names.add("banana")

    for name in names {
        io.printLn(name)
    }
    names.free()

    Map<string, int> ages = new Map<string, int>()
    ages.put("ada", 36)
    if ages.has("ada") {
        io.printLn(str.convert(ages.get("ada")))
    }
    ages.free()
}
```

> `get` on a missing key or an out-of-range index returns the **zero value** of
> the element type rather than failing. Test with `has()` or `size()` first.

---

## Testing

Tests are part of the language. A `suite` groups test-methods; each `case` is
one reported step.

```fly
import fly.assert

int add(const int a, const int b) { return a + b }

suite MathSuite {
    void addTest() {
        case "adds": assert.assertEqI(add(2, 3), 5, 1)
    }
}
```

```text
$ fly --suite
suite MathSuite
  addTest
    adds ... ok
suite MathSuite: 1 cases, 1 passed, 0 failed
```

See [Testing](@/docs/Fly-Testing.md) for the full model.

---

## Where to go next

- [Language Reference](@/docs/Fly-Language-Reference.md) — the complete syntax and semantics.
- [Fly API](@/docs/Fly-API.md) — the standard library.
- [Conformance](@/docs/Fly-Conformance.md) — exactly what the compiler checks today, and what it does not yet.
- [Command Line](@/docs/Fly-CLI.md) — builds, libraries, projects and tools.
