+++
title = "Conformance"
description = "What Fly actually checks: compile-time rules enforced by the released compiler, by the next release and by the seed; the run-time behaviour of unsafe operations; and the known limits of the standard library."
template = "docs-page.html"
weight = 6
+++

# Conformance and safety status

The [Language Reference](@/docs/Fly-Language-Reference.md) describes the
language as designed. This page records **what the released compiler enforces
today**, so you know which guarantees you can rely on and which rules you must
keep yourself.

Everything below was observed by compiling and running a probe program. Three
compilers are compared:

- **0.14.6** — the current release of the self-hosted compiler;
- **Next** — the self-hosted compiler on the `release/0.14.X` branch, which
  closes the gaps listed here and ships with the next release;
- **Seed** — the C++ bootstrap compiler, 0.13.18.

Status values:

- **Enforced** — violating the rule is a compile error.
- **Checked** — violating the rule is detected at run time.
- **Not enforced** — the compiler accepts the program; the behaviour is
  described. Treat these as rules you must follow by hand.
- **Internal error** — the compiler stops in its back end without a source
  position, instead of reporting a diagnostic.

---

## Compile-time rules

| Rule | 0.14.6 | Next | Seed |
|---|---|---|---|
| Every function declares a return type | Enforced | Enforced | Enforced |
| A value function returns on every path | Enforced | Enforced | Enforced |
| The number of returned values matches the declaration | Enforced | Enforced | Enforced |
| An undefined name is an error | Enforced | Enforced | Enforced |
| Argument count and argument types match the callee | Enforced | Enforced | Enforced |
| The returned value's type matches the declared type | Enforced | Enforced | Enforced |
| A class implements every method of its interfaces | Enforced | Enforced | Enforced |
| An unknown command-line option is an error | Enforced | Enforced | Enforced |
| A condition must be `bool` (`if n { }` with `int n`) | **Internal error** | Enforced | Enforced |
| Implicit **narrowing** is rejected (`long`→`int`, `int`→`byte`, `double`→`int`) | **Not enforced** | Enforced | Enforced |
| A `private` field is inaccessible from outside its class | **Not enforced** | Enforced | Enforced |
| A `private` method cannot be called from outside its class | **Not enforced** | Enforced | Not enforced |
| An `abstract` class cannot be instantiated | **Not enforced** | Enforced | Enforced |
| A `final` method cannot be overridden; a `final` class cannot be extended | **Not enforced** | Enforced | Enforced |
| `break` only inside a loop or `switch`; `continue` only inside a loop | **Not enforced** | Enforced | Enforced |
| A variable cannot be declared twice in one scope | **Not enforced** | Enforced | Enforced |
| `string + int` is a type error | **Not enforced** | Enforced | Enforced |
| A `const` local or parameter cannot be assigned, incremented or decremented | **Not enforced** | Enforced | Not enforced |
| A `const` field can be assigned only by a constructor of its class | **Not enforced** | Enforced | Not enforced |
| Two functions of one file cannot share an identical signature | **Not enforced** | Enforced | Not enforced |
| No statement may follow a `return` in the same block | **Internal error** | Enforced | Not enforced |

The released 0.14.6 is **less strict than the seed** on eight rules. The next
release closes all of them and enforces five rules the seed never did.

### The rules in detail

**Narrowing needs a cast.** A number may be stored into a numeric type that can
hold every value of the source type, never a smaller one. Write the cast when
you mean to narrow:

```fly
long big = 5000000000
int n = big          // error from the next release — 0.14.6 stores 705032704
int m = (int)big     // explicit: accepted everywhere
```

A **literal** is exempt when its value fits the target, so ordinary
initializers need no cast:

```fly
byte b = 200         // fits
short s = -5         // fits
uint u = 3144134277  // fits
float f = 1.5        // a float literal may initialize a float
byte c = 300         // error: 300 does not fit a byte
```

Container sizes are `pointer`, which is wider than `int`:

```fly
int n = (int)list.size()
```

Two conversions remain implicit on every compiler: any value may be assigned to
a `bool`, and a negative literal may be assigned to an unsigned type
(`uint u = -1`).

**`const` protects the variable, not the object.** Assigning to a `const`
local or parameter, or applying `++`/`--` to it, is an error from the next
release. Writing a *field* through a `const` reference (`p.x = 1`) is allowed:
`const` does not make the object immutable. A field that is itself declared
`const` is the exception: it is assigned by a constructor of its class and is
read-only everywhere else.

```fly
const int k = 1
k = 2                // error from the next release — 0.14.6 stores 2
```

**`private` is per class.** A `private` field can be read and written, and a
`private` method called, only from the class that declares it. `protected` is
parsed but not yet checked.

**Redeclaration versus shadowing.** Declaring a name twice in the same scope is
an error; declaring it again in a nested block shadows the outer variable and
is allowed.

**Overloads must differ in their parameters.** Two functions of the same file
with the same name and parameter types are rejected. The return type does not
distinguish overloads.

### Known compiler defects

| Program | Result |
|---|---|
| In a build that compiles many suites at once (`fly --suite` over a tree), a diagnostic can name the wrong source file and column. | All versions. Compile the single suite (`--suite=<Name>`) to get the right position. |

### Warnings

The self-hosted compiler emits **no language warnings**: there is no report for
an unused variable or import, a shadowed name or a `switch` fall-through.

Three command-line options are accepted but have no effect yet:
`--debug-symbols` (the self-host back end emits no debug information),
`--ftime-report` and `--mthread-model`. In 0.14.6 they are ignored silently; from
the next release the driver prints a warning saying so, which `-w` suppresses.

---

## Run-time behaviour

| Operation | Behaviour in 0.14.6 |
|---|---|
| Array index out of range | **Checked.** The program stops with `error 2989` and a non-zero exit code. |
| Local variable without initializer | Defined: it holds the zero value of its type. |
| Signed integer overflow | Not checked. Wraps: `2147483647 + 1` is `-2147483648`. |
| Integer division by zero | Not checked. The process terminates abnormally, with no message. |
| Method call on a class reference that is `null` or was never assigned | Not checked. Segmentation fault, with no message. |
| Use of an object after its `free()` | Not checked. |
| A class instance whose `free()` is never called | Leaks. There is no garbage collector. |
| A `string` | Released automatically at scope exit and on reassignment. |
| A `struct` | Stack storage, released at scope exit. |

### Errors that are not handled

A `fail` in a called function does **not** interrupt the caller. The call
returns, the caller continues with the next statement, and the value it received
is the zero value of the return type.

```fly
int load() {
    fail 7, "bad"
    return 1
}

void main() {
    int v = load()            // v is 0; execution continues
    io.printLn("continued")   // printed
}                             // the process then exits with code 7
```

```text
continued
error 7: bad
```

An unhandled error is reported once, when `main` returns, and becomes the exit
code. Nothing forces a caller to look at it earlier. When the result of a call
matters, wrap the call in `handle { }` and test `error` before using the value.

### `switch` falls through

A `case` body that does not end in `break` continues into the next label,
including `default`:

```fly
switch a {
    case 1: io.printLn("one")
    default: io.printLn("d")
}
// with a == 1 this prints "one" and then "d"
```

### Operator precedence

Precedence differs from C in two places, and neither is diagnosed:

- Bitwise (`& | ^`) and shift (`<< >>`) operators bind **looser** than
  comparisons: `a & b == c` means `a & (b == c)`.
- `&&` and `||` share **one** level and associate left to right.

Parenthesize every expression that mixes these operators.

### String literals

Escape sequences in a string literal are **not translated**: `"\n"` is the two
characters `\` and `n`. Use `io.printLn` or `io.writeNL` to emit a line break.

---

## The standard library

The library reports failure in several ways. Until it is unified on `fail`, read
each function's description:

| Convention | Used by |
|---|---|
| Sentinel result (`fd < 0`, `0`, `""`, empty buffer) | `fly.os.fs`, `fly.os.io`, `fly.net`, `fly.net.tls`, `fly.os.env` |
| `bool` result | `fly.net` options, `fly.net.http` downloads, `fly.targz` extraction |
| `int errOut` parameter | `fly.codec`, `fly.json`, `fly.targz` |
| Zero value of the element type | `fly.data` containers, for a missing key or an out-of-range index |
| `fail` | `fly.assert`, and a few checks in `fly.hash` and `fly.net` |

Consequences worth keeping in mind:

- `fs.read` returns an empty buffer both for an empty file and for a missing
  one. Call `fs.exists` first when the difference matters.
- `List.get`, `Map.get` and the `pop`/`peek` methods return `0`, `""` or `null`
  when there is nothing to return. Use `size()`, `isEmpty()` or `has()`.
- `fly.data.Tree` is an **unbalanced** binary search tree: inserting keys in
  sorted order degrades every operation to linear time.
- `Map` and `Set` hash strings with unseeded 32-bit FNV-1a and integers by
  value. Do not key them on attacker-controlled input in a server.
- `json.parse` recurses once per nesting level without a depth limit. Bound the
  size of untrusted input before parsing it.
- `fly.math` random functions are a fast statistical generator
  (xoshiro256**), **not** a cryptographic one. The library has no CSPRNG.
- `fly.net` addresses are IPv4 only.
- `fly.sync` threads are detached; there is no `join`. Containers are not
  thread-safe.
- `fly.net.tls` verifies certificates and host names **by default**; only
  `tlsConnectOpts` / `tlsWrap` with `verify = false` turn that off.
- `fly.targz` rejects absolute paths and `..` components when extracting.

---

## Platforms

| Target | Status |
|---|---|
| Linux x86_64 | built and tested in CI |
| Windows x64 (UCRT, no Visual Studio needed) | built and tested in CI |
| macOS | runtime sources in the tree; not built or tested in CI |
| aarch64 | not built |

---

## Reporting a difference

If the compiler accepts or rejects something in a way neither this page nor the
Language Reference describes, that is a bug in one of the three. Please report
it at <https://github.com/fly-lang/fly/issues> with the smallest program that
shows it and the output of `fly --version`.
