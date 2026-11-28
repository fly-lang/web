+++
title = "Conformance"
description = "What Fly actually checks: compile-time rules enforced by the released 0.14.6, by 0.14.7 and by the seed; the run-time behaviour of unsafe operations; and the known limits of the standard library."
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
- **0.14.7** — the next release of the self-hosted compiler (branch `release/0.14.X`), which
  closes the gaps listed here;
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

| Rule | 0.14.6 | 0.14.7 | Seed |
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
| A `const` field can be assigned only by a constructor of its class (from 0.16.0 also static, bare-name, `super.v`, `delete` and multi-assignment writes; see below) | **Not enforced** | Enforced | Not enforced |
| Two functions of one file cannot share an identical signature | **Not enforced** | Enforced | Not enforced |
| No statement may follow a `return` in the same block | **Internal error** | Enforced | Not enforced |

The released 0.14.6 is **less strict than the seed** on eight rules. 0.14.7
closes all of them and enforces five rules the seed never did.

### The rules in detail

**Narrowing needs a cast.** A number may be stored into a numeric type that can
hold every value of the source type, never a smaller one. Write the cast when
you mean to narrow:

```fly
long big = 5000000000
int n = big          // error from 0.14.7 — 0.14.6 stores 705032704
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

From 0.15.0 the same rule covers every **argument** of a call (`take(aLong)` for
an `int` parameter is an error), a **signed value into an unsigned type** needs
a cast at any width (`uint u = anInt`, `ulong n = anInt`, and `uint u = -1`),
and comparing a signed with an unsigned integer is an error unless the signed
type holds every unsigned value (see the Language Reference §3.1.4). Up to 0.14.x
an argument narrowed silently and a negative value became a huge unsigned one.

One conversion remains implicit on every compiler: any value may be assigned to
a `bool`.

**`const` protects the variable, not the object.** Assigning to a `const`
local or parameter, or applying `++`/`--` to it, is an error from the next
release. Writing a *field* through a `const` reference (`p.x = 1`) is allowed:
`const` does not make the object immutable. A field that is itself declared
`const` is the exception: it is assigned by a constructor of its class and is
read-only everywhere else. From 0.16.0 that rule covers every way of writing the
field. A `static const` field is set by its initializer alone: `K.A = 2`,
`K.A += 1`, `A++` inside the class and a write in a constructor are errors. An
instance `const` field is set once, by a constructor of the class that declares
it, on its own object (`this.v = …` or the bare `v = …`), and only when the
declaration has no initializer. Writing it on another object (`o.v = …`), from a
subclass constructor (`super.v = …`), over an initializer, or with `delete` is an
error. Up to 0.15.x only `obj.v = …` outside a constructor was rejected.
From 0.16.0 a multi-assignment follows the same rule for each receiver:
`a, k = two()` with `k` a `const` local, parameter or field is the same error,
and a constructor may still set its own unset `const` field this way. Up to
0.15.x the receivers of a multi-assignment were not checked.

```fly
const int k = 1
k = 2                // error from 0.14.7 — 0.14.6 stores 2
```

**Field initializers are checked, from 0.16.0.** An initializer is checked like
the initializer of a declaration (`int f = "abc"` and `int f = aLong` are
errors), and a `static` field's must be a constant: literals, enum entries,
`static const` fields and the operators and casts over them
(`static const int B = A * 2 + 1`). `static int n = compute()` is an error, and
so is a constant that overflows its type or divides by zero. Up to 0.15.x (the
v0.15.2 seed included) an initializer was resolved without these checks and
never emitted: the field started at its zero value.

**`private` is per class.** A `private` field can be read and written, and a
`private` method called, only from the class that declares it.

**`protected` is per class hierarchy, from 0.16.0.** A `protected` field or
method (constructors included) is reachable from the class that declares it and
from every class derived from it, through any receiver; anywhere else it is an
error (`'v' is protected in 'Base': it can be accessed only from 'Base' and the
classes derived from it`). `protected` may be written only on the methods and
attributes of a class: on a function, a variable, a type or a member of an
interface, struct or suite it is an error. From 0.16.0 both `private` and
`protected` are also checked on **static** members (`A.count`, `A.make()`), on
`super.v`, and on `new` with a non-public constructor. Up to 0.15.x `protected`
was parsed and ignored (every access compiled), and a `private` static member
could be reached from outside its class.

**`this` is only a receiver in a constructor, from 0.16.0.** In a constructor
and in an instance field initializer `this` may read or set a field and call a
method (`this.x`, `this.x = v`, `this.m()`); assigning it, passing it as an
argument, putting it in an array value or comparing it is an error (`'this' can
only be a receiver (this.x, this.m()) in a constructor or a field initializer`):
a constructor that fails releases its object, and a copy of `this` kept
elsewhere would point at released memory. Not covered: a method the constructor
calls can still store `this`. Up to 0.15.x (the v0.15.2 seed included) `this`
was an ordinary value there.

**The files of one namespace see each other without an import, from 0.16.0.**
A file declaring `namespace N` uses the classes, functions and enums of the
other files of `N` by their bare names; the compiler reads those files from
the project's sources because `N` is the file's own namespace. Up to 0.15.x
(the v0.15.2 seed included) they were read only when something imported `N`:
a file of `N` that used a sibling's class without `import N.C` failed with
`unknown type 'C'`. Code the seed compiles keeps its imports.

**Redeclaration versus shadowing.** Declaring a name twice in the same scope is
an error; declaring it again in a nested block shadows the outer variable and
is allowed.

**Overloads must differ in their parameters.** Two functions of the same file
with the same name and parameter types are rejected. The return type does not
distinguish overloads.

### Known compiler defects

| Program | Result |
|---|---|
| A build of more than one source file (`fly --suite` over a tree, `--lib`, a module pulled in by an import) | **0.14.6:** a semantic error outside the first input file is reported with the name, line and column of the first file. Compile the single suite (`--suite=<Name>`) to get the right position. **Fixed in 0.14.7.** |

### Warnings

From 0.15.0 the self-hosted compiler reports unused variables and imports,
shadowed names, integer-to-float conversions that may round, and a `switch` over
an enum that misses values (see the CLI reference). Up to 0.14.x it emitted no
language warning.

Two command-line options are accepted but have no effect yet: `--ftime-report`
and `--mthread-model`. In 0.14.6 they are ignored silently; from 0.14.7 the
driver prints a warning saying so, which `-w` suppresses. `--debug-symbols` was
the third until 0.14.8, which emits DWARF (line tables, parameters and locals; not yet the fields of a
class).

---

## Run-time behaviour

| Operation | Behaviour |
|---|---|
| Array index out of range | **Checked.** A failure with code 2989, which `handle` can intercept; uncaught, the program stops with `error 2989: index out of range at main.fly:4:5` (the message and position from 0.16; before, `error 2989` alone) and exit code 2989 (173 as a POSIX shell sees it). |
| Access through a `pointer` that points nowhere (`fly.mem` reads and writes) | Not checked: `pointer` is the unchecked part of the language. **From 0.16** the program prints `fatal error: invalid memory access at address 0x…` before it ends; the exit code stays the system's (`0xC0000005` on Windows, 139 for SIGSEGV on Linux and macOS), and a debugger, a core file or a crash dump see the fault as before. Up to 0.15.x: no message. |
| Stack overflow (recursion without end) | Not checked. **From 0.16** the program prints `fatal error: stack overflow` before it ends, with the system's exit code (`0xC00000FD` on Windows, 139 on Linux and macOS). The main thread only: on Linux and macOS a fault within 64 MB below `main`'s frame is reported as a stack overflow, any other one as an invalid memory access. Up to 0.15.x: no message. |
| Local variable without initializer | Defined: it holds the zero value of its type. A struct then takes the initializers of its fields (from 0.16.0). |
| A field declared with an initializer (`int f = 6`, `static int count = 11`) | **From 0.16.0:** an instance field's initializer runs when the object is created, after the zeroing and before any constructor, inherited fields first, whether or not the constructor calls `super(...)`; a failing initializer fails the `new`. A static field starts at its constant initializer. Up to 0.15.x the initializer was ignored and the field started at its zero value. |
| Integer overflow of `+`, `-`, `*`, `++`, `--`, unary `-` (signed and unsigned) | **Checked from 0.15.0, in every build:** `error 134: integer overflow in '+' at main.fly:2:5`. Wrapping is explicit: `math.wrapAdd` and its siblings. Not checked: `pointer` arithmetic, shifts, bitwise operators, casts. Up to 0.14.x: wraps silently. |
| Integer division or remainder by zero (`/`, `%`, `/=`, `%=`) | **Checked from 0.15.0, in every build:** the program stops with `error 134: division by zero at main.fly:2:5` and exit code 134. Up to 0.14.x: abnormal termination, with no message. |
| Signed minimum value divided by `-1` (`-2147483648 / -1`) | **Checked from 0.15.0:** `error 134: integer overflow: the minimum value divided by -1 at …`. `%` by `-1` is `0`. |
| Floating-point division by zero | IEEE 754: infinity or NaN, no trap. |
| Method call or field access on a class or interface reference that is `null` or was never assigned | **Checked from 0.15.0, in every build** (release included): the program stops with `error 134: null reference: calling 'size' on a null 'Box' at main.fly:16:5` (or `accessing field 'v' of …`) and exit code 134. Calls through `this` are not checked: the caller already did. Up to 0.14.x: segmentation fault, with no message. |
| Use of an object after `delete x` | **Checked from 0.15.0** through the same variable: `delete` sets it to `null`, so a later use stops on the null-reference trap. Another variable that still points at the object is not checked. |
| Use of an object after calling its `free()` directly | Not checked. |
| A class instance created with a plain `new` that is never deleted | From 0.16.0 leaving the scope of its variable releases nothing (see the Language Reference §6.6), and **at the end of `main` the object is reported** on stderr (`warning: N objects not deleted (B B)` and one line per class) **and released** — its `free()` runs, an object before what it owns, a `new shared` cycle once. The exit code does not change; a run-time trap or a crash skips the report. In 0.15.x a local created with `new` that had not escaped was released at the end of its scope, and the rest leaked silently. Objects created with `new unique` or `new shared` are released by their owner or their last holder. There is no garbage collector. |
| A second `delete` of an object through another reference (`b = a`, `delete a`, `delete b`) | **Checked from 0.16.0:** `error 134: double delete of a 'Node' at main.fly:4:5`, exit code 134. Up to 0.15.x: undefined (a double free). |
| The object of a `fail` consumed by `handle` | Deleted from 0.16.0 (the error owns it). Up to 0.15.x: dropped from the error, never released. |
| A `free()` that fails | **An ordinary failure from 0.16.0**: after `delete` (the object still released, the variable `null`), at the end of a `new unique` scope, with the last `new shared` holder, in the release at the end of `main` (it sets the exit code when `main` ended cleanly). One that fails while another failure is in flight does not replace it: the first stays the failure and the later one is **attached** to it — `main` prints it as `  also: error 43: free`, a `handle` consumes it with the first. Up to 0.15.x: not checked after `delete` (it surfaced at a later call or at the end of `main`), dropped in a cleanup or at the end of `main`, and at the end of a `new unique` scope it overwrote the failure in flight. |
| Locals of a `handle` block or a suite `case` (and of the blocks in them) left by a failure | **Released from 0.16.0**, as a `return` releases them: `new unique` owners deleted, `new shared` holders let go, strings and arrays freed, innermost scope first. Up to 0.15.x: left behind (objects reported at the end of `main`, strings and arrays leaked). |
| A `fail` (or an index out of range) written directly in a suite `case` | **Ends the case from 0.16.0**, like a failing call: reported on the case's own line, the next case runs. Up to 0.15.x (and in the v0.15.2 seed): it returned from the test method, reported as `body ... FAIL(...)`, and the method's later cases were skipped. |
| Two files with no `namespace` declaring a class, a function or an enum of the same name | **Kept apart from 0.16.0**: each file is a namespace of its own, and its symbols carry its base name. Up to 0.15.x (and in the v0.15.2 seed) the symbols carried an empty namespace: compiled together, the two classes shared their initializer and release helpers (an object released as the other class, or a crash) and the two enums one name table; compiled apart, their symbols could clash at link time. Two files with the same base name in different directories still share it. |
| Two functions with the same name and parameter types, or two types (class, struct, interface, enum) with the same name, in one namespace | **An error from 0.16.0**, in one file or across files, with a note at the first definition. Up to 0.15.x (and in the v0.15.2 seed) only two functions in one file were reported: across files the program compiled and the link order chose the body, and a type defined twice was accepted. |
| Two functions whose parameters are classes or enums with the same short name from two namespaces (`h(probe.left.P)`, `h(probe.right.P)`), or specializations over them (`Box<probe.left.P>`, `Box<probe.right.P>`) | **Two overloads from 0.16.0**: a class or an enum type is identified by its namespace and its name, a specialization by its type arguments, and each call picks its own. Up to 0.15.x (and in the v0.15.2 seed) types were compared by their short name: the second function was reported as already defined. |
| A class value assigned where an unrelated class, or a subclass of its own, is expected (`Dog d = new Cat()`), or an enum value where another enum is | **An error from 0.16.0**, in a declaration, an assignment, a `return`, a field initializer and a struct literal: a class value goes where its class, a base or an interface of it is expected. Up to 0.15.x (and in the v0.15.2 seed) only the kind (class, number, string, enum) was checked, and the program read the object as the wrong class. |
| A type written with the prefix of an imported namespace of more than one segment (`left.P` after `import probe.left`), a dotted `new` (`new left.P()`, `new probe.left.P()`), and a dotted type with an unknown prefix (`zzz.P`) | **Resolved from 0.16.0**: the prefix expands through the imports, as for a call; an unknown prefix is an unknown type. Up to 0.15.x (and in the v0.15.2 seed) the first two were errors (`unknown type`, a parse error) and `zzz.P` silently meant any `P` in scope. |
| A local array whose element type is a dotted name (`probe.left.P[2] all`, `left.P[] some = {}`), and an enum value or a static member written with a namespace prefix (`probe.left.Mode.ON`, `left.Mode.ON`, `left.P.count`) | **Resolved from 0.16.0**: the array is a declaration (the name after the brackets tells it from an index into a member), and the prefix reaches the values of a type as it reaches the type. Up to 0.15.x (and in the v0.15.2 seed) the declaration was read as an expression or did not parse, and the value was an error (`'probe' is a namespace, not a value`): the enum or the class had to be imported by name. |
| Order of the entries `fs.dirRead` returns, and with it the order in which `fly --suite` and `fly test` run the suites of a directory | **Sorted by name from 0.16.0** (byte by byte), the same on every machine. Up to 0.15.x (and in the v0.15.2 seed) the order was the operating system's: two suites of one directory could run, and print, in either order. |
| A struct written into an array: as an element of an array literal (`Cell[] cells = {c}`), by assignment to an element (`cells[0] = c`), or read as the item of a `for … in` | **Copied from 0.16.0**: the fields go into the element, as an assignment between two struct variables copies them, and the item of the loop is a copy of the element. Up to 0.15.x (and in the v0.15.2 seed) the element kept the address of the value instead of its fields and read back as something else, and a `for … in` over an array of structs could crash; neither was reported. |
| A sized array of more than one dimension (`int[2][3] g`, `Cell[2][2] grid`) | **Built whole from 0.16.0**: 2 rows of 3, the dimensions in the order of the subscripts, every row built as a sized array of one dimension is; an element that fails to build releases the rows already built. Up to 0.15.x (and in the v0.15.2 seed) only the outer array was built and its sizes were read in the opposite order: every subscript failed with `index out of range`. |
| Two specializations of one generic class (`Box<int>`, `Box<string>`) | **Released each as itself from 0.16.0.** Up to 0.15.x (the v0.15.2 seed included) they shared the release helper of the first one built: the other was released with its fields, and with an owning field the program could crash. |
| Order in which one scope releases its locals | **Last declared first from 0.16.0**, on every exit: an object whose `free()` uses an earlier local's object finds it alive. Up to 0.15.x: declaration order, so such a `free()` read a released object (no diagnostic). |
| An object deleted, or released by its owner or last holder, through a base class or interface reference | Defined **from 0.16.0**: destroyed as the class it was created with — that class's `free()` and every owning, holding and weak field it has, bases' fields included. In 0.15.x only the fields of the reference's type were released: those of the subclass leaked, and a `free()` declared only by the subclass did not run. |
| A `string` | Released automatically at scope exit and on reassignment. |
| A `struct` | Stack storage, released at scope exit. |

### Errors that are not handled

**From 0.15.1 a failure cannot be ignored.** When a called function fails, the
caller stops right after the call and returns too, and so on up the stack, until
a `handle` stops the error; an error that nobody handles ends the program from
`main`, which prints it and exits with its code.

```fly
int load() {
    fail 7, "bad"
    return 1
}

void main() {
    int v = load()            // load failed: main stops here
    io.printLn("continued")   // NOT printed
}                             // the process exits with code 7
```

```text
error 7: bad
```

To go on after a failure on purpose, put the call in `handle { }` and test
`error`. Inside a `handle` the rest of the block is skipped too, from the
failing call on.

Up to 0.15.0 the call returned, the caller continued with the next statement,
and the value it received was the zero value of the return type: the program
above printed `continued` before reporting `error 7: bad`.

The report names the failure that ended the program. From 0.16 a message built
by an expression (`fail 1, str.concat(what, ": x")`) is reported, and a `handle`
consumes the message along with the code; in 0.15.1 and 0.15.2 such a message was
lost (`error 1`); and up to 0.15.2 a message consumed by an earlier `handle`
reappeared in a later failure that had none of its own.

### `switch` and fall-through

From 0.15.0 a `case` body never continues into the next label: the switch ends
with the body. Several values share a body by being listed, `case 1, 2:`, and a
case with no statements is a compile-time error. Up to 0.14.x a body that did
not end in `break` continued into the next label, `default` included:

```fly
switch a {
    case 1: io.printLn("one")
    default: io.printLn("d")
}
// with a == 1: "one" from 0.15.0; "one" and then "d" up to 0.14.x
```

### Operator precedence

Precedence differs from C in two places:

- Bitwise (`& | ^`) and shift (`<< >>`) operators bind **looser** than
  comparisons: `a & b == c` means `a & (b == c)`.
- `&&` and `||` share **one** level, with the bitwise and shift operators, and
  associate left to right.

From 0.15.0 both are **diagnosed**: two different operators of that level
without parentheses (`a || b && c`), or a comparison as the operand of a bitwise
or shift operator (`flags & mask == 0`), is a compile-time error. Up to 0.14.x
neither was, and the expression silently took the Fly reading.

### String literals

From 0.15.0 an ordinary string literal decodes escape sequences (`\n`, `\t`,
`\\`, `\xHH`, `\u{H…}`; an unknown one is a compile-time error), and `"""…"""`
is a raw literal that keeps backslashes and newlines as written (Language
Reference §2.3.4). Up to 0.14.x escapes were **not translated**: `"\n"` was the
two characters `\` and `n`.

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
| macOS | not supported for now: runtime sources are in the tree, but nothing builds or tests them |
| aarch64 | not built |

---

## Reporting a difference

If the compiler accepts or rejects something in a way neither this page nor the
Language Reference describes, that is a bug in one of the three. Please report
it at <https://github.com/fly-lang/fly/issues> with the smallest program that
shows it and the output of `fly --version`.
