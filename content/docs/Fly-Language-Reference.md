+++
title = "Language Reference"
description = "Complete reference for the Fly programming language — syntax, types, expressions, statements, and grammar."
template = "docs-page.html"
weight = 1
+++

# Fly Language Reference

**Version:** 0.14.9  
**Project:** [Fly Programming Language](https://flylang.org)  
**License:** Apache License v2.0

---

## Table of Contents

1. [Introduction](#1-introduction)
2. [Lexical Elements](#2-lexical-elements)
3. [Types](#3-types)
4. [Variables](#4-variables)
5. [Functions](#5-functions)
6. [Classes and Structures](#6-classes-and-structures)
   - [6.6 Allocation and Lifetime](#6-6-allocation-and-lifetime)
   - [6.7 Generics](#6-7-generics)
7. [Enumerations](#7-enumerations)
8. [Expressions](#8-expressions)
   - [8.1.3 Array Subscript](#8-1-3-array-subscript)
9. [Statements](#9-statements)
   - [9.5.3 For-In Loop](#9-5-3-for-in-loop)
   - [9.8 Testing Constructs](#9-8-testing-constructs)
10. [Namespaces and Imports](#10-namespaces-and-imports)
11. [Modifiers](#11-modifiers)
12. [Comments](#12-comments)
13. [Grammar Summary](#13-grammar-summary)

---

## 1. Introduction

Fly is a compiled, high-level, general-purpose programming language with particular attention to simplicity, readability, and multi-paradigm support. Fly is built on LLVM infrastructure. It has no garbage collector: structs live on the stack, strings are released automatically, and class instances are released explicitly (see [6.6](#6-6-allocation-and-lifetime)).

This reference describes the language as designed. Where the released compiler does not yet enforce a rule stated here, the difference is recorded in [Conformance](@/docs/Fly-Conformance.md), which is the page to consult when a guarantee matters.

**Design Principles:**
- **Simple** - Easy to read and write
- **Fast** - Compiled with LLVM for optimal performance
- **Powerful** - Multi-paradigm with modern features

---

## 2. Lexical Elements

### 2.1 Keywords

Fly reserves the following keywords:

```
abstract    as          bool        break       byte
case        class       complex     const       continue
default     delete      double      else        elsif
enum        error       fail        false       final
float       for         handle      if          import
in          int         interface   long        namespace
new         null        pointer     private     protected
public      return      short       static      string
struct      suite       switch      test        true
uint        ulong       ushort      void        while
```

`complex` is reserved but has no meaning yet: it cannot be used as a name, and the
compiler accepts it nowhere else. `unset`, reserved up to v0.15.2, is not a keyword
from 0.16.0: a variable without an initializer holds the zero value of its type (see
[Section 4.2](#4-2-variable-initialization)), an enum its first constant, a struct the
initializers of its fields ([Section 6.2](#6-2-structure-declaration)).

> **`return`** carries the value a function hands back (`return expr`, or `return a, b` when several return types are declared). The value travels through a hidden output parameter the compiler adds to the signature, never through a copy out of the callee's frame. See [Section 5.4](#5-4-return-values).

> **`this`** is *not* a reserved keyword either — it is a special identifier available inside instance methods that refers to the current object (e.g. `this.value`). See [Section 6.4](#6-4-class-members).

> **`delete`** is a keyword from 0.15.0: the statement `delete x` releases an object created with `new` (see [Section 6.6](#6-6-allocation-and-lifetime)). From 0.15.0 **no reserved word can be used as a declared name** (variable, parameter, field, method, function, class): `int delete = 3` and `int while = 3` are errors. `fs.delete` and `env.delete` became `fs.del` and `env.del`. Up to 0.14.x `delete` was an ordinary identifier.

### 2.2 Identifiers

Identifiers must start with a letter or underscore, followed by any combination of letters, digits, or underscores.

**Syntax:**
```
Identifier ::= [a-zA-Z_][a-zA-Z0-9_]*
```

**Examples:**
```fly
myVariable
_privateVar
counter123
MyClass
getValue
```

### 2.3 Literals

#### 2.3.1 Numeric Literals

```fly
42          // integer literal
0           // zero
3.14        // floating-point literal
0.0         // floating-point zero
```

#### 2.3.2 Boolean Literals

```fly
true        // boolean true
false       // boolean false
```

#### 2.3.3 Characters

Fly has no character type and no character literal (from 0.15.0 `char` is not
even a keyword): a character is a **string of one character**, written `"a"`.
The functions of `fly.str` that work on characters count Unicode code points,
not bytes: `charAt`, `charCount`, `chars`, `charCode` and `fromCharCode`.

```fly
string first = str.charAt("héllo", 1)      // "é" (2 bytes, 1 character)
int n = str.charCount("héllo")             // 5
int code = str.charCode("é", 0)            // 233
string e = str.fromCharCode(233)           // "é"
```

#### 2.3.4 String Literals

```fly
"Hello, World!"
""                    // empty string
"tab\there\n"         // escape sequences (from 0.15.0)
"caf\u{E9} \x41"      // a code point as UTF-8, a byte
"""C:\Users\fly"""    // raw: backslashes stay as written
"""first line
second line"""        // raw literals may span lines
```

From 0.15.0 a backslash in an ordinary string literal starts an **escape
sequence**:

| Escape | Byte(s) |
|---|---|
| `\n` `\t` `\r` `\0` | newline (10), tab (9), carriage return (13), NUL (0) |
| `\` `\"` `\'` | backslash, double quote, apostrophe |
| `\xHH` | the byte with hex value `HH` (exactly two hex digits) |
| `\u{H…}` | the code point `H…` (1 to 6 hex digits, up to `10FFFF`, not a surrogate), written as UTF-8 |

Any other sequence (`\q`, `\x4`, `\u41`) is a compile-time error. A **raw
literal** between three double quotes, `"""…"""`, has no escapes: everything up
to the closing `"""` is its text, backslashes, quotes and newlines included.
`fly fmt` leaves the lines of a raw literal untouched.

Strings are sequences of **bytes** (`str.len`, `str.byteAt`, `str.substring`
count bytes); text is UTF-8, read by character (code point) with `str.charAt`,
`str.charCount`, `str.chars`, `str.charCode` and `str.fromCharCode` (see §2.3.3
and the API reference).

> Up to 0.14.x every literal was raw: `"\n"` was the two characters `\` and
> `n`, and a line break had to be written with `io.printLn` or a byte.

#### 2.3.5 Null Literal

```fly
null        // null value for reference types
```

### 2.4 Operators and Punctuators

#### Arithmetic Operators
```fly
+           // addition
-           // subtraction
*           // multiplication
/           // division
%           // modulo
++          // increment
--          // decrement
```

#### Compound Assignment Operators
```fly
+=          // add and assign
-=          // subtract and assign
*=          // multiply and assign
/=          // divide and assign
%=          // modulo and assign
```

#### Comparison Operators
```fly
==          // equal to
!=          // not equal to
<           // less than
>           // greater than
<=          // less than or equal
>=          // greater than or equal
```

#### Logical Operators
```fly
&&          // logical AND
||          // logical OR
!           // logical NOT
```

#### Bitwise Operators
```fly
&           // bitwise AND
|           // bitwise OR
^           // bitwise XOR
<<          // left shift
>>          // right shift
&=          // bitwise AND and assign
|=          // bitwise OR and assign
^=          // bitwise XOR and assign
<<=         // left shift and assign
>>=         // right shift and assign
```

#### Other Operators and Punctuators
```fly
=           // assignment
?:          // ternary conditional
.           // member access
[]          // array subscript (bounds-checked — see 8.1.3)
()          // function call / grouping
{}          // block delimiters
,           // separator
;           // statement separator (used between the clauses of a for loop)
:           // label / case / base-type / type-bound separator
```

---

## 3. Types

### 3.1 Built-in Types

#### 3.1.1 Integer Types

| Type     | Size    | Range                                    | Description              |
|----------|---------|------------------------------------------|--------------------------|
| `byte`   | 8-bit   | 0 to 255                                 | Unsigned byte            |
| `short`  | 16-bit  | -32,768 to 32,767                        | Signed short integer     |
| `ushort` | 16-bit  | 0 to 65,535                              | Unsigned short integer   |
| `int`    | 32-bit  | -2,147,483,648 to 2,147,483,647          | Signed integer           |
| `uint`   | 32-bit  | 0 to 4,294,967,295                       | Unsigned integer         |
| `long`   | 64-bit  | -9,223,372,036,854,775,808 to ...        | Signed long integer      |
| `ulong`  | 64-bit  | 0 to 18,446,744,073,709,551,615          | Unsigned long integer    |

**Examples:**
```fly
byte age = 25
short temperature = -10
ushort port = 8080
int count = 1000
uint id = 12345
long bigNum = 9999999999
ulong hugeNum = 18446744073709551615
```

#### 3.1.2 Floating-Point Types

| Type     | Size    | Description                    |
|----------|---------|--------------------------------|
| `float`  | 32-bit  | Single-precision float         |
| `double` | 64-bit  | Double-precision float         |

**Examples:**
```fly
float pi = 3.14
double precise = 3.14159265359
```

#### 3.1.3 Other Built-in Types

| Type     | Description                                          |
|----------|------------------------------------------------------|
| `bool`   | Boolean type (true or false)                         |
| `string` | String type (heap-managed, see [§6.6](#6-6-allocation-and-lifetime)) |
| `error`  | Error type for error handling                        |
| `void`   | Absence of a value — used only as a function return type |

**Examples:**
```fly
bool isActive = true
string name = "Fly"
```

> `void` is written as the return type of functions that do not produce a value: `void main() { … }`. A return type is **mandatory** on every function and method (see [Section 5.1](#5-1-function-declaration)).

#### 3.1.4 Numeric Conversions

A number converts **implicitly** only where no value can be lost: into a type
that holds every value of the source type. Everything else needs an explicit
cast, `(T)expr`, which truncates (integers) or rounds toward zero (float to int).

| Conversion | Implicit? |
|---|---|
| widening: `int → long`, `int → double`, `float → double`, `byte → int` | yes |
| unsigned into a **wider** signed type: `uint → long`, `byte → short` | yes |
| narrowing: `long → int`, `int → byte`, `double → float`, `double → int` | no — cast |
| **signed into unsigned**, any width: `int → uint`, `int → ulong`, `long → ulong` (a negative value has no unsigned counterpart) | no — cast (from 0.15.0) |
| any integer into `pointer` (address arithmetic: a negative offset wraps) | yes |

The rule is the same wherever a value is stored: a declaration, an assignment,
a field, a `return`, and — from 0.15.0 — every **argument** of a call, which is
stored into its parameter. A variable passed to a non-`const` (out) parameter is
checked the other way round, since the callee writes the parameter back into it.

A **literal** needs no cast when its value fits the target: `byte b = 200`,
`uint u = 5`, `take(4096)` for a `ulong` parameter. `uint u = -1` is an error.

**Comparing signed with unsigned.** `int` and `uint` are compared in `uint`,
where `-1` reads as `4294967295`; from 0.15.0 such a comparison is an error
that asks for a cast. It stays legal when the signed type holds every value of
the unsigned one (`aLong < aUint`, `anInt > aByte`), or when the signed side is
a non-negative literal (`u > 0`).

```fly
long big = 5000000000
int n = big            // error: may not fit
int m = (int)big       // explicit
int k = -1
ulong size = k         // error: a negative int has no ulong value
ulong ok = (ulong)len  // explicit, once len is known to be >= 0
if k < aUint { }       // error: compared in uint
if k < (int)aUint { }  // explicit
```
### 3.2 Array Types

Arrays can be declared with or without explicit size.

**Syntax:**
```
ArrayType ::= Type '[' [ Expression ] ']'
```

**Examples:**
```fly
// Dynamic array: the initializer gives the size
byte[] dynamicArray = {}
int[] numbers = {1, 2, 3}

// Fixed-size array
byte[10] fixedBuffer
int[5] coordinates

// Multi-dimensional arrays
int[][] matrix = {}
byte[][][] cube = {}
```

A declaration must give either a size or an initializer — `int[] xs` alone is an error, because there is nothing to say how large the array is.

Elements are read and written with the subscript operator, which is bounds-checked (see [8.1.3 Array Subscript](#8-1-3-array-subscript)), and iterated with `for in` (see [9.5.3](#9-5-3-for-in-loop)).

Arrays are heap-backed, **reference counted**, and have **reference semantics** — assigning one array to another shares the buffer instead of copying it. Their lifetime, and what an array does and does not own, is specified in [6.6 Allocation and Lifetime](#6-6-allocation-and-lifetime).

### 3.3 Named Types

User-defined types include classes, structures, and enumerations.

**Examples:**
```fly
MyClass obj
Point location
Status currentStatus
```

### 3.4 Qualified Type Names

Types can be qualified with namespace prefixes.

**Examples:**
```fly
// Using dotted notation
utils.Helper helper
mylib.DataType data
```

---

## 4. Variables

### 4.1 Local Variables

Local variables are declared within functions or blocks.

**Syntax:**
```
LocalVar ::= [ Modifiers ] Type Identifier [ '=' Expression ]
```

**Examples:**
```fly
void func() {
    // Simple declaration
    int x = 10
    
    // Without initialization
    bool flag
    
    // Constant local variable
    const int limit = 100
}
```

### 4.2 Variable Initialization

#### 4.2.1 Basic Types

```fly
bool flag = true
int count = 42
float value = 3.14
string message = "Hello"
```

#### 4.2.2 Null Initialization

```fly
MyClass obj = null
Type instance = null
```

#### 4.2.3 Array Initialization

```fly
// Empty array
byte[] empty = {}

// Array with values
byte[] values = {1, 2, 3, 4, 5}
int[] numbers = {10, 20, 30}

// Fixed-size array
byte[3] buffer = {1, 2, 3}
```

#### 4.2.4 Struct Literal Initialization

A brace literal that contains `field = value` pairs builds a **struct value**. Plain comma-separated values (without `=`) build an array value (see above); the two forms are distinguished by the presence of `field =`.

A field the literal does not give takes its initializer (`int x = 4` in the struct, see [Section 6.2](#6-2-structure-declaration)), or its zero value.

```fly
// Struct value — field = value pairs
Point p = {x = 10, y = 20}

// Array value — bare values
int[] xs = {10, 20, 30}
```

---

## 5. Functions

### 5.1 Function Declaration

Every function and method **must** declare a **return type** before the function name — this is mandatory. A function that produces no value declares `void`. When a non-`void` return type is present, the body must hand a value back with `return expr` on every path; the compiler adds a hidden output parameter to the signature and the return writes through it.

> **Important:** A missing return type is a compile error (`err_parser_missing_return_type`). Write `void doSomething() { … }`, not `doSomething() { … }`. The only exceptions are **constructors** (a method named exactly like its class — see [§6.4](#6-4-class-members)) and **interface method declarations**, which omit the return type.

**Syntax:**
```
Function    ::= [ Modifiers ] ReturnType Identifier
                [ '<' TypeParam ( ',' TypeParam )* '>' ]
                '(' [ Parameters ] ')' ( Block | ';' )
ReturnType  ::= Type ( ',' Type )*
```

**Examples:**
```fly
// Void function — declares 'void' explicitly
void doSomething() {
    // function body
}

// Function with a return type — `return` carries the value
int add(const int a, const int b) {
    return a + b
}

// Multiple return types — one return, values in declaration order
int,int minMax(const int a, const int b) {
    if a < b {
        return a, b
    } else {
        return b, a
    }
}
```

### 5.2 Function Parameters

The `const` modifier on a parameter is **optional**. A `const` parameter is read-only inside the body (an input); a non-`const` parameter may be written and is the mechanism used for output parameters (including the hidden parameter generated for return values). A parameter may also declare a **default value** with `= <literal>`.

**Syntax:**
```
Parameters ::= Parameter ( ',' Parameter )*
Parameter  ::= [ Modifiers ] Type Identifier [ '=' Value ]
```

**Examples:**
```fly
// const inputs (read-only)
void process(const int x, const float y, const bool flag) {
    // implementation
}

// Parameters without const are writable (e.g. output parameters)
void clamp(const int value, const int lo, const int hi, int result) {
    if value < lo { result = lo }
    elsif value > hi { result = hi }
    else { result = value }
}

// Default parameter value
void retry(const string url, const int attempts = 3) {
    // attempts defaults to 3 when the caller omits it
}

// Generic parameter without const (see §6.7)
T identity<T>(T v) {
    return v
}
```

### 5.3 Visibility Modifiers

Functions can have different visibility levels:

```fly
// Default visibility (package-private)
void defaultFunction() {}

// Private function (internal use only)
private void privateHelper() {}

// Public function (exported)
public void publicAPI() {}
```

`protected` is not a visibility for functions: it applies only to the methods
and attributes of a class (see [11.1](#11-1-visibility-modifiers)), and on a
function it is an error.

### 5.4 Return Values

A function hands a value back with `return expr`. The caller receives it as if the function returned by value — but the compiler generates a hidden by-reference output parameter, writes the value into it and then returns, so **the result is never copied out of the callee's frame**.

That is a statement about the calling convention, not a promise that returning is free. `return expr` is lowered to an ordinary assignment into that hidden slot and obeys the ownership rules of [6.6](#6-6-allocation-and-lifetime): returning a *borrowed* string — a parameter or a field — clones its buffer, which is exactly what makes every call result independently owned; returning an array transfers ownership without copying a single element.

```fly
// Looks like return-by-value to the caller…
int square(const int n) {
    return n * n
}

void main() {
    int x = square(5)   // x = 25
}
```

This resolves the classic C++ ergonomics/performance tension: in C++ you must choose between `File f = open(path)` (readable, but implies a copy) or `open(path, &f)` (efficient, but noisy). Fly does both with the same syntax — the source reads as a plain return, and the compiler silently passes `x` by reference to `square`.

**Every path must return.** In a function that declares a return type, reaching the closing brace without a `return` is a compile error rather than a silent zero. An `if` counts only when it has an `else` and every branch returns, a `switch` only when it has a `default` and no case falls out of it; `fail` also ends the function, and so does `while true` with no `break`.

```fly
// error: 'classify' must return a value on every path
int classify(const int n) {
    if n > 0 {
        return 1
    }
}
```

**Building a struct in place.** When every `return` in a body hands back the same struct local, that local *is* the caller's slot: its fields are written straight into the returned object and the return copies nothing. This is what makes the idiomatic shape below cost exactly what filling an output parameter by hand used to cost:

```fly
Stat statOf(const string path) {
    Stat s
    s.size = sizeOf(path)
    s.mode = modeOf(path)
    return s          // no copy: `s` was the caller's Stat all along
}
```

**Writing into a value you are about to return.** An intrinsic that fills an output parameter needs a local to fill, which the return then hands back (this is standard-library code: from 0.15.0 `fly.llvm` can only be called from a `fly.*` namespace, see [Section 10.4](#10-4-reserved-namespaces)):

```fly
public int size() {
    int n = 0
    fly.llvm.ptrReadInt(this.ptr, 8, n)
    return n
}
```

**Multiple return types** are returned in one statement, in declaration order:

```fly
int,int divmod(const int a, const int b) {
    return a / b, a % b   // quotient, remainder
}

void main() {
    int q = divmod(17, 5)   // q = 3 — a plain assignment binds the first value
}
```

At the call site, a **multi-assignment** consumes every slot: list the
receiving variables (comma-separated) before `=` — they bind to the return
slots **in order**. Every receiver must be an **already-declared** variable
(a multi-assignment never declares), the right side must be a call, and the
receiver count must match the callee's return count exactly. The call writes
every receiver, so a `const` receiver is an error, the same as `k = …`
(a `const` field follows the rule of [§6.4](#6-4-class-members)). For example:

```fly
void main() {
    int q = 0
    int r = 0
    q, r = divmod(17, 5)   // q ← 3, r ← 2
}
```

A `return` with the wrong number of values is a compile error, as is a value in a function declared `void`.

**Early exit** in a void function is a bare `return`:

```fly
void process(const int x) {
    if x < 0 {
        return   // exit early — no value
    }
    // continue processing
}
```

### 5.5 The Main Function

The `main()` function is the entry point of a Fly application.

**Syntax:**
```fly
void main() {
    // Application code
}
```

**Key Characteristics:**

1. **Function signature:** Must be declared as `void main() {}` with no parameters and void return type
2. **Entry point:** The application starts execution from `main()`
3. **Automatic error handling:** The main function has special error handling behavior

The signature is **enforced**, not merely conventional. Declaring a return type
on `main` — single (`int main()`) or multiple (`int, int main()`) — is a compile
error in both compilers:

```
error: 'main()' must be declared 'void': the exit code comes from an unhandled 'fail', not from a returned value
```

It follows that `main` cannot `return` a value: a `void` function has no output
parameter, so a value there is the ordinary "'return' cannot carry a value in a
function declared 'void'" error (a bare `return` still exits early). The process
exit code is not something `main` returns — it is derived from the error state
described next, so the only things a program can act on are `fail` (to set an
error) and `handle` (to stop one from propagating).

**Error Handling and Return Codes:**

When the application runs, `main()` automatically returns an exit code to the operating system:

- **Return 0:** If no unhandled errors occur (success)
- **Return `code`:** If an unhandled error occurs, the exit code is the error's
  integer code (`fail 404, "…"` → exit 404; a bare `fail` or a `fail` with only a
  string/object → exit 1)

Before exiting with a non-zero code, `main()` also **prints the unhandled error to
stderr** in the form:

```
error <code>: <message>
```

(or just `error <code>` when the error carries no message). This behavior is
automatic—you don't explicitly return an integer from `main()`, and only the
*unhandled* error that is still recorded when `main()` ends is printed; handled
errors are never printed.

**Example 1: Successful Execution**
```fly
void main() {
    // Code executes successfully
    int x = 10
    int y = 20
    // Automatically returns 0 (success)
}
```

**Example 2: Unhandled Error**
```fly
void err0() {
    fail "Something went wrong"
}

void main() {
    err0()  // Error is not handled
    // Automatically returns 1 (failure)
}
```

**Example 3: Handled Error**
```fly
void err0() {
    fail "Something went wrong"
}

void main() {
    handle err0()  // Error is caught and handled
    // Continues execution
    // Automatically returns 0 (success)
}
```

**Example 4: Captured Error with Graceful Handling**
```fly
void riskyOperation() {
    fail "Operation failed"
}

void main() {
    handle {
        riskyOperation()
    }
    
    if error {
        // Error was caught and handled
        // Continue with fallback logic
    }
    // Automatically returns 0 (success)
}
```

**Best Practices:**

1. **Always handle errors in main:** Unhandled errors will cause the application to exit with code 1
2. **Use handle blocks:** Wrap risky operations in `handle` blocks to ensure graceful error handling
3. **Check error variables:** Use `if (error)` to detect and respond to errors appropriately
4. **Provide fallback logic:** When errors occur, provide alternative execution paths

**Summary:**
- `main()` is required (void return type, no parameters)
- Exit code 0 = success (no unhandled errors)
- Exit code 1 = failure (unhandled error occurred)
- Use `handle` to catch errors and ensure successful exit

---

## 6. Classes and Structures

### 6.1 Class Declaration

A class declares its base types after a colon. The grammar accepts a **comma-separated list** of base types: a class may extend a base struct and/or implement one or more interfaces.

**Syntax:**
```
Class    ::= [ Modifiers ] 'class' Identifier
             [ '<' TypeParam ( ',' TypeParam )* '>' ]
             [ ':' BaseType ( ',' BaseType )* ] '{' ClassMember* '}'
BaseType ::= NamedType
```

**Examples:**
```fly
// Simple class
class MyClass {
}

// Public class
public class Application {
}

// Class extending a struct
class Derived : BaseStruct {
}

// Class implementing an interface
class MyImpl : Drawable {
}

// Class with a base struct and one or more interfaces
class Widget : BaseStruct, Drawable, Resizable {
}
```

> `abstract` and `final` may be applied as class modifiers (see [Section 11](#11-modifiers)).

### 6.2 Structure Declaration

Structures are value types similar to classes. A struct can only extend another struct.

**Syntax:**
```
Struct ::= [ Modifiers ] 'struct' Identifier [ ':' Identifier ] '{' StructMember* '}'
```

**Examples:**
```fly
// Simple structure
struct Point {
    int x
    int y
}

// Public structure
public struct Vector {
    float x
    float y
    float z
}

// Struct extending another struct
struct Point3D : Point {
    int z
}
```

A struct field may have an **initializer** (`int x = 4`). From 0.16.0 every value of the struct starts from it, after the zero value: `new Point()`, a declaration `Point p`, a brace literal that does not give the field, a struct field inside an object or another struct, the base struct of a struct, the base struct of a class (see [Section 6.4](#6-4-class-members)), and each element of a sized struct array (`Point[3] ps`), in index order; a struct constructor does not run for them, as for `Point p`. Up to 0.15.x the initializer was ignored.

```fly
struct Pen {
    int width = 2
    string color = "black"
}

Pen a                     // width 2, color "black"
Pen b = {color = "red"}   // width 2, color "red"
```

### 6.3 Interface Declaration

Interfaces define contracts for classes. An interface can only extend another interface.

**Syntax:**
```
Interface ::= [ Modifiers ] 'interface' Identifier [ ':' Identifier ] '{' InterfaceMember* '}'
InterfaceMember ::= ReturnType Identifier '(' [ ParamList ] ')'
```

An interface method declares its return type like any function (`void` included)
and has no body; a class that implements the interface defines every method with
the same signature.

**Examples:**
```fly
// Simple interface
interface Drawable {
    void draw()
}

// Public interface
public interface Serializable {
    void serialize(const string path)
    bool deserialize(const string data)
}

// Interface extending another interface
interface Resizable : Drawable {
    void resize(const int width, const int height)
}
```

### 6.4 Class Members

Classes can contain fields (attributes) and methods.

**Constructors** are methods whose name is **exactly the class name** and which declare **no return type**. They are the one exception to the mandatory-return-type rule and run when the object is created with `new` (see [§6.5](#6-5-object-creation)).

**`this`** is available inside any instance method and refers to the current object. Use `this.field` to access members.

**In a constructor `this` is only a receiver** (from 0.16.0). In a constructor, and in an instance field initializer, `this` may be used to read or set a field and to call a method — `this.x`, `this.x = v`, `this.m()` — and nothing else. The object is not complete yet, and a constructor that fails releases it (see [A `new` that fails](#class-heap-allocated-released-with-delete)), so a copy of `this` kept elsewhere would point at released memory. Any other use is a compile error, `'this' can only be a receiver (this.x, this.m()) in a constructor or a field initializer`: assigned (`Reg.last = this`, `Node self = this`), passed as an argument (`list.add(this)`, `f(this)`), placed in an array value (`{this}`), compared. As the object of a `fail` it has an error of its own (see [Fail Statement](#9-7-2-fail-statement)). In every other method `this` is an ordinary value. A program that registers a new object does it after the `new`, where the object is complete:

```fly
Node n = new Node()
Reg.last = n
```

Not covered: a method called from the constructor (`this.m()`) can still store `this` somewhere, and the constructor can fail after it. Like using an object after `delete`, that is the program's responsibility. Up to 0.15.x `this` was an ordinary value in a constructor too.

All other methods require a return type (`void` for methods that return nothing).

**`const` fields** are read-only. A `static const` field takes its value from its initializer and is never assigned. An instance `const` field without an initializer is set once, by a constructor of the class that declares it, on its own object (`this.v = …`, `v = …`, or as a receiver of a multi-assignment, `a, v = f()`). Every other write is an error: another method, another object (`o.v = …`), a subclass constructor (`super.v = …`), a field that has an initializer, `+=`, `++`/`--` and `delete`. The error is the same as for a `const` local: `cannot assign to 'v': it is declared 'const'`.

**Field initializers.** A field may be declared with an initializer (`int count = 4`). An **instance** field's initializer runs when the object is created: after the object is zeroed and before any constructor, for every field that has one, inherited fields first and then the class's own, in declaration order, whether or not the constructor calls `super(...)`. So a constructor already sees the initialized values. An initializer may read the fields declared before it, with `this` as a receiver only (see above). If it fails (a call that `fail`s), the `new` fails and releases the object (see [A `new` that fails](#class-heap-allocated-released-with-delete)). A **`static`** field's initializer must be a constant: a literal, an enum entry, a `static const` field, or the operators and casts over them (`static const int B = A * 2 + 1`, `static const byte W = (byte)300`). It becomes the field's initial value, and no code runs for it before `main`. Any other value is a compile error (`static field 'n' needs a constant initializer`): assign it in code. Initializers are checked like a declaration's (`int f = "abc"` is an error). All of this is from 0.16.0. Up to 0.15.x an initializer was ignored and the field started at its zero value.

**Examples:**
```fly
public class Person {
    // Private fields
    private string name
    private int age
    
    // Public field
    public bool isActive
    
    // Static field
    static int instanceCount = 0
    
    // Constructor — same name as the class, no return type
    public Person(const string personName, const int personAge) {
        this.name = personName
        this.age = personAge
        this.isActive = true
        instanceCount++
    }
    
    // Public method with return type
    public string getName() {
        return this.name
    }
    
    // Private void method
    private void validate() {
        // validation logic
    }
    
    // Static method with return type
    public static int getCount() {
        return instanceCount
    }
}
```

### 6.5 Object Creation

Objects are created with `new`, which invokes a constructor. There is **no** `delete` operator in the language; how the memory is reclaimed depends on whether the type is a `struct` or a `class` (see [§6.6](#6-6-allocation-and-lifetime)).

**Examples:**
```fly
// Create a class instance (heap-allocated)
MyClass obj = new MyClass()

// Construct with arguments and call a return-type method
Person person = new Person("John", 30)
string name = person.getName()   // name = "John"
int count = Person.getCount()    // count = 1
```

---

### 6.6 Allocation and Lifetime

The `new` keyword allocates a new instance. Where the memory comes from — and how it is reclaimed — depends on whether the type is a `struct` or a `class`. A class instance is released with `delete` (from 0.15.0).

#### Struct: stack-allocated, freed automatically

A `new` on a `struct` allocates the data on the **stack** (via LLVM `alloca`). It is freed automatically when the enclosing scope exits — nothing to release manually.

```fly
struct Point {
    int x
    int y
}

void process() {
    Point p = new Point()   // ← stack alloca
    p.x = 10
    p.y = 20
}   // ← p released automatically when the scope exits
```

#### Class: heap-allocated, released with `delete`

A `new` on a `class` allocates on the **heap** — from 0.16.0 the runtime's own **object heap**, which knows every live object (see [Objects never deleted](#objects-never-deleted-the-report-at-the-end-of-main) below); up to 0.15.x the system `malloc`. From 0.15.0 the object is released with **`delete x`**, which:

1. does nothing when `x` is `null`;
2. calls the class's `free()` method, when it declares one with no parameters (its own or inherited) — that is where a class releases what it owns, such as a buffer;
3. releases the object's owning, holding and weak fields (see below), then the object itself. Steps 2 and 3 follow the class the object was created with, not the type of `x` (from 0.16.0);
4. sets `x` to `null`, so a second `delete x` is harmless and a later use of `x` stops on the null-reference trap instead of reading freed memory.

Deleting through **another** reference an object that is already gone — `Node b = a`, `delete a`, `delete b` — stops the program (from 0.16.0): `error 134: double delete of a 'Node' at main.fly:4:5`, exit code 134. The object heap no longer counts the object as live, so the second `delete` is caught before it runs anything.

The target must be a variable, a parameter or a field holding a class or interface reference; it cannot be `this`, a `struct` or a `const` variable.

**A `new` that fails.** When a field initializer, a constructor argument or the constructor itself fails, the object is only half built. From 0.16.0 the `new` releases it before the failure goes on: the owning, holding and weak fields already set (those the constructor never reached are still `null`), then its memory — for `new`, `new unique` and `new shared` alike. The class's `free()` does **not** run: the object was never built. Nothing is left for the report at the end of `main`. Up to 0.15.x the object was left behind. A constructor cannot hand `this` to the error (`fail 1, this` is a compile error, see [Fail Statement](#9-7-2-fail-statement)); when a method it calls does, the object travels in the error instead, and the `handle` that consumes the failure deletes it. An element of a sized array that fails stops the declaration the same way (see [Arrays](#arrays-reference-counted-freed-automatically)).

**A `free()` that fails.** `free()` may fail like any method (a `fail`, a failing call, an index out of range), and from 0.16.0 that is an ordinary failure, checked and propagated: a `handle` catches it, or `main` reports it and exits with its code. Up to 0.15.x it was not checked after `delete`, and it was dropped during a cleanup.

- **`delete x`:** the object is still released and `x` set to `null`; then the failure goes on from the `delete`, as from a failing call. The statement after it does not run.
- **The automatic releases:** the end of a `new unique` scope, the last `new shared` holder going away, the reassignment of an owner or a holder, a `break` or `continue` leaving a scope. With no failure in flight, the failure of `free()` is the failure at that point, as if a call there had failed.
- **The release at the end of `main`** of the objects never deleted (see [below](#objects-never-deleted-the-report-at-the-end-of-main)): when `main` ended cleanly, the first `free()` that fails is reported like an unhandled failure and sets the exit code; the other objects are still released.
- **Two failures at once.** A `free()` that fails while another failure is already in flight — the cleanup of a `new` or of a sized array that failed, the scopes released by a failing `return` or by a failure leaving a `handle`, the release at the end of a `main` that failed — does not replace it. The **first** failure stays the failure (its code, its message, its object, the exit code); each later one is **attached** to it, like Java's suppressed exceptions. Nothing is dropped and nothing is overwritten. Unhandled, `main` reports the first failure and then each attached one, in order; a `handle` that consumes the failure consumes the attached ones too (their messages freed, their objects deleted).
- **The error's object:** when a `handle` (or `main`) deletes the object of a `fail …, obj` and its `free()` fails, that is a new failure, starting after the `handle` as if a statement there had failed.

Every release still runs: a `free()` that fails does not stop the release of the object's fields, nor that of the other locals of the scope.

```fly
int compute() {
    Res r = new unique Res()     // r's free() fails with 43, "free"
    fail 50, "compute"
}

void main() {
    int v = compute()
}
```

```
error 50: compute
  also: error 43: free
```

The exit code is 50. Up to 0.15.x the 43 overwrote the 50.

```fly
import fly.data.List

void main() {
    List<int> l = new List<int>()     // ← heap allocation
    l.add(1)
    l.add(2)
    // … use l …
    delete l                          // ← l.free(), then the object, then l = null
}
```

#### `new unique`: one owner, released at the end of its scope

From 0.15.0 a class instance can be created with **`new unique`**. The variable it
initializes **owns** the object, and the compiler deletes it (as `delete` does:
`free()`, release, null) when that variable's scope ends — on every exit, `return`,
`fail` and `break` included. Nothing is written by hand and nothing leaks.

```fly
void process() {
    Res r = new unique Res()   // r owns the object
    use(r)                     // passing it to a call only LENDS it
}                              // ← r deleted here

Res make() {
    Res r = new unique Res()
    return r                   // the object MOVES to the caller, not deleted
}
```

The owner can **move** the object, and the source is then left `null` (a later use
stops on the null-reference trap):

- into a new declaration: `Res b = a` — `b` now owns it;
- into another owner: `b = a` deletes what `b` held, then `b` owns the object;
- out of the function: `return a` — the caller receives it, and the variable that
  takes the result (`Res m = make()`) **owns** it.

An owner can also be **reassigned**: `a = new unique Res()`, `a = b` (another owner)
or `a = make()` (an owned result) first delete what `a` held; `a = null` just deletes
it. An owner takes nothing else — a plain `new` object or a borrowed reference would
be deleted by someone who does not own it.

**Owning fields.** A field that a method fills with `this.f = new unique C()` or with
an owner (`this.f = x`) is an **owning field**: when the object is deleted — by
`delete`, or because its own owner's scope ended — the compiler first calls the
class's `free()`, then deletes every owning field, then releases the object. Assigning
an owning field deletes what it held, exactly like an owner variable. **Reading** an
owning field only **borrows** (`Res r = this.inner`, `return this.inner`): the field
keeps the object. A class may own a field of its own type, so a linked list built
with `new unique` nodes is deleted node by node.

```fly
public class Box {
    Res inner
    public Box() {
        this.inner = new unique Res()   // inner is an owning field
    }
}

void main() {
    Box b = new unique Box()
}   // ← b deleted: Box.free() (if any), then b.inner, then the Box
```

`new unique` must initialize a declaration or be assigned to an owner, and an owner
moves only into another owner, a new declaration or a return — never into a plain
variable: those are compile-time errors. A
`struct` cannot be `new unique` (it is already a value on the stack).

**Destruction follows the real class.** An object is destroyed as the class it was
created with, whatever the type of the reference that lets it go: `delete` through a
base class or an interface, the end of a `new unique` owner's scope and the last
`new shared` holder all run the `free()` of the object's own class (its own or the one
it inherits) and release every owning, holding and weak field that class has, bases'
fields included. A base class's `free()` is not called on top of a subclass's:
an override that needs it calls `super.free()`.

```fly
public class Shape {
}

public class Poly : Shape {
    Res points
    public Poly() {
        this.points = new unique Res()   // a field only Poly declares
    }
}

void main() {
    Shape s = new Poly()
    delete s   // ← Poly.free() (if any), then s.points, then the Poly
}
```

#### `new shared`: counted references

From 0.15.0 a class instance created with **`new shared`** lives as long as it has a
**holder**. The variable it initializes is the first holder; every copy into another
holder counts one more, and each holder lets go at the end of its scope, on
reassignment, with `= null` or with `delete`. When the last holder lets go the object is
destroyed — `free()`, its owning fields, its memory.

```fly
void main() {
    Res a = new shared Res()   // count 1
    Res b = a                  // count 2: a copy, not a move
    use(b)                     // passing it to a call only LENDS it
    delete a                   // count 1: a is null, the object lives on in b
}                              // ← b lets go: count 0, the object is destroyed
```

- A function that returns a holder (`return r`) hands its count to the caller, and
  the variable that takes the result (`Res m = make()`) is a holder.
- A holder takes only a `new shared` object, another holder, a held result or
  `null`; it is copied only into another holder, a new declaration or a return.
  Mixing it with `new unique` owners or plain references is a compile-time error.
- `delete` on a holder releases **that** reference, not the object. Never `delete` a
  shared object received as a plain (borrowed) reference.
- The count lives in a 16-byte header in front of the object (with the weak count, see
  below); it is **not atomic**: do not share these objects between threads yet.
- **Holding fields.** A field a method fills with `this.f = new shared C()` or with a
  holder is a holding field: assigning it releases what it held, copying it
  (`Res c = this.f`) counts one more holder (and a method that returns that copy
  hands the count to its caller), and destroying the object releases it — the shared
  object survives the container while another holder has it.

#### `weak h`: observing without holding

From 0.15.0 a **weak reference** observes a `new shared` object without keeping it
alive. It does not create anything: it is made from a holder with **`weak h`**.

```fly
void main() {
    Res h = new shared Res()   // creation: h is the first holder
    Res w = weak h             // link: w observes h's object, the count stays 1
    if w != null {
        w.use()                // while a holder has it, w reads as the object
    }
    h = null                   // last holder gone: the object is destroyed
    if w == null {
        // … and every weak reference to it now reads null
    }
}
```

- Every read of a weak variable or field yields the object while it lives and `null`
  once its last holder has let go, so a use after that stops on the null trap
  instead of touching a destroyed object.
- `weak` takes a holder or another weak reference, and it must initialize a
  declaration or be assigned to a weak variable or field; a weak variable takes only
  `weak h` or `null`. `new weak C()` is a compile-time error.
- Copying a weak reference into a holder **promotes** it: `Res p = w` is a holder that
  counts one more (or is `null`, if the object has gone).
- **Weak fields.** A field a method fills with `this.f = weak h` is a weak field: the
  typical use is the back reference of a parent/child pair (the parent holds the child,
  the child observes the parent), which a pair of holding fields would keep alive
  forever.
- The 16-byte header in front of a shared object keeps two counts — holders and weak
  references. The object is destroyed with its last holder; the header is freed when
  the last weak reference has gone too.

> **A plain `new` requires `delete`.** From 0.16.0 an object created with a plain `new` belongs to the program until a `delete` releases it: leaving the scope of the variable that holds it releases nothing, and an object that is never deleted leaks. Automatic release belongs to `new unique` (at the end of the owner's scope) and `new shared` (with the last holder). In 0.15.x a local initialized directly with `new` that had not escaped (passed to a call or assigned elsewhere) was still released at the end of its scope — only the object, not what its `free()` would release. Up to 0.14.x there was no `delete`: classes exposed `free()` and the caller invoked it.

#### Strings: managed automatically

`string` values are heap-backed but managed for you. A non-`const` `string` variable with an initializer owns its buffer: the compiler frees it at scope exit, and **reassigning** the variable frees the previous buffer before storing the new one. `const` strings and uninitialized strings point at static/null data and are never freed.

```fly
void demo() {
    string s = str.toUpper("hello")  // owns a heap buffer
    s = str.toLower("WORLD")         // old buffer freed automatically before reassignment
}   // ← final buffer freed automatically at scope exit
```

**The rule that makes this work.** Everything above follows from one invariant: *every owned string slot holds a buffer that nothing else owns*. That is what lets the compiler free at scope exit unconditionally, with no escape analysis and no reference counting. Storing into an owned slot therefore takes one of three forms, chosen by what the right-hand side is:

| Right-hand side | What is stored | Why |
|---|---|---|
| a string **literal** | a fresh heap **copy** of the constant | the constant lives in static memory and must never be freed |
| a string **lvalue** — variable, parameter, field, member, **array element** | a **deep clone** of its buffer | the original still owns its buffer; sharing it would free it twice |
| a **fresh producer** — a call result, a concatenation | the buffer as-is, **moved** | it is already unique and unowned, so copying it would be waste |

```fly
void demo(const string param) {
    string a = "hello"        // copy of the constant
    string b = a              // deep clone — a and b own different buffers
    string c = param          // deep clone — the caller still owns param's buffer
    string d = a + b          // moved — the concatenation's buffer was already fresh
}   // ← a, b, c and d each free their own buffer
```

The empty string is the one case that goes the other way: it owns no heap at all, so cloning it allocates nothing and freeing it is a no-op.

Because `return <lvalue>` is lowered to that same assignment, a function that returns a *borrowed* string — a parameter or a field — returns a **clone**. That is what makes every call result a fresh buffer, and why the third row above is safe.

#### Arrays: reference-counted, freed automatically

An array is a **fat pointer**, `{data, size}`, held in the variable's own storage. The elements live in a heap buffer preceded by an 8-byte reference count:

```
    variable            heap buffer
  ┌────────┬──────┐   ┌──────────┬─────────────────────┐
  │  data  │ size │   │ refcount │ element 0, 1, 2, …  │
  └───┬────┴──────┘   └──────────┴─────────────────────┘
      │                ◄─8 bytes─►▲
      └─────────────────────────── points HERE
```

Arrays therefore have **reference semantics**, and this is the one place where Fly deliberately diverges from the rest of the language — structs and strings copy, arrays do not:

```fly
void demo() {
    int[] k = {1, 2, 3}
    int[] j = k        // NO copy: one buffer, two names, count = 2
    j[0] = 9
    int x = k[0]       // ← 9. The write through j is visible through k.
}   // ← k releases (count 1), then j releases (count 0) → the buffer is freed, once
```

The rules, all of them:

- **Declaring** an array variable makes it an **owner**: it holds a reference and releases it at scope exit. The buffer goes back when the last owner releases it.
- **Binding another array** (`int[] j = k`) copies the fat pointer and **retains**. No element is ever copied, whatever the array's size.
- **Reassigning** (`k = {7, 8}`) retains the new buffer, releases the old one, then stores — in that order, so `k = k` is harmless.
- **Passing as an argument** is a **borrow**: no retain, no release. The callee sees the array for the duration of the call.
- **Returning an array** transfers ownership to the caller: the callee retains on the caller's behalf, then its own variable releases, leaving the caller holding the only reference.
- The **item of a `for in`** is a view of the element, not an owner.
- **Nested arrays** (`int[][]`) have one buffer and one count *per level*. Releasing the outer array walks its elements and releases each inner buffer first.
- An **empty array** (`int[0]`, or a runtime size of zero) has no buffer at all. Declaring, binding and iterating one are all legal and allocate nothing.

**The ownership rule.** An array owns its **buffer** and nothing else. Freeing it drops the elements it holds — it never frees what they point at. Every object is released by whoever is responsible for it, exactly as in the summary table below. The one apparent exception, an array of arrays, is not one: an inner array is itself reference counted, so what it receives is a *decrement*, not a free.

Two consequences deserve stating plainly.

**An array of classes is born full, but the instances are yours.** Declaring `C[3]` builds three *distinct* instances, one per index, with `C`'s no-argument constructor — every class has one, implicitly, when it does not declare it. They are ordinary `new` allocations and follow the class convention: the array releasing its buffer does **not** free them.

```fly
void demo() {
    Cell[3] cells          // three separate instances, already constructed
    Cell first = cells[0]
    first.set(7)           // does not affect cells[1] or cells[2]
}   // ← the buffer of pointers is freed; the three Cell instances are NOT
```

**An element that fails stops the declaration.** When the constructor or a field initializer of element `k` fails, `C[n] cs` fails with it, and from 0.16.0 nothing is left behind: element `k` is released as any [`new` that fails](#class-heap-allocated-released-with-delete) is (its fields, then its memory, without `free()`), the elements already built are deleted from `k-1` down to `0` (their `free()` runs), then the buffer goes. A sized struct array whose field initializer fails at element `k` stops the same way. Up to 0.15.x a failing element constructor did not stop the declaration, and a failing initializer left the elements behind.

A sized array of an **interface** is rejected at the declaration: an interface has no constructor to build the elements with. Supply them with an array literal instead.

**Elements are borrowed, and a borrow can outlive its owner.** Storing a string, a class or a struct into an array neither copies it nor transfers ownership. Reading one back out into an owned slot *clones* it, by the string rule above, so the common direction is safe. The opposite direction is not:

```fly
string[] names = {"a", "b"}
{
    string s = str.toUpper("hello")
    names[0] = s      // borrow — names does not own this buffer
}                     // ← s is freed here; names[0] now dangles
```

That is the direct consequence of the ownership rule, not an oversight: cloning on store would mean the array *owned* the element, which is exactly what it must not do. Keep the owner alive at least as long as the array borrowing from it.

String **literals** inside an array literal are unaffected: they are stored as pointers into static memory, allocate nothing per element, and need no freeing.

**When the release happens.** An owned array is released when its scope ends: where the scope falls through its end, and on every exit that leaves it early — a `return`, a `break` or `continue`, a failure that returns from the function, and (from 0.16.0) a failure that jumps to the end of an enclosing `handle` (see [Handle Statement](#9-7-4-handle-statement)) or of a suite `case`. Strings, `new unique` owners and `new shared` holders are released on the same paths.

**In which order.** The scopes an exit leaves are released innermost first, and within one scope the locals go **last declared first** (from 0.16.0), on every exit, the scope's end included. A local may lean on one declared before it — an object whose `free()` reads an earlier owner's object through a plain reference — so the earlier one is still there when the later one goes. Up to 0.15.x the locals of a scope went in declaration order, and such a `free()` read an object already released.

```fly
{
    Res a = new unique Res()
    User u = new unique User(a)   // u keeps a plain reference to a's object
}                                 // ← u first (its free() may use a), then a
```

**Class fields of array type** are likewise never released: only locals are registered as owners.

---

#### Summary

| Expression | Memory | Reclaimed by |
|---|---|---|
| `struct S = new S()` | stack (alloca) | automatic at scope exit |
| `class C = new C()` | heap (the object heap) | `delete x` (calls `free()`, releases, sets `x` to null); never deleted, it is reported and released at the end of `main` |
| `string s = …` (non-const, initialized) | heap | automatic at scope exit; reassignment frees the old buffer |
| `const string s = …` | static/null | nothing to free |
| `int[] xs = …` / `int[3] xs` | heap, `[refcount \| elements]` | reference counted: released at scope exit, freed when the last owner goes |
| an array's **elements** | wherever they came from | never the array — each object is freed by whoever is responsible for it |
| `C[3] cells` (array of classes) | heap buffer + one `new C()` per index | the buffer automatically; the **instances** by the class convention |

#### Ownership qualifiers at a glance

| Form | Who frees the object | Copy `C y = x` |
|------|----------------------|----------------|
| `new C()` | `delete x` — nothing else; never deleted, it is reported at the end of `main` | an alias, nothing counted |
| `new unique C()` | the owner, at the end of its scope (or `delete`) | a **move**: `x` is left null |
| `new shared C()` | the last holder | one more holder |
| `weak h` | never — it only observes a shared object | a holder (promotion) |

#### Objects never deleted: the report at the end of `main`

From 0.16.0 every class object — `new`, `new unique`, `new shared` — lives in the runtime's **object heap**, which knows the live ones. When `main` ends, the program reports on **stderr** the objects it never deleted, by class, most first, and then releases them. The exit code does not change, unless a `free()` fails in that release (see [A `free()` that fails](#class-heap-allocated-released-with-delete)). A program that deletes everything prints nothing.

```fly
namespace app

class Node {
    int v
}

void main() {
    Node a = new Node()
    Node b = new Node()
}
```

```
warning: 2 objects not deleted (32 B)
  app.Node x2, 32 B
```

- The count includes what a leaked object owns or holds. A generic class is named with its type arguments (`fly.data.List<string>`); a class compiled by an earlier release (a package built with 0.15.x) has no type information and is counted as `(a class built by an earlier compiler)`.
- **Release.** Each leaked object is then released as `delete` would: its `free()` runs and its owning and holding fields go with it. An object is released before the objects it owns or holds, so a `free()` still finds them; objects that hold each other (a `new shared` cycle) are released once each. A `BufWriter` nobody closed writes out its buffered bytes. A `free()` that fails here is reported after the list: as `error <code>: <message>`, which sets the exit code, when `main` ended cleanly; as `  also: error …` after the failure of a `main` that failed. Through `fly.os.env.exit` it is reported and the exit code stays the one given to `exit`.
- **When.** When `main` returns or ends with an unhandled `fail`, and also when the program ends through `fly.os.env.exit`. Not after a run-time trap (`error 134`, `error 2989`…) or a crash: a broken program is not walked.
- **Test suites.** A suite's generated `main` reports at its end like any program, and each `case` that leaves objects alive gets a line under its result — a warning, the case still passes (see the Testing page).
- **Not covered.** Strings and arrays (released automatically), raw `fly.mem` blocks, structs (stack). The Fly toolchain itself (compiler, `fly-lsp`, `fly-registry`) does not delete its syntax trees yet and turns the report off for itself.

### 6.7 Generics

Fly supports **generic classes** and **generic functions** via monomorphization. Each unique instantiation is compiled into a distinct, fully specialized implementation — no type erasure, no boxing overhead, no runtime cost.

#### 6.7.1 Generic Class Declaration

Add one or more **type parameters** in angle brackets after the class name.

**Syntax:**
```
GenericClass ::= [ Modifiers ] 'class' Identifier '<' TypeParam ( ',' TypeParam )* '>' [ ':' BaseType ( ',' BaseType )* ] '{' ClassMember* '}'
TypeParam    ::= Identifier [ ':' Type ]
```

A type parameter may carry an optional **bound** after a colon (`<T : SomeType>`), constraining the types it can be instantiated with.

**Example:**
```fly
public class Wrapper<T> {
    private T value

    public Wrapper(T v) {
        value = v
    }

    public T get() {
        return value
    }

    public void set(T v) {
        value = v
    }
}
```

#### 6.7.2 Instantiation

Provide concrete type arguments in angle brackets when declaring a variable. Each unique combination of type arguments produces a **separate monomorphized type** at compile time.

```fly
import fly.data

void main() {
    // Wrapper<string> — holds a string
    Wrapper<string> ws = new Wrapper<string>("hello")
    string s = ws.get()   // s = "hello"
    ws.set("world")

    // Wrapper<int> — holds an int
    Wrapper<int> wi = new Wrapper<int>(42)
    int n = wi.get()       // n = 42

    // Wrapper<bool>
    Wrapper<bool> wb = new Wrapper<bool>(true)
    bool b = wb.get()      // b = true
}
```

`Wrapper<string>` and `Wrapper<int>` are entirely separate types: the compiler emits a distinct LLVM struct and a distinct set of methods for each instantiation.

#### 6.7.3 Generic Functions

Functions can also declare type parameters, placed between the function name and the parameter list.

**Syntax:**
```
GenericFunc ::= [ Modifiers ] ReturnType Identifier '<' TypeParam ( ',' TypeParam )* '>' '(' [ Parameters ] ')' Block
TypeParam   ::= Identifier [ ':' Type ]
```

**The type argument is inferred from the call's arguments** — and only from them. A generic function is called like any other, with no `<…>` at the call site:

```fly
// Generic identity function — returns its argument unchanged
T identity<T>(const T v) {
    return v
}

void main() {
    int    i = identity(10)     // T inferred as int
    string s = identity("fly")  // T inferred as string
    bool   b = identity(true)   // T inferred as bool
}
```

> **Explicit type arguments are for generic *classes*, not functions.**
> `new Wrapper<int>(42)` is how you name an instantiation ([6.7.2](#6-7-2-instantiation));
> `identity<int>(10)` does **not** compile — the compiler reads `<` as a comparison and
> reports that `identity` is a function, not a value. Write `identity(10)` instead.
>
> It follows that **a type parameter must appear among the parameters**: one that shows up
> only in the return type (`T make<T>()`) has nothing to be inferred from, and such a
> function cannot be called.

#### 6.7.4 Managing a List of Strings — fly.data.List\<string\> Pattern

`fly.data.List` is an untyped dynamic array that stores `long` values (raw integers or object addresses). To maintain a **typed list of strings**, wrap each string in a `Wrapper<string>` and store the wrapper reference in the list. Retrieve the wrapper and call `.get()` to recover the string.

```fly
import fly.data.list
import fly.data.wrapper

void main() {
    List lst = new List()

    // Box each string into a Wrapper<string>
    Wrapper<string> a = new Wrapper<string>("apple")
    Wrapper<string> b = new Wrapper<string>("banana")
    Wrapper<string> c = new Wrapper<string>("cherry")

    lst.add(a)
    lst.add(b)
    lst.add(c)

    // Iterate — retrieve wrapper, then unwrap the string
    int total = lst.size()   // total = 3
    for int i = 0; i < total; i++ {
        Wrapper<string> item = lst.get(i)
        string text = item.get()
        // use text …
    }

    lst.free()
}
```

The same pattern applies to any heap-allocated type: `Wrapper<int>`, `Wrapper<MyClass>`, etc.

| Goal | Approach |
|---|---|
| Store strings in a list | `Wrapper<string>` + `List` |
| Store ints in a list | `Wrapper<int>` + `List` (or raw `long` directly) |
| Single typed value | `Wrapper<T>` alone |

---

## 7. Enumerations

### 7.1 Enum Declaration

Enumerations define a set of named constants. Enums **cannot extend** any other type.

**Syntax:**
```
Enum ::= [ Modifiers ] 'enum' Identifier '{' EnumEntryList '}'
EnumEntryList ::= EnumEntry ( ',' EnumEntry )*
EnumEntry ::= Identifier
```

**Examples:**
```fly
// Simple enum with comma-separated entries
enum Color {
    RED, GREEN, BLUE
}

// Public enum
public enum Status {
    IDLE, RUNNING, STOPPED, FAILED
}


// Multi-line enum for readability
enum Direction {
    NORTH,
    SOUTH,
    EAST,
    WEST
}
```

### 7.2 Using Enums

**Examples:**
```fly
void processColor() {
    // Declare and initialize
    Color c = Color.RED
    
    // Assignment
    c = Color.BLUE
    
    // Pass to function
    setColor(Color.GREEN)
    
    // Compare
    if c == Color.RED {
        // handle red
    }
}

void setColor(const Color c) {
    // use color
}
```

Every constant has a **`.value`** — its position in the declaration, from `0`
(`Color.RED.value` is 0, `Color.BLUE.value` is 2) — and a **`.name`**, the declared
identifier (`"RED"`); both work on a constant and on a variable. An enum variable
declared **without an initializer holds the first constant**: after `Color c`,
`c == Color.RED` is true. Up to v0.15.2 values started at 1 and an uninitialized
enum variable equalled none of the constants.

---

## 8. Expressions

### 8.1 Primary Expressions

#### 8.1.1 Literals

```fly
42              // integer literal
3.14            // float literal
true            // boolean literal
"string"        // string literal
null            // null literal
```

#### 8.1.2 Identifiers

```fly
myVariable      // simple identifier
obj.field       // member access
array[0]        // array access
```

#### 8.1.3 Array Subscript

`xs[i]` reads or writes one element of an array. The index is any integer expression; the element takes the array's element type.

```fly
int[] k = {5, 6, 7}

int x = k[1]        // read  → 6
k[0] = 40           // write
int y = k[x - 5]    // the index is an ordinary expression
```

**Both forms are bounds-checked at run time**, and on both sides: the index is compared against the array's `size` field and must satisfy `0 <= i < size`. A negative index is caught as surely as one past the end — the check is signed precisely so it can be.

An out-of-range access **fails** with the dedicated error code `2989` (`0x0BAD`), which behaves like any other `fail`: it propagates to the caller, sets the exit code of `main`, and can be intercepted with `handle`. Its message says where: uncaught, the program prints `error 2989: index out of range at main.fly:4:5`.

> **Reading the code from a shell.** `main` returns 2989 on every platform, but POSIX passes only the low 8 bits of a process status through `wait()`, so a Unix shell reports `173`. Windows preserves the full value. Intercepting with `handle` — below — is unaffected and is the portable way to inspect the code.

```fly
void demo() {
    int[] k = {5, 6, 7}

    handle {
        int bad = k[9]      // out of range → fail 2989
    }
    bool caught = error     // ← true; execution continues after the handle
}
```

A caught failure leaves the array — and every other local — untouched and still usable: control resumes in the same scope, so nothing is released early.

What the subscript yields depends on the element type, and follows the ownership rules of §6.6: a **number** is a value, a **class** element is the instance itself (a reference), a **struct** element is copied by value, and a **string** element is a borrow that is cloned when it is bound to an owned slot.

> **Note.** Chaining a subscript directly into a member access — `cells[0].set(7)` — is accepted by the self-host compiler but **not** by the reference, whose subscript does not chain. For code that must build with either, bind the element first: `Cell c = cells[0]` then `c.set(7)`.

#### 8.1.4 Parenthesized Expressions

```fly
(a + b)
(x * y + z)
```

### 8.2 Unary Expressions

**Syntax:**
```
UnaryExpr ::= ( '++' | '--' | '!' | '-' ) Expression
            | Expression ( '++' | '--' )
```

Unary `-` negates any numeric operand (`-5`, `-x`, `-(a * 2)`, `-2.5`).
There is **no** unary `+`. Postfix `++`/`--` bind **same-line only** — a
`++`/`--` at the start of a line is always a prefix statement.

**Examples:**
```fly
// Pre-increment/decrement
++counter
--index

// Post-increment/decrement
value++
count--

// Logical negation
!flag
!isActive

// Unary minus/plus
-value
+number
```

### 8.3 Binary Expressions

#### 8.3.1 Arithmetic Operators

```fly
a + b           // addition
x - y           // subtraction
m * n           // multiplication
p / q           // division
r % s           // modulo
```

Integer `/` truncates toward zero and `%` takes the sign of the dividend
(`-17 / 5` is `-3`, `-17 % 5` is `-2`). From 0.15.0 both are **checked in every
build**:

- a zero divisor stops the program with
  `error 134: division by zero at main.fly:2:5` and exit code 134;
- the minimum value of a signed type divided by `-1` (`-2147483648 / -1`, whose
  result does not fit) stops it with `error 134: integer overflow: the minimum
  value divided by -1 at …`;
- `%` by `-1` is always `0`, the minimum value included.

The compound forms `/=` and `%=` are checked the same way. A constant divisor
(`x / 2`) costs nothing: the check is resolved at compile time. Floating-point
division follows IEEE 754 instead: `1.0 / 0.0` is infinity, not a trap.

`+`, `-` and `*` on integers are **checked in every build** from 0.15.0 too, like
`++`, `--` and unary `-`: a result that does not fit its type stops the program
with `error 134: integer overflow in '+' at main.fly:2:5`. That covers signed and
unsigned types alike (`uint` `3 - 5` stops), and a compound assignment computed in
a wider type must still fit its variable (`short s = 32000; s += 1000` stops with
`the result does not fit in 'short'`). Not checked: `pointer` arithmetic (address
offsets wrap by design), shifts and bitwise operators, explicit casts (`(byte)300`
is `44`), and constant expressions, which are computed at compile time. When the
wrapping result is the point — a hash, a checksum, a random generator — ask for
it: `math.wrapAdd(a, b)` and its siblings in `fly.math`.

#### 8.3.2 Comparison Operators

```fly
a == b          // equal to
x != y          // not equal to
m < n           // less than
p > q           // greater than
i <= j          // less than or equal
k >= l          // greater than or equal
```

#### 8.3.3 Logical Operators

```fly
flag1 && flag2  // logical AND
cond1 || cond2  // logical OR
```

Both operators **short-circuit**: the right operand is evaluated only when the
left one does not already decide the result. `a && f()` never calls `f()` when
`a` is `false`, and `a || f()` never calls `f()` when `a` is `true` — so the
right operand may safely guard on the left (`p != null && p.ready()`).

#### 8.3.4 Bitwise Operators

```fly
a & b           // bitwise AND
x | y           // bitwise OR
m ^ n           // bitwise XOR
p << 2          // left shift
q >> 1          // right shift
```

### 8.4 Assignment Expressions

**Syntax:**
```
Assignment ::= Identifier AssignOp Expression
AssignOp   ::= '=' | '+=' | '-=' | '*=' | '/=' | '%=' 
             | '&=' | '|=' | '^=' | '<<=' | '>>='
```

**Examples:**
```fly
// Simple assignment
x = 10
name = "Fly"

// Compound assignment
a += 5          // a = a + 5
b -= 3          // b = b - 3
c *= 2          // c = c * 2
d /= 4          // d = d / 4
e %= 7          // e = e % 7

// Bitwise compound assignment
f &= mask       // f = f & mask
g |= flag       // g = g | flag
h ^= toggle     // h = h ^ toggle
i <<= 2         // i = i << 2
j >>= 1         // j = j >> 1
```

**What a class or an enum variable takes.** A class value is accepted where its own class, one of its bases or one of its interfaces is expected: in a declaration, an assignment, a `return`, a field initializer and a struct literal. An unrelated class, or a base where a subclass is expected, is an error (`cannot assign a value of type 'Cat' to a variable of type 'Dog'`); two classes with the same short name from two namespaces are named in full (`probe.right.P`, `probe.left.P`). An enum variable takes only its own enum. `null`, and the integer and `pointer` conversions of a class handle, stay as they are. Up to 0.15.x (the v0.15.2 seed included) only the kind was checked, so any class went into any class variable and any enum into any enum variable.

```fly
class Animal {
}
class Dog : Animal {
}
class Cat {
}

Animal a = new Dog()    // ok: Dog derives from Animal
Dog d = new Cat()       // error: Cat is not a Dog
Dog e = a               // error: an Animal is not a Dog
```

#### 8.4.1 Assignment vs Equality: Important Distinction

Fly clearly distinguishes between the **assignment operator** `=` and the **equality comparison operator** `==`:

- **`=` (Assignment)**: Stores a value into a variable. This is a statement-level operation.
- **`==` (Equality)**: Compares two values for equality. This is an expression that evaluates to a boolean.

**Examples:**
```fly
// Assignment: stores the value 5 into variable x
x = 5

// Equality comparison: compares x with 5, evaluates to boolean
if x == 5 {
    // x is equal to 5
}

// Assignment with equality comparison on right side
result = x == 5    // result gets true or false

// Complex example
a = a + 1          // a = (a + 1) - addition then assignment
b = a == 10        // b = (a == 10) - comparison then assignment
```

**Parser Representation:**
Under the hood, the parser creates different AST structures:
- Assignment `a = expr` creates `ASTBinaryOp(OP_BINARY_ASSIGN)` with left=`a` and right=`expr`
- Equality `a == b` creates `ASTBinaryOp(OP_BINARY_EQ)` with left=`a` and right=`b`
- Assignment with equality `a = (b == c)` creates nested structure:
  - Outer: `ASTBinaryOp(OP_BINARY_ASSIGN)` with left=`a`
  - Right child: `ASTBinaryOp(OP_BINARY_EQ)` with left=`b` and right=`c`

**Common Mistake:**
```fly
// WRONG: Using = instead of == in condition
if x = 5 {        // This assigns 5 to x, then evaluates the result
    // ...
}

// CORRECT: Using == for comparison
if x == 5 {       // This compares x with 5
    // ...
}
```

### 8.5 Ternary Conditional Expression

**Syntax:**
```
TernaryExpr ::= Condition '?' TrueExpr ':' FalseExpr
```

**Examples:**
```fly
result = condition ? valueIfTrue : valueIfFalse
max = a > b ? a : b
status = isActive ? Status.RUNNING : Status.IDLE
```

### 8.6 Function Call Expressions

**Examples:**
```fly
// Function call without arguments
result = calculate()

// Function call with arguments
sum = add(10, 20)
process(x, y, z)

// Method call
obj.doSomething()
person.getName()
```

### 8.7 Array Value Expressions

**Examples:**
```fly
// Empty array
empty = {}

// Array with values
values = {1, 2, 3, 4, 5}
matrix = {% raw %}{{1, 2}, {3, 4}}{% endraw %}
```

Each literal allocates its **own** reference-counted buffer, and a nested literal allocates one per level — `matrix` above is three buffers, the outer one holding the two rows. They are released like any other array (see [6.6](#6-6-allocation-and-lifetime)).

An empty literal allocates nothing at all.

---

## 9. Statements

### 9.1 Expression Statements

Any expression can be used as a statement.

**Examples:**
```fly
// Function call
doSomething()
calculate()

// Increment/decrement
counter++
--index

// Assignment
x = 42
```

### 9.2 Block Statements

**Syntax:**
```
Block ::= '{' Statement* '}'
```

**Examples:**
```fly
{
    int x = 10
    int y = 20
    int z = x + y
}
```

### 9.3 If Statements

**Syntax:**
```
IfStmt ::= 'if' Expression Statement
           ( 'elsif' Expression Statement )*
           [ 'else' Statement ]
```

The condition is written **without parentheses around it**: `if (x > 0) {` is an
error (the compiler suggests `if x > 0 {`). Parentheses inside it group as usual —
`if (a + b) * 2 > c {` is fine. The same holds for `elsif`, `while`, `switch`, `for`
and for-in.

**Examples:**
```fly
// Simple if
if condition {
    // code
}

// If-else
if x > 0 {
    positive = true
} else {
    positive = false
}

// If-elsif-else
if a == 1 {
    b = 0
} elsif a == 2 {
    b = 1
} elsif a == 3 {
    b = 2
} else {
    b = -1
}

// Inline if (without braces)
if condition doSomething()
```

### 9.4 Switch Statements

**Syntax:**
```
SwitchStmt  ::= 'switch' Expression '{' CaseClause* [ DefaultClause ] '}'
CaseClause  ::= 'case' Expression ( ',' Expression )* ':' Statement+
DefaultClause ::= 'default' ':' Statement*
```

**Examples:**
```fly
switch value {
    case 1:
        result = "one"
    case 2:
        result = "two"
    case 3, 4:
        result = "three or four"
    default:
        result = "other"
}

switch value {
    case 0:
        result = "zero"
        if strict {
            break              // leaves the switch early
        }
        result = "zero, relaxed"
    default:
        result = "other"
}
```

**No fall-through** (from 0.15.0): when a case body ends, the switch ends — it
never continues into the next case or into `default`, so a `break` at the end of
a case is not needed (it is still allowed, and `break` in the middle of a body
leaves the switch early). Several values share one body by being listed in one
case: `case 3, 4:`. A case with **no statements** is a compile-time error: up to
0.14 it shared the next case's body (C stacking), and the same text must not
quietly turn into a case that does nothing. Case bodies may be braced:
`case 1: { r = 10 }`.

> Up to 0.14.x the `switch` was C-style: a case body that did not end in
> `break`, `return` or `fail` fell through into the next case body, and stacked
> empty labels (`case 3:` `case 4:`) shared the following body.

### 9.5 Loop Statements

#### 9.5.1 While Loop

**Syntax:**
```
WhileStmt ::= 'while' Expression Statement
```

**Examples:**
```fly
// While
while count < 10 {
    count++
}

// Infinite loop
while true {
    // loop body
    if shouldBreak break
}

// Inline while
while condition doSomething()
```

#### 9.5.2 For Loop

**Syntax:**
```
ForStmt ::= 'for' VarDecl ( ',' VarDecl )* ';' Expression ';' 
            Expression ( ',' Expression )* Statement
```

**Examples:**
```fly
// Standard for loop
for int i = 0; i < 10; i++ {
    // loop body
}

// Multiple initialization and post expressions
for int i = 0, int j = 10; i < j; i++, j-- {
    // loop body
}

// Over an array
for int i = 0; i < length; i++ {
    process(array[i])
}
```

#### 9.5.3 For-In Loop

Fly also provides a **for-in** loop that iterates a loop variable over the elements of a collection expression.

**Syntax:**
```
ForInStmt ::= 'for' Identifier 'in' Expression Statement
```

**Examples:**
```fly
// Iterate over the elements of a list
for item in items {
    process(item)
}
```

### 9.6 Jump Statements

#### 9.6.1 Return Statement

`return` leaves the function. In a function that declares a return type it carries the value — one expression per declared type — and every path through the body must reach one. In a `void` function it takes no value and is used to exit early.

**Syntax:**
```
ReturnStmt ::= 'return' [ Expr { ',' Expr } ]
```

A value must begin on the same line as the keyword: Fly has no statement
terminator, so that is what separates `return` followed by a value from a bare
`return` followed by the next statement.

**Examples:**
```fly
// Hand a value back
return n * n

// One value per declared return type
return a / b, a % b

// Early exit from a void function
if done {
    return
}
```

#### 9.6.2 Break Statement

**Syntax:**
```
BreakStmt ::= 'break'
```

**Examples:**
```fly
while true {
    if condition {
        break  // exit loop
    }
}

switch value {
    case 1:
        doSomething()
        break  // exit switch
}
```

#### 9.6.3 Continue Statement

**Syntax:**
```
ContinueStmt ::= 'continue'
```

**Examples:**
```fly
for int i = 0; i < 10; i++ {
    if i % 2 == 0 {
        continue  // skip even numbers
    }
    process(i)
}
```

### 9.7 Error Handling Statements

Fly error handling is built on two keywords — `fail` and `handle` — and a built-in `error` type. The mechanism is **not** exception-based stack unwinding. Instead, every function receives a hidden first parameter: a pointer to an `error` struct. When `fail` fires, it writes into that struct and either jumps past the surrounding `handle` block (if one exists in the same function) or returns immediately.

**A failure cannot be ignored** (since 0.15.1). After every call to a Fly function the caller checks the struct: if the callee failed, the caller stops right there and takes the same exit its own `fail` would take — out of the enclosing `handle`, or a return to *its* caller. So the error travels up the call stack, one frame at a time, until a `handle` stops it; an error nobody handles ends the program from `main`.

#### 9.7.1 The Error Type

The `error` type is a built-in type that holds the result of a `fail`. Internally it is:

```
%error = type { i32 code, ptr str_ptr, ptr obj_ptr }
```

- **`code`** — integer code. Non-zero means an error occurred.
- **`str_ptr`** — pointer to an error string (null if none).
- **`obj_ptr`** — pointer to an error object (null if none).

From Fly code you declare an error variable and test it with `if`:

```fly
error err           // declare
// …
if error { /* error occurred */ }
```

#### 9.7.2 Fail Statement

`fail` signals an error. It accepts zero, one, two, or three comma-separated arguments (integer, string, and/or object instance, in any order and combination, up to one of each).

**Syntax:**
```
FailStmt ::= 'fail' [ Expr [ ',' Expr [ ',' Expr ] ] ]
```

**Forms:**
```fly
fail                        // code = 1, no message, no object

fail 404                    // code = 404
fail "file not found"       // str  = "file not found", code = 1
fail new MyError()          // obj  = MyError instance, code = 1

fail 404, "not found"       // code = 404, str = "not found"
fail 1, "oops", new Ctx()   // code = 1,   str = "oops", obj = Ctx instance
```

The error **owns** its object: when a `handle` consumes the failure, or `main` reports it, the object is deleted (from 0.16.0; up to 0.15.x it was only dropped from the error, and leaked). In a constructor the object cannot be `this` (from 0.16.0): `'this' cannot be the object of a fail in a constructor`, because a constructor that fails releases the object it was building. When the object's `free()` fails as it is deleted, that is a new failure, after the `handle` (see [A `free()` that fails](#class-heap-allocated-released-with-delete)).

**Behavior of `fail`:**

| Context | What happens |
|---|---|
| Inside a `handle` block (same function) | Writes to error struct; jumps directly to the `safe` block (skips remaining handle body) |
| Outside any `handle` (no enclosing handle in the current function) | Writes to error struct; returns `void` immediately |

Any code after `fail` within the same basic block is unreachable.

The operands are evaluated first, all of them, and only then written to the
error: a message may be any string expression, a call included
(`fail 400, str.concat("bad field: ", name)`). The error keeps its own copy of
the message, so a temporary message is moved into it.

> **0.15.1 and 0.15.2** wrote the code before evaluating the message: when the
> message came from a call (a `+` with a `str.convert(...)` in it included, like
> the `fly.assert` messages), the check after that call saw the code already set
> and the message was lost — `error 1` instead of `error 1: …`. Fixed in 0.16.

```fly
void validate(const int age) {
    if age < 0 {
        fail 400, "age must be non-negative"
        // unreachable
    }
    if age > 150 {
        fail 1001
    }
    // continues here if no fail
}
```

#### 9.7.3 Automatic Error Propagation

Every function (except `main`) has a hidden first parameter: a pointer to the caller's error struct. When a function fails **without** a `handle` in its own body, it writes to that pointer and returns. Right after the call, the caller tests the struct's code; when it is set, the caller stops too:

- inside a `handle` block, it jumps past the block (the handle consumes the error);
- inside a suite `case`, it ends the case (reported as that case's failure);
- otherwise it frees its locals and returns, leaving the error for its own caller.

```fly
void fetchData() {
    fail 503, "service unavailable"   // writes the error, returns
}

void load() {
    fetchData()   // failed: load returns here, the error goes on up
    parse()       // NOT reached
}

void main() {
    load()        // failed: main stops here
    // main() prints "error 503: service unavailable" to stderr
    // and exits with code 503
}
```

Nothing has to be written for this: no annotation on the function, no marker at the call. The check is a load and a branch after the call, and costs nothing else on the success path. Calls into C (the `fly.bridge` bridge) and the runtime intrinsics take no error pointer and are not checked.

> **Before 0.15.1** a failing callee returned and the caller went on with the next statement, so an error could be silently ignored. Code that relied on that now stops at the call; to go on after a failure on purpose, put the call in a `handle`.

#### 9.7.4 Handle Statement

`handle` creates a guarded region. Failures raised inside it — directly or in
a callee — are **consumed** by the handle instead of reaching the caller.
After the block, the implicit **`error`** variable tells whether the guarded
region recorded a failure.

**Syntax:**
```
HandleStmt ::= 'handle' Block
```

**How it works:**

The compiler emits two LLVM basic blocks for each `handle`:
- **`handle`** — the guarded code
- **`safe`** — the continuation (code after the handle)

When `fail` fires **directly inside the handle body** (same function), execution jumps to `safe`, skipping the rest of the handle body. When `fail` fires **in a callee** (at any depth), the callee returns, and the call inside the handle body jumps to `safe` the same way: the rest of the body is skipped.

**The scopes a failure leaves are cleaned up** (from 0.16.0). On its way to `safe` the failure releases the locals of every scope it leaves — the handle body and the blocks nested in it, innermost first — as a `return` does: a `new unique` owner is deleted (its `free()` runs), a `new shared` holder lets go of its reference, strings and arrays are released. The same holds for a failing `new` and an index out of range. Only the locals declared before the failure count; one whose own initializer failed holds nothing and is skipped. Up to 0.15.x those locals were left behind: the objects were reported at the end of `main`, the strings and arrays leaked.

```fly
handle {
    Res r = new unique Res()
    int v = boom()          // fails: r is deleted, then execution goes on after the handle
}
```

On exit the handle **consumes** the error: the failure never propagates to
the caller, and the implicit `error` variable — automatically in scope after
any handle, no declaration needed — holds the outcome. It reads as a boolean:
`true` when the guarded block recorded a failure, `false` otherwise. Each
`handle` opens a fresh window: a later handle that stays clean resets `error`
to `false`.

The handle consumes the **whole** failure: its code, its message (freed) and its
object (deleted from 0.16.0: the error owns it; up to 0.15.x it was only dropped), and
the failures attached to it — those raised by a `free()` while it was in flight, from
0.16.0 (see [A `free()` that fails](#class-heap-allocated-released-with-delete)). A
`free()` that fails as the handle deletes one of those objects is a new failure, which
goes on after the handle as if a statement there had failed. A later
failure starts empty — up to 0.15.2 a handled failure's message stayed behind,
and a later `fail` without a message, or with one built by a call, was reported
with the old text.

**Forms:**

**1. Detect and recover:**
```fly
void main() {
    handle {
        riskyOperation()
    }
    if error {
        // failure recorded — take the fallback path
        return
    }
    // success path
}
```

**2. Discard — swallow any failure:**
```fly
void main() {
    handle {
        riskyOperation()
        anotherOp()
    }
    // execution always reaches here; a failure was consumed silently
}
```

**3. Nested handles — the innermost intercepts first:**
```fly
void process() {
    handle {
        handle {
            deepOp()      // a failure here sets the INNER window
        }
        if error {
            fail          // re-raise to the outer handle
        }
        followupOp()
    }
    if error {
        // handle the top-level failure
    }
}
```

> **Legacy form.** Older code may name the variable explicitly —
> `error err handle { … }` followed by `if (err)`. The unnamed form with the
> implicit `error` variable is the canonical syntax; the named form is
> deprecated and will be removed.

#### 9.7.5 Complete Examples

**Example 1: Propagation without handle**
```fly
void openFile(const string path) {
    if path == "" {
        fail 400, "empty path"
    }
}

void main() {
    openFile("")       // fails with 400: main stops here
    openFile("/tmp")   // NOT called
    // main prints "error 400: empty path" and exits with code 400
}
```

**Example 2: Intercepting with handle**
```fly
void openFile(const string path) {
    if path == "" { fail 400, "empty path" }
}

void main() {
    handle {
        openFile("")       // fails: the handle body stops here
        openFile("/tmp")   // NOT called
    }
    if error {
        // the 400 failure was recorded and consumed here
    }
    openFile("/tmp")       // runs: the error did not get past the handle
}
```

**Example 2b: Going on after a failure, on purpose**
```fly
void main() {
    handle { openFile("") }      // a failure here is expected and dropped
    handle { openFile("/tmp") }  // runs whatever happened above
}
```

**Example 3: Direct fail in handle — jumps immediately**
```fly
void main() {
    handle {
        if someCondition {
            fail 500    // jumps directly to safe block
        }
        neverReached()  // skipped when fail fires above
    }
    if error { /* the failure (code 500) was recorded */ }
}
```

**Example 4: Re-raise to caller**
```fly
void inner() {
    fail 503
}

void outer() {
    handle {
        inner()
    }
    if error {
        fail    // re-raise; outer's caller sees the error
    }
}
```

**Example 5: Object payload**
```fly
class NetError {
    int code
    string host
}

void connect(const string host) {
    NetError e = new NetError()
    e.code = 503
    e.host = host
    fail e
}
```

#### 9.7.6 The Main Function and Exit Codes

`main()` allocates its own error struct. On exit, the compiler emits:

```
ret i32 load(error.code)
```

So the process exit code equals the error code of the last unhandled failure — `0` means clean exit. See [Section 5.5](#5-5-the-main-function) for the full behaviour table.

After the error is printed, each failure attached to it is printed on a line of its own (`  also: error 43: free`, from 0.16.0: a `free()` that failed while the error was in flight, [§6.6](#class-heap-allocated-released-with-delete)); then its message and object are released, and the end-of-`main` report lists the objects the program never deleted (from 0.16.0, [§6.6](#objects-never-deleted-the-report-at-the-end-of-main)). A `free()` that fails in that release sets the exit code when `main` ended cleanly, and is reported as attached when it failed.

#### 9.7.7 Key Differences from try-catch

| Feature | Fly `fail`/`handle` | Traditional `try`/`catch` |
|---|---|---|
| Signal error | `fail` | `throw` |
| Intercept | `handle { }` | `try { } catch { }` |
| Payload | int, string, or object (comma-separated, up to one each) | typed exception object |
| Stack unwinding | **No** — each frame returns normally (locals freed), one after the other | **Yes** — stack frames are unwound by the runtime |
| Callee fail bypasses rest of caller? | **Yes** (since 0.15.1) | **Yes** |
| Error propagation | automatic through the hidden pointer parameter, until a `handle` | automatic until caught |
| Overhead | one load + branch after each call; ordinary returns on failure | zero-cost on success; unwinder on failure |

---

### 9.8 Testing Constructs

Fly has built-in testing support based on three constructs: inline `test` blocks, `suite` declarations, and `case` labels. These are compiled **only when the compiler runs in test mode** (the `--test` driver flag, also implied by `--suite`, which additionally builds and runs the suite). Outside test mode, `test` blocks are stripped during semantic analysis and have no effect on the produced binary. See the **Testing** guide for the full runner, `--suite=Name`/`--test=Method` filters, and assertion reference.

#### 9.8.1 Inline `test` Block

A `test { … }` block embeds test-only code inside ordinary functions. The body is parsed normally but is included in code generation only under `--test`.

**Syntax:**
```
TestStmt ::= 'test' Block
```

```fly
void compute() {
    int result = doWork()

    test {
        // Only runs when compiled with --test
        assert.assertEqI(result, 42, 1)
    }
}
```

#### 9.8.2 `suite` Declaration

A `suite` is a declaration that groups related tests. It is declared like a class (same modifiers and member syntax) but uses the `suite` keyword and has **no constructors**.

**Syntax:**
```
Suite ::= [ Modifiers ] 'suite' Identifier '{' SuiteMember* '}'
```

```fly
public suite MathTests {

    void additionsTest() {
        case "adds positives": {
            assert.assertEqI(add(2, 3), 5, 1)
        }
        case "adds negatives": {
            assert.assertEqI(add(-2, -3), -5, 2)
        }
    }
}
```

A method whose name ends in `Test` is a test method and runs automatically;
`setup()` and `teardown()` run once before and after them; any other method is a
helper. See the [Testing](@/docs/Fly-Testing.md) guide.

#### 9.8.3 `case` Label (inside a suite method)

Inside a suite test-method body, a standalone `case "label": …` statement names an individual test scenario. Consecutive `case` statements in the same block each describe a labelled scenario. (The same `case` keyword is also used inside `switch` statements — see [§9.4](#9-4-switch-statements).)

**Syntax:**
```
SuiteCase ::= 'case' StringLiteral ':' Statement
```

> The standard library, the runtime, the compiler and the tools are tested this way: every `*Suite.fly` under `std/test`, `compiler/test` and `tools/*/test` is a `suite` run by `fly --suite` in the bootstrap's test stages.

---

## 10. Namespaces and Imports

### 10.1 Namespace Declaration

Namespaces organize code and prevent name conflicts.

**Syntax:**
```
Namespace ::= 'namespace' Identifier ( '.' Identifier )*
```

**Examples:**
```fly
// Single namespace
namespace mylib

// Nested namespace (dotted notation)
namespace my.library

namespace company.project.module
```

**Rules:**
- A namespace declaration must appear before any imports or top-level declarations
- Only one namespace declaration per file
- If no namespace is declared, a default namespace based on the filename is used: each such file is a namespace of its own, so what it declares is not visible from another file, and two files may declare a class, a function or an enum of the same name, each getting its own. From 0.16.0 the compiled program keeps them apart too (the generated symbols carry the file's base name); up to 0.15.x they shared their symbols, and an object could be initialized or released as the other file's class. Two such files with the same base name in different directories still share it: give a file a `namespace` when that can happen.
- **A namespace is one scope spread over files.** The files that declare the same `namespace` see each other's classes, functions and enums without an `import`, by their bare names (`Counter`, `twice(3)`), or fully qualified (`probe.samens.twice(3)`). The compiler reads those files from the project's sources (the current directory or each `--src-dir`) because the namespace is the file's own, as it reads the files of an imported namespace. An explicit `import` of a symbol of the same namespace stays legal. A short prefix of the file's own namespace (`samens.twice(…)` inside `namespace probe.samens`) is not: a prefix comes from an import of another namespace. Up to 0.15.x (the v0.15.2 seed included) the other files of the namespace were read only when something imported it, so a file used a sibling's class only after `import probe.samens.Counter`.

```fly
// parts.fly
namespace probe.samens

public int twice(const int n) {
    return n * 2
}
```

```fly
// main.fly
namespace probe.samens

void main() {
    int t = twice(3)        // no import: same namespace
}
```

- **One definition per name in a namespace.** Two functions of one namespace with the same name and the same parameter types are an error from 0.16.0, whether they are in one file or in two: the error is on the second, with a note at the first. Two types of the same name (class, struct, interface or enum, in any mix) in one namespace are an error in the same way. The files without a `namespace` are checked one file at a time, since each is a namespace of its own. Overloads stay legal: they differ in their parameter types. Two classes or enums with the same short name in two namespaces (`probe.left.P`, `probe.right.P`) are two types, so `h(probe.left.P)` and `h(probe.right.P)` are two overloads, as are `k(Box<probe.left.P>)` and `k(Box<probe.right.P>)`, and each call picks its own; up to 0.15.x types were compared by their short name and the second `h` was reported as already defined. Up to 0.15.x (the v0.15.2 seed included) only two functions in one file were reported; across files the program compiled and which body ran depended on the link order, and a type declared twice was taken without a word.

### 10.2 Import Declaration

Imports make symbols from other namespaces available. Fly supports four import forms, all modelled on Java-style imports.

**Syntax:**
```
Import ::= 'import' Name ( '.' Name )*                           // namespace import
         | 'import' Name ( '.' Name )* '.' '*'                   // wildcard import
         | 'import' Name ( '.' Name )* 'as' Name ( '.' Name )*  // alias import
```

#### 10.2.1 Namespace import

Brings the last namespace segment into scope. Access symbols with the segment prefix.

```fly
import fly.str          // 'str' is in scope
import fly.os.time      // 'time' is in scope

void main() {
    int n   = str.len("hello")    // qualified access
    Time t  = time.now()
}
```

A namespace import binds **only the prefix** — it does not bring the namespace's
members into scope, so bare calls are an error:

```fly
import fly.str

void main() {
    int n = len("hello")    // → compile error: no function 'len' in scope
                            //   (use str.len(...), or import fly.str.*)
}
```

#### 10.2.2 Class import (Java style)

When the last component of the path names a **class** (or enum/struct), that type is placed directly in the current scope — no prefix needed. This is the Fly equivalent of Java's `import java.util.List`.

```fly
import fly.data.List    // 'List' class is in scope
import fly.data.Stack   // 'Stack' class is in scope

void main() {
    List<int>  l = new List<int>()    // no 'data.' prefix needed
    Stack<int> s = new Stack<int>()
}
```

There is no coupling between the filename and the class name. The import path navigates the **namespace hierarchy**; the filename is irrelevant. By convention, stdlib files use the capitalized class name (`list.fly` → `List`), but this is optional for user code.

#### 10.2.3 Wildcard import

`.*` brings **all public symbols** (classes, enums, structs, functions) declared directly in the target namespace into the current scope. The target must be a namespace — using `.*` on a class or function is a compile-time error.

```fly
import fly.data.*       // List, Stack, Queue, Deque, Map, Set, Tree all in scope

void main() {
    List<int>        l = new List<int>()
    Map<string, int> m = new Map<string, int>()
}
```

```fly
// Error: fly.data.List is a class, not a namespace
import fly.data.List.*  // → compile error: wildcard requires a namespace target
```

A wildcard import brings in the **symbols**, never the namespace prefix — the
two forms are complementary, not overlapping:

```fly
import fly.str.*

void main() {
    int n = len("hello")        // OK: bare call, 'len' is in scope
    int m = str.len("hello")    // → compile error: 'str' is not defined in this
                                //   scope (a wildcard does not bind the prefix;
                                //   add `import fly.str` for prefixed calls)
}
```

#### 10.2.4 Alias import

Binds the imported namespace or symbol under a different local name. Cannot be combined with wildcard (`.*`). An aliased class or function is in scope under the alias only (`import geom.Circle as C` … `C c = new C()`); up to 0.15 the alias of a class or function import was ignored.

```fly
import fly.str as s     // 's' is in scope
import fly.data.List as L

void main() {
    int n = s.len("hello")
    L<int> myList = new L<int>()
}
```

### 10.3 Using Imported Symbols

**Full example:**
```fly
// File: shapes.fly
namespace geom

public class Circle {
    public int radius
}

public int area(const int r) {
    return r * r
}
```

```fly
// File: main.fly — class import (Java style)
import geom.Circle

void main() {
    Circle c = new Circle()
    c.radius = 5
}
```

```fly
// File: main.fly — namespace import
import geom
import geom.Circle

void main() {
    int a = geom.area(5)    // functions: through the prefix
    Circle c = new Circle() // types: through a class import
}
```

The namespace prefix is for calls only: a type is never written with it
(`geom.Circle c` is an error). A type comes into scope through a class import,
a wildcard import or an alias import.

```fly
// File: main.fly — wildcard import
import geom.*

void main() {
    Circle c = new Circle()
    int a = area(5)      // function in scope too
}
```

---

### 10.4 Reserved namespaces

From 0.15.0, two namespaces of the standard library are **reserved to the standard
library and the toolchain** — that is, to modules whose namespace is `fly` or starts
with `fly.`:

- `fly.llvm.*` — compiler intrinsics (raw string and pointer access, atomics, …);
- `fly.runtime.*` — the operating-system primitives under the std.

A call to either from any other module is an error:

```text
main.fly:2:17: error: 'fly.runtime.proc_exit' can only be called from the standard library
(namespace fly.*): use fly.mem or a std function
```

User code reaches raw memory through **`fly.mem`**, and everything else through a std
function that wraps the primitive; what is missing is added to the std as a wrapper.
The unsafe code of a program is therefore found by searching for its calls to
`fly.mem`. Generic templates of the std (`List<T>`, …) keep using `fly.llvm`
internally: their bodies are std code wherever they are instantiated.

---

## 11. Modifiers

### 11.1 Visibility Modifiers

Control the accessibility of declarations.

| Modifier    | Scope                                          | Applies To                    |
|-------------|------------------------------------------------|-------------------------------|
| `private`   | Only within the same file/class                | Functions, classes, members   |
| `protected` | Within the class and every class derived from it | Methods and attributes of a class |
| `public`    | Accessible from anywhere                       | Functions, classes, members   |
| (default)   | Package-private (same namespace)               | Functions, classes            |

**Examples:**
```fly
// Private function
private void internalHelper() {}

// Protected member
class Base {
    protected int value
}

// Public class
public class PublicAPI {
    public void exportedMethod() {}
}

// Default visibility
void packageFunction() {}
class DefaultClass {}
```

**Member access, checked by the compiler.** A `private` member (field, method,
constructor, static or not) is reachable only from the methods of the class that
declares it. A `protected` member is reachable from the methods of that class and
of every class derived from it, directly or not. The rule looks only at *where*
the code is, never at the receiver: inside a subclass, `this.v`, `super.v`, `o.v`
on another instance of the subclass and `b.v` on a plain `Base` are all allowed.
A `protected` constructor can be called with `super(...)` and with `new` from a
subclass only.

```fly
class Base {
    protected int v
    protected Base(const int v) {
        this.v = v
    }
}

class Derived : Base {
    public Derived() {
        super(1)
    }
    public int twice(const Base other) {
        return this.v + other.v     // ok: Derived derives from Base
    }
}

void main() {
    Derived d = new unique Derived()
    int x = d.v                     // error: 'v' is protected in 'Base'
}
```

`protected` is written only on the methods and attributes of a class. On a
function, a parameter or local variable, a type, or a member of an interface,
struct or suite it is an error: none of them has subclasses to open a member to.

### 11.2 Constant Modifier

The `const` modifier marks values as immutable.

> **Status:** 0.14.6 does not reject an assignment to a `const` variable or parameter; 0.14.7 does — see [Conformance](@/docs/Fly-Conformance.md). `const` protects the variable itself, not the fields of an object it refers to.

**Examples:**
```fly

// Constant function parameter — const is optional on parameters,
// and marks the parameter read-only inside the body
void process(const int size) {
    // size cannot be modified
}

// Constant local variable
void func() {
    const int limit = 50
    // limit = 100  // not allowed: limit is const
}
```

### 11.3 Static Modifier

The `static` modifier creates class-level members.

**Examples:**
```fly
class Counter {
    static int totalCount = 0
    
    public static int getTotal() {
        return totalCount
    }
    
    public void increment() {
        totalCount++
    }
}

void main() {
    Counter c = new Counter()
    c.increment()
    int total = Counter.getTotal()   // total = 1
}
```

### 11.4 Combining Modifiers

Multiple modifiers can be combined.

**Examples:**
```fly
// In a class context
class Configuration {
    // Public constant (class-level)
    public const int BUFFER_SIZE = 1024
    
    // Private static field
    private static int instanceCounter = 0
    
    // Public static constant
    public static const string APP_NAME = "FlyApp"
}

// In a function
void process() {
    // Constant local variable
    const int maxRetries = 3
}
```

### 11.5 Abstract and Final Modifiers

Two further modifiers apply mainly to type and method declarations:

| Modifier   | Meaning                                                        |
|------------|---------------------------------------------------------------|
| `abstract` | The declaration is incomplete and must be implemented by a subtype (e.g. an abstract class or method). |
| `final`    | The declaration cannot be further extended or overridden.     |

**Examples:**
```fly
// Abstract class — cannot be instantiated directly
public abstract class Shape {
    public abstract int area()
}

// Final class — cannot be subclassed
public final class Vector2 {
    int x
    int y
}
```

---

## 12. Comments

### 12.1 Line Comments

Line comments start with `//` and continue to the end of the line. A newline
always terminates a line comment: Fly has **no line-splicing**, so a backslash
at the end of the line is an ordinary comment character and does **not**
continue the comment onto the next line.

**Examples:**
```fly
// This is a line comment
int value = 42  // End-of-line comment

// Multiple line comments
// can be used for
// multi-line documentation

// A trailing backslash does not extend this comment \
int next = 1    // this line is code, not comment
```

### 12.2 Block Comments

Block comments are enclosed between `/*` and `*/`.

**Examples:**
```fly
/* This is a block comment */

/*
 * Multi-line block comment
 * for detailed documentation
 */

void calculate() {
    /* inline comment */ return
}
```

**Note:** Block comments can span multiple lines and are preserved by the parser for documentation purposes. A block comment ends only at a literal `*/`: since Fly has no line-splicing, a backslash-newline between `*` and `/` does not terminate the comment.

### 12.3 No Line Splicing

Unlike C and C++, Fly performs **no line-splicing** anywhere: a backslash
followed by a newline is never a line continuation. Inside comments it is
ordinary comment text; outside comments and string literals a stray backslash
is a lexical error.

---

## 13. Grammar Summary

### 13.1 Program Structure

```
Program         ::= [ Namespace ] Import* TopDecl*

Namespace       ::= 'namespace' Name ( '.' Name )*

Import          ::= 'import' Name ( '.' Name )*
                  | 'import' Name ( '.' Name )* '.' '*'
                  | 'import' Name ( '.' Name )* 'as' Name ( '.' Name )*

TopDecl         ::= Comment 
                  | ClassDecl 
                  | EnumDecl 
                  | FunctionDecl

Modifiers       ::= ( 'public' | 'private' | 'protected'
                    | 'const' | 'static' | 'abstract' | 'final' )*
```

### 13.2 Type System

```
Type            ::= BuiltinType 
                  | NamedType 
                  | ArrayType

BuiltinType     ::= 'void' | 'bool' | 'byte' 
                  | 'short' | 'ushort' | 'int' | 'uint' 
                  | 'long' | 'ulong' | 'float' | 'double' 
                  | 'string' | 'error'

NamedType       ::= Name ( '.' Name )*

ArrayType       ::= Type '[' [ Expression ] ']'
```

### 13.3 Declarations

```
ClassDecl       ::= Modifiers ( 'class' | 'struct' | 'interface' | 'suite' )
                    Identifier [ '<' TypeParam ( ',' TypeParam )* '>' ]
                    [ ':' BaseType ( ',' BaseType )* ] '{' ClassMember* '}'

BaseType        ::= NamedType

TypeParam       ::= Identifier [ ':' Type ]

GenericFunc     ::= Modifiers ReturnType Identifier
                    '<' TypeParam ( ',' TypeParam )* '>'
                    '(' [ ParamList ] ')' Block

InterfaceDecl   ::= Modifiers 'interface' 
                    Identifier [ ':' Identifier ] '{' InterfaceMember* '}'

EnumDecl        ::= Modifiers 'enum' Identifier '{' EnumEntryList '}'

EnumEntryList   ::= EnumEntry ( ',' EnumEntry )*

EnumEntry       ::= [ Modifiers ] Identifier

FunctionDecl    ::= Modifiers ReturnType Identifier
                    [ '<' TypeParam ( ',' TypeParam )* '>' ]
                    '(' [ ParamList ] ')' ( Block | ';' )

ReturnType      ::= Type ( ',' Type )*

ParamList       ::= Param ( ',' Param )*

Param           ::= [ Modifiers ] Type Identifier [ '=' Value ]
```

Notes:
- `ReturnType` is **mandatory** (use `void` for no value). Constructors — a
  method named like its class — and interface methods are the only declarations
  that omit it.
- A class may list **one or more** base types after `:` (a base struct and/or
  interfaces). Structs extend only structs, interfaces extend only interfaces,
  and enums cannot extend anything.

### 13.4 Statements

```
Statement       ::= Block 
                  | IfStmt 
                  | SwitchStmt 
                  | WhileStmt 
                  | ForStmt
                  | ForInStmt
                  | ReturnStmt 
                  | BreakStmt 
                  | ContinueStmt 
                  | FailStmt 
                  | HandleStmt
                  | TestStmt
                  | ExprStmt 
                  | VarDeclStmt 
                  | AssignStmt
                  | MultiAssignStmt

Block           ::= '{' Statement* '}'

IfStmt          ::= 'if' Expr Statement
                    ( 'elsif' Expr Statement )*
                    [ 'else' Statement ]

SwitchStmt      ::= 'switch' Expr '{'
                    CaseClause* [ DefaultClause ] '}'

CaseClause      ::= 'case' Expr ( ',' Expr )* ':' Statement+

WhileStmt       ::= 'while' Expr Statement

ForStmt         ::= 'for' VarDecl ( ',' VarDecl )* ';' Expr ';'
                    Expr ( ',' Expr )* Statement

ForInStmt       ::= 'for' Identifier 'in' Expr Statement

ReturnStmt      ::= 'return' [ Expr { ',' Expr } ]   (* one value per declared
                       return type; the first must start on the keyword's line *)

BreakStmt       ::= 'break'

ContinueStmt    ::= 'continue'

FailStmt        ::= 'fail' [ Expr [ ',' Expr [ ',' Expr ] ] ]

HandleStmt      ::= 'handle' Block          (* implicit `error` var after the block; the
                                               named `error Ident handle` form is legacy *)

TestStmt        ::= 'test' Block

VarDeclStmt     ::= Modifiers Type Identifier [ '=' Expr ]

AssignStmt      ::= Identifier AssignOp Expr

MultiAssignStmt ::= Identifier ( ',' Identifier )+ '=' CallExpr
                    (* every receiver is an ALREADY-DECLARED variable; the
                       call's return slots bind in order — see §5.4 *)
```

### 13.5 Expressions

The grammar below reflects the compiler's **flat** precedence (six binary
levels). See [Appendix B](#appendix-b-operator-precedence) for the ordering and
the important note that bitwise/shift bind *looser* than comparisons.

```
Expression      ::= AssignExpr

AssignExpr      ::= TernaryExpr ( AssignOp AssignExpr )?

TernaryExpr     ::= LogicalExpr [ '?' Expr ':' Expr ]

// One level: logical, bitwise and shift operators together
LogicalExpr     ::= RelationalExpr
                    ( ( '||' | '&&' | '|' | '&' | '^' | '<<' | '>>' ) RelationalExpr )*

// One level: equality and relational operators together
RelationalExpr  ::= AddExpr
                    ( ( '==' | '!=' | '<' | '>' | '<=' | '>=' ) AddExpr )*

AddExpr         ::= MultExpr ( ( '+' | '-' ) MultExpr )*

MultExpr        ::= UnaryExpr ( ( '*' | '/' | '%' ) UnaryExpr )*

UnaryExpr       ::= ( '++' | '--' | '!' | '-' | '+' ) UnaryExpr 
                  | PostfixExpr

PostfixExpr     ::= PrimaryExpr ( '++' | '--' | '(' ArgList ')' 
                  | '[' Expr ']' | '.' Identifier )*

PrimaryExpr     ::= Literal 
                  | Identifier 
                  | '(' Expr ')' 
                  | 'new' NamedType [ '<' TypeArg ( ',' TypeArg )* '>' ] '(' ArgList ')'
                  | ArrayValue
                  | StructValue

ArrayValue      ::= '{' [ Expr ( ',' Expr )* ] '}'

StructValue     ::= '{' [ Identifier '=' Value ( ',' Identifier '=' Value )* ] '}'

Literal         ::= NumericLiteral | StringLiteral
                  | 'true' | 'false' | 'null'

AssignOp        ::= '=' | '+=' | '-=' | '*=' | '/=' | '%=' 
                  | '&=' | '|=' | '^=' | '<<=' | '>>='
```

---

## 14. Complete Example

Here's a comprehensive example demonstrating various Fly language features:

```fly
namespace myapp

import utils
import data.models as models


// Enum declaration (enums cannot extend anything)
public enum Status {
    IDLE, RUNNING, PAUSED, STOPPED
}

// Class declaration
public class Application {
    // Private fields
    private string name
    private int value
    private Status currentStatus
    
    // Static field
    static int instanceCount = 0
    
    // Constructor — same name as the class, no return type
    public Application(const string appName) {
        this.name = appName
        this.value = 0
        this.currentStatus = Status.IDLE
        instanceCount++
    }
    
    // Method with return type — `return` carries the result
    public int getValue() {
        return this.value
    }
    
    // Public void method with error handling
    public void process() {
        handle {
            this.calculateResult()
        }
        
        if error {
            // Error occurred
            this.currentStatus = Status.STOPPED
        }
    }
    
    // Private helper method that may fail
    private void calculateResult() {
        if this.value < 0 {
            fail "Invalid value"     // Fail with string message
        }
        if this.value > 1000 {
            fail 999                 // Fail with error code
        }
    }
    
    // Method demonstrating void error handling
    public void validate() {
        error validationErr handle {
            if this.name == "" {
                fail "Name cannot be empty"
            }
        }
        
        if validationErr {
            this.currentStatus = Status.STOPPED
        }
    }
    
    // Setter method
    public void setValue(const int newValue) {
        this.value = newValue
    }
    
    // Static method
    public static void incrementCount() {
        instanceCount++
    }
}

// Structure declaration (struct can extend only struct)
public struct Point {
    int x
    int y
}

// Struct extending another struct
public struct Point3D : Point {
    int z
}

// Interface declaration (interface can extend only interface)
public interface Drawable {
    void draw()
}

// Class implementing an interface
public class Shape : Drawable {
    private int width
    private int height
    
    public void draw() {
        // drawing logic
    }
}

// Main entry point
// Note: main() automatically returns 0 if all errors are handled,
// or returns 1 if an unhandled error occurs
void main() {
    // Create application instance (constructor invoked by new)
    Application app = new Application("MyApp")
    
    // Error handling example: validate the application
    handle app.validate()
    
    // Set status
    Status status = Status.RUNNING
    
    // Control flow with error handling
    if status == Status.RUNNING {
        error processErr handle {
            app.process()
        }
        
        if processErr {
            // Handle error gracefully
            status = Status.STOPPED
        } else {
            handleResult()
        }
    } elsif status == Status.PAUSED {
        // Handle paused state
    } else {
        // Handle other states
    }
    
    // Loop through array
    int[] numbers = {1, 2, 3, 4, 5}
    for int i = 0; i < 5; i++ {
        processNumber(numbers[i])
    }
    
    // While loop
    int count = 0
    while count < 10 {
        count++
    }
    
    // Switch statement
    switch count {
        case 10:
            // count is 10
            break
        default:
            // other value
    }
    
    // Create structure (stack-allocated)
    Point p = new Point()
    p.x = 10
    p.y = 20
    
    // Error handling with structure
    error distErr handle {
        int dist = p.x * p.x + p.y * p.y
        if dist > 1000 {
            fail "Distance too large"
        }
    }
}

// Private void helper function
private void handleResult() {
    // handle result logic
}

// Function with const parameter
private void processNumber(const int num) {
    if num % 2 == 0 {
        // even number
    } else {
        // odd number
    }
}
```

---

## 15. Best Practices

### 15.1 Naming Conventions

- **Classes, Structs, Enums**: Use PascalCase (e.g., `MyClass`, `StatusType`)
- **Functions, Variables**: Use camelCase (e.g., `calculateTotal`, `userName`)
- **Constants**: Use UPPER_SNAKE_CASE (e.g., `MAX_SIZE`, `DEFAULT_VALUE`)
- **Private members**: Prefix with underscore or use clear naming (e.g., `_internal`, `privateHelper`)

### 15.2 Code Organization

- One namespace per file
- Group related functionality in the same namespace
- Use imports to reference external code
- Keep functions focused and small

### 15.3 Error Handling

- Use `fail` for unrecoverable errors
- Use `handle` blocks to catch and recover from errors
- Validate inputs at function boundaries

### 15.4 Comments

- Use line comments for brief explanations
- Use block comments for detailed documentation
- Document public APIs thoroughly
- Explain complex algorithms and business logic

---

## Appendix A: Reserved Keywords

All keywords are reserved and cannot be used as declared names (variables, parameters,
fields, methods, functions, classes) — enforced from 0.15.0:

```
abstract    as          bool        break       byte
case        class       complex     const       continue
default     delete      double      else        elsif
enum        error       fail        false       final
float       for         handle      if          import
in          int         interface   long        namespace
new         null        pointer     private     protected
public      return      short       static      string
struct      suite       switch      test        true
uint        ulong       ushort      void        while
```

> `this` is **not** a reserved keyword (see [Section 2.1](#2-1-keywords)); `delete` is one from 0.15.0. Neither is `out`, which is now an ordinary identifier — the standard library still uses it as a parameter name for C-ABI output parameters. The ownership qualifiers `unique`, `shared` and `weak` are contextual: they mean something only after `new` (`unique`, `shared`) or in front of a holder (`weak h`), and remain usable as names.

---

## Appendix B: Operator Precedence

Fly uses a **flat** precedence scheme with only six binary levels. From highest to lowest precedence (tightest to loosest binding):

1. **Primary / postfix / unary**: literals, identifiers, calls `()`, subscript `[]`, member `.`, then prefix/postfix `++` `--`, `!`, unary `-`. Postfix `++`/`--` bind **same-line only**: a leading `++`/`--` on the next line is a new prefix statement, never the previous operand's postfix.
2. **Multiplicative**: `*`, `/`, `%`
3. **Additive**: `+`, `-`
4. **Relational & Equality** (one level): `==`, `!=`, `<`, `>`, `<=`, `>=`
5. **Logical, Bitwise & Shift** (one level): `||`, `&&`, `|`, `&`, `^`, `<<`, `>>`
6. **Ternary**: `?:`
7. **Assignment** (right-associative): `=`, `+=`, `-=`, `*=`, `/=`, `%=`, `&=`, `|=`, `^=`, `<<=`, `>>=`

> ⚠️ **Differs from C/C++/Java.** Bitwise (`&` `|` `^`) and shift (`<<` `>>`) operators sit at the *same, looser* level as the logical operators — below the comparison operators. Read the C way, `a & b == c` would be `(a & b) == c`; in Fly it is `a & (b == c)`.
>
> **From 0.15.0 the ambiguous forms are compile-time errors**, so the reading is always written out:
>
> - two **different** operators of level 5 chained without parentheses — `a || b && c`, `x << 2 | y`, `a & b | c` — are rejected: write `(a || b) && c` or `a || (b && c)`. The same operator repeated (`a && b && c`, `m & n & 7`) is fine;
> - a **comparison as the operand of a bitwise or shift operator** — `flags & mask == 0`, `a == b & c` — is rejected: write `(flags & mask) == 0`. Comparisons joined by `&&` or `||` (`a == b && c < d`) mean the same in every language and stay legal.
>
> ```
> main.fly:4:14: error: '&' binds looser than the comparison '==' in Fly ('a & b == c' means 'a & (b == c)'): add parentheses
> ```

---

## Appendix C: Error Handling Quick Reference

Fly uses `fail` and `handle` keywords for error handling, which differs from traditional try-catch mechanisms.

### Quick Comparison

| Concept | Fly Syntax | Traditional (Java/C++) |
|---------|------------|------------------------|
| Throw exception | `fail` | `throw` |
| Throw with message | `fail "Error message"` | `throw new Exception("Error message")` |
| Throw with code | `fail 404` | `throw 404` or custom exception |
| Catch exception | `handle { ... }` | `try { ... } catch { ... }` |
| Inspect the outcome | `handle { ... }` then `if (error)` | `catch (Exception err) { ... }` |
| Error type | `error` | `Exception` or custom class |

### Common Patterns

```fly
// Pattern 1: Simple fail
void operation() {
    fail                        // Throw exception
}

// Pattern 2: Fail with integer code
void check() {
    fail 404                    // Error code
}

// Pattern 3: Fail with string message
void load() {
    fail "File not found"       // Error message
}

// Pattern 4: Simple handle
handle operation()              // Catch and ignore

// Pattern 5: Handle with error capture
handle {
    riskyOperation()
}
if error {
    // Handle error
}

// Pattern 6: Handle with recovery
handle {
    computation()
}
if error {
    fallbackOperation()
}
```

### Error Types

- **void**: `fail` (no value)
- **integer**: `fail 404`, `fail 500`, `fail -1`
- **string**: `fail "Error message"`, `fail "Not found"`
- **object**: `fail errorObject`

### Key Points

1. **`fail`** immediately terminates function execution
2. **`handle`** catches exceptions in the enclosed block
3. **`error`** type stores exception information
4. Multiple operations can be wrapped in a single `handle` block
5. Error handling is more concise than traditional try-catch
6. No exception type hierarchy needed—use simple values
7. **`main()` function:** Unhandled errors cause the application to return exit code 1; handled errors allow return code 0

### Main Function and Exit Codes

The `main()` function has special error handling behavior:

```fly
void main() {
    // If no error occurs or all errors are handled: returns 0
    // If an unhandled error occurs: returns 1
}
```

**Examples:**

```fly
// Returns 0 (success)
void main() {
    handle mayFail()
}

// Returns 1 (failure)
void main() {
    mayFail()  // Error not handled
}

// Returns 0 (success) - error is caught and handled
void main() {
    handle {
        riskyOperation()
    }
    if error {
        // Handle gracefully
    }
}
```

---

**© Fly Project - https://flylang.org**  
**Licensed under Apache License v2.0**  
**Documentation Version 1.1 - June 2026** — revised to match the compiler (Parser / AST / Resolver): mandatory return types, constructors & `this`, for-in loops, generics with bounds, testing constructs, automatic memory management, and corrected operator precedence.

