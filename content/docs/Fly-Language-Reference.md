+++
title = "Language Reference"
description = "Complete reference for the Fly programming language — syntax, types, expressions, statements, and grammar."
template = "docs-page.html"
weight = 1
+++

# Fly Language Reference

**Version:** 0.12.3  
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
9. [Statements](#9-statements)
10. [Namespaces and Imports](#10-namespaces-and-imports)
11. [Modifiers](#11-modifiers)
12. [Comments](#12-comments)
13. [Grammar Summary](#13-grammar-summary)

---

## 1. Introduction

Fly is a compiled, high-level, general-purpose programming language with particular attention to simplicity, readability, and multi-paradigm support. Fly is built on LLVM infrastructure and aims to provide optional Garbage Collection.

**Design Principles:**
- **Simple** - Easy to read and write
- **Fast** - Compiled with LLVM for optimal performance
- **Powerful** - Multi-paradigm with modern features

---

## 2. Lexical Elements

### 2.1 Keywords

Fly reserves the following keywords:

```
as          bool        break       byte        case
char        class       const       continue    default
double      elsif       else        enum        error
fail        false       float       for         handle
if          import      interface   int         long
namespace   new         null        out         private
protected   public      return      short       static
string      struct      switch      true        uint
ulong       ushort      while
```

> **`out`** is a special identifier automatically declared inside any function that has a return type. It holds the value to be returned. For functions with multiple return types, use `out[0]`, `out[1]`, … See [Section 5.4](#5-4-return-values).

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

#### 2.3.3 Character Literals

```fly
'a'         // character
'Z'         // uppercase character
'\n'        // newline escape
```

#### 2.3.4 String Literals

```fly
"Hello, World!"
"Fly Language"
""          // empty string
```

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
[]          // array subscript
()          // function call / grouping
{}          // block delimiters
,           // separator
;           // statement terminator (optional)
:           // label/case separator
...         // ellipsis
@           // annotation
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

| Type     | Description                           |
|----------|---------------------------------------|
| `bool`   | Boolean type (true or false)          |
| `char`   | Character type                        |
| `string` | String type                           |
| `error`  | Error type for error handling         |

**Examples:**
```fly
bool isActive = true
char letter = 'A'
string name = "Fly"
```

### 3.2 Array Types

Arrays can be declared with or without explicit size.

**Syntax:**
```
ArrayType ::= Type '[' [ Expression ] ']'
```

**Examples:**
```fly
// Dynamic array (size unspecified)
byte[] dynamicArray
int[] numbers

// Fixed-size array
byte[10] fixedBuffer
int[5] coordinates

// Multi-dimensional arrays
int[][] matrix
byte[][][] cube
```

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
func() {
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

---

## 5. Functions

### 5.1 Function Declaration

Functions and methods may optionally declare a **return type** before the function name. When a return type is present, the special identifier `out` is implicitly declared inside the body and holds the value to be returned. Functions without a return type are void.

**Syntax:**
```
Function    ::= [ Modifiers ] [ ReturnType ] Identifier '(' [ Parameters ] ')' ( Block | ';' )
ReturnType  ::= Type ( ',' Type )*
```

**Examples:**
```fly
// Void function — no return type
doSomething() {
    // function body
}

// Function with a return type — assign to 'out' to return a value
int add(const int a, const int b) {
    out = a + b
}

// Multiple return types — use out[0], out[1], …
int,int minMax(const int a, const int b) {
    if (a < b) {
        out[0] = a
        out[1] = b
    } else {
        out[0] = b
        out[1] = a
    }
}
```

### 5.2 Function Parameters

Input parameters use the `const` modifier and are read-only inside the function body. Output parameters (the traditional multi-output style) omit `const` and are written directly by the function.

**Syntax:**
```
Parameters ::= Parameter ( ',' Parameter )*
Parameter  ::= [ 'const' ] Type Identifier
```

**Examples:**
```fly
// Input-only parameters (const)
process(const int x, const float y, const bool flag) {
    // implementation
}

// Mixed: const inputs + traditional output parameter
clamp(const int value, const int lo, const int hi, int result) {
    if (value < lo) { result = lo }
    elsif (value > hi) { result = hi }
    else { result = value }
}
```

### 5.3 Visibility Modifiers

Functions can have different visibility levels:

```fly
// Default visibility (package-private)
defaultFunction() {}

// Private function (internal use only)
private privateHelper() {}

// Public function (exported)
public publicAPI() {}

// Protected function (for inheritance)
protected protectedMethod() {}
```

### 5.4 Return Values

When a function declares a return type, the special identifier `out` is implicitly available inside the body. Assigning to `out` sets the return value. The caller receives it as if the function returned by value — but the compiler generates a hidden by-reference output parameter, so **no copy is ever made**.

```fly
// Looks like return-by-value to the caller…
int square(const int n) {
    out = n * n
}

void main() {
    int x = square(5)   // x = 25
}
```

This resolves the classic C++ ergonomics/performance tension: in C++ you must choose between `File f = open(path)` (readable, but implies a copy) or `open(path, &f)` (efficient, but noisy). Fly does both with the same syntax — the source reads as a normal assignment, and the compiler silently passes `x` by reference to `square`.

**Multiple return types** use `out[0]`, `out[1]`, …:

```fly
int,int divmod(const int a, const int b) {
    out[0] = a / b   // quotient
    out[1] = a % b   // remainder
}

void main() {
    int q = divmod(17, 5)   // q = 3
}
```

**Early exit** in void functions still uses `return` (without a value):

```fly
process(const int x) {
    if (x < 0) {
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

**Error Handling and Return Codes:**

When the application runs, `main()` automatically returns an exit code to the operating system:

- **Return 0:** If no unhandled errors occur (success)
- **Return 1:** If an unhandled error occurs (failure)

This behavior is automatic—you don't explicitly return an integer from `main()`.

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
err0() {
    fail "Something went wrong"
}

void main() {
    err0()  // Error is not handled
    // Automatically returns 1 (failure)
}
```

**Example 3: Handled Error**
```fly
err0() {
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
riskyOperation() {
    fail "Operation failed"
}

void main() {
    error err handle {
        riskyOperation()
    }
    
    if (err) {
        // Error was caught and handled
        // Continue with fallback logic
    }
    // Automatically returns 0 (success)
}
```

**Best Practices:**

1. **Always handle errors in main:** Unhandled errors will cause the application to exit with code 1
2. **Use handle blocks:** Wrap risky operations in `handle` blocks to ensure graceful error handling
3. **Check error variables:** Use `if (err)` to detect and respond to errors appropriately
4. **Provide fallback logic:** When errors occur, provide alternative execution paths

**Summary:**
- `main()` is required (void return type, no parameters)
- Exit code 0 = success (no unhandled errors)
- Exit code 1 = failure (unhandled error occurred)
- Use `handle` to catch errors and ensure successful exit

---

## 6. Classes and Structures

### 6.1 Class Declaration

Classes support **single inheritance**. A class can extend one struct or one interface.

**Syntax:**
```
Class ::= [ Modifiers ] 'class' Identifier [ ':' Identifier ] '{' ClassMember* '}'
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

// Class extending an interface
class MyImpl : Drawable {
}
```

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

### 6.3 Interface Declaration

Interfaces define contracts for classes. An interface can only extend another interface.

**Syntax:**
```
Interface ::= [ Modifiers ] 'interface' Identifier [ ':' Identifier ] '{' InterfaceMember* '}'
```

**Examples:**
```fly
// Simple interface
interface Drawable {
    draw()
}

// Public interface
public interface Serializable {
    serialize(const string path)
    deserialize(const string data)
}

// Interface extending another interface
interface Resizable : Drawable {
    resize(const int width, const int height)
}
```

### 6.4 Class Members

Classes can contain fields (attributes) and methods.

**Examples:**
```fly
public class Person {
    // Private fields
    private string name
    private int age
    
    // Public field
    public bool isActive
    
    // Constructor-like method (void — no return type)
    public initialize(const string personName, const int personAge) {
        name = personName
        age = personAge
        isActive = true
    }
    
    // Public method with return type
    public string getName() {
        out = name
    }
    
    // Private method
    private validate() {
        // validation logic
    }
    
    // Static field
    static int instanceCount = 0
    
    // Static method with return type
    public static int getCount() {
        out = instanceCount
    }
}
```

### 6.5 Object Creation

**Examples:**
```fly
// Create a class instance on the heap
MyClass obj = new MyClass()
delete obj   // must be freed manually

// Use with initialization and return-type method call
Person person = new Person()
person.initialize("John", 30)
string name = person.getName()   // name = "John"
int count = Person.getCount()    // count = 1
delete person
```

---

### 6.6 Allocation and Lifetime

The `new` keyword allocates a new instance. Where the memory comes from — and who is responsible for freeing it — depends on whether the type is a `struct` or a `class`, and on the optional allocation qualifier (`unique`, `shared`, `weak`).

#### Struct: stack by default

A plain `new` on a `struct` allocates the data on the **stack** (via LLVM `alloca`). The variable is freed automatically when the enclosing scope exits. **Do not call `delete` on a stack-allocated struct** — the pointer is not heap-owned and calling `free()` on it is undefined behaviour.

```fly
struct Point { int x; int y }

process() {
    Point p = new Point()   // ← stack alloca
    p.x = 10
    p.y = 20
}   // ← p freed automatically; no delete
```

#### Class: heap by default

A plain `new` on a `class` allocates on the **heap** (`malloc(sizeof(T))`). The programmer is responsible for calling `delete` to release the memory. Forgetting `delete` leaks the object.

```fly
class Node { int val }

process() {
    Node n = new Node()   // ← malloc
    n.val = 42
    delete n              // ← free(n)
}
```

`delete` calls `free()` on the pointer and is valid only for objects created with plain `new` on a class. Never call `delete` on a smart-pointer object or a stack-allocated struct.

---

#### Smart-pointer allocation qualifiers

All three qualifiers work on both `struct` and `class`. When used on a `struct`, the data is moved to the **heap** (qualifiers always imply `malloc` so the runtime can call `free()` at scope exit).

| Qualifier | Storage | Who frees | Copies |
|---|---|---|---|
| *(none, struct)* | stack | automatic at scope exit | value semantics |
| *(none, class)* | heap | programmer via `delete` | reference semantics |
| `new unique` | heap | `free()` at scope exit | **not allowed** — compiler error |
| `new shared` | heap + 8-byte refcount header | `free()` when last reference exits | allowed — increments refcount |
| `new weak` | heap | `free()` at each holder's scope exit | allowed — no refcount; first exit frees, others dangle |

---

##### `new unique` — exclusive ownership

The object is heap-allocated. When the variable goes out of scope the runtime automatically calls `free()`. Copying a `unique` variable is a compile-time error.

```fly
process() {
    Point p = new unique Point()   // heap-allocated
    p.x = 10
    p.y = 42
}   // ← free(p) emitted automatically
```

##### `new shared` — reference-counted ownership

The runtime allocates `sizeof(i64) + sizeof(T)` bytes. The first 8 bytes hold a **reference count** initialised to 1. Every time the variable is copied the counter is incremented; when any holder exits scope the counter is decremented. When the count reaches 0 the entire block (header + data) is freed.

```fly
process() {
    Point a = new shared Point()   // refcount = 1
    Point b = a                    // refcount = 2 (copy increments)
    // … use a and b …
}   // ← refcount decremented twice; reaches 0 → freed
```

Memory layout:

```
+──────────────────+──────────────────────────────+
│  i64  refcount   │        struct/class data      │
+──────────────────+──────────────────────────────+
 ↑ 8 bytes          ↑ data pointer seen by code
```

##### `new weak` — untracked shared access

Like `unique`, the object is heap-allocated and `free()` is emitted for each variable at its scope exit. There is **no reference count** and no ownership coordination: if two variables hold the same allocation, the first one to go out of scope calls `free()`; the other becomes a dangling pointer. Use only when the lifetime is known to be longer than all aliases.

```fly
process() {
    Point a = new weak Point()
    Point b = a   // no refcount — b and a share the same pointer
    // b goes out of scope first → free(b) called
    // a is now dangling
}
```

---

#### Summary table

| Expression | Memory | Freed by |
|---|---|---|
| `struct S = new S()` | stack (alloca) | automatic at scope exit |
| `class C = new C()` | heap (malloc) | `delete c` |
| `T x = new unique T()` | heap | automatic `free()` at scope exit |
| `T x = new shared T()` | heap + refcount | automatic release when refcount → 0 |
| `T x = new weak T()` | heap | automatic `free()` at each holder's scope exit |

---

### 6.7 Generics

Fly supports **generic classes** and **generic functions** via monomorphization. Each unique instantiation is compiled into a distinct, fully specialized implementation — no type erasure, no boxing overhead, no runtime cost.

#### 6.7.1 Generic Class Declaration

Add one or more **type parameters** in angle brackets after the class name.

**Syntax:**
```
GenericClass ::= [ Modifiers ] 'class' Identifier '<' TypeParam ( ',' TypeParam )* '>' [ ':' Identifier ] '{' ClassMember* '}'
TypeParam    ::= Identifier
```

**Example:**
```fly
public class Wrapper<T> {
    private T value

    public Wrapper(T v) {
        value = v
    }

    public T get() {
        out = value
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
GenericFunc ::= [ Modifiers ] [ ReturnType ] Identifier '<' TypeParam ( ',' TypeParam )* '>' '(' [ Parameters ] ')' Block
```

**Example:**
```fly
// Generic identity function — returns its argument unchanged
T identity<T>(const T v) {
    out = v
}

void main() {
    int    i = identity<int>(10)        // explicit type argument
    string s = identity<string>("fly")  // explicit type argument
}
```

**Type inference** — when the argument type is unambiguous, the type argument can be omitted and the compiler infers it automatically:

```fly
void main() {
    int    i = identity(10)     // T inferred as int
    string s = identity("fly")  // T inferred as string
    bool   b = identity(true)   // T inferred as bool
}
```

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
processColor() {
    // Declare and initialize
    Color c = Color.RED
    
    // Assignment
    c = Color.BLUE
    
    // Pass to function
    setColor(Color.GREEN)
    
    // Compare
    if (c == Color.RED) {
        // handle red
    }
}

setColor(const Color c) {
    // use color
}
```

---

## 8. Expressions

### 8.1 Primary Expressions

#### 8.1.1 Literals

```fly
42              // integer literal
3.14            // float literal
true            // boolean literal
'c'             // character literal
"string"        // string literal
null            // null literal
```

#### 8.1.2 Identifiers

```fly
myVariable      // simple identifier
obj.field       // member access
array[0]        // array access
```

#### 8.1.3 Parenthesized Expressions

```fly
(a + b)
(x * y + z)
```

### 8.2 Unary Expressions

**Syntax:**
```
UnaryExpr ::= ( '++' | '--' | '!' | '-' | '+' ) Expression
            | Expression ( '++' | '--' )
```

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

#### 8.4.1 Assignment vs Equality: Important Distinction

Fly clearly distinguishes between the **assignment operator** `=` and the **equality comparison operator** `==`:

- **`=` (Assignment)**: Stores a value into a variable. This is a statement-level operation.
- **`==` (Equality)**: Compares two values for equality. This is an expression that evaluates to a boolean.

**Examples:**
```fly
// Assignment: stores the value 5 into variable x
x = 5

// Equality comparison: compares x with 5, evaluates to boolean
if (x == 5) {
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
if (x = 5) {        // This assigns 5 to x, then evaluates the result
    // ...
}

// CORRECT: Using == for comparison
if (x == 5) {       // This compares x with 5
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
matrix = {{1, 2}, {3, 4}}
```

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
IfStmt ::= 'if' [ '(' ] Expression [ ')' ] Statement
           ( 'elsif' [ '(' ] Expression [ ')' ] Statement )*
           [ 'else' Statement ]
```

**Examples:**
```fly
// Simple if
if (condition) {
    // code
}

// If without parentheses
if condition {
    // code
}

// If-else
if (x > 0) {
    positive = true
} else {
    positive = false
}

// If-elsif-else
if (a == 1) {
    b = 0
} elsif (a == 2) {
    b = 1
} elsif (a == 3) {
    b = 2
} else {
    b = -1
}

// Inline if (without braces)
if (condition) doSomething()
```

### 9.4 Switch Statements

**Syntax:**
```
SwitchStmt  ::= 'switch' [ '(' ] Expression [ ')' ] '{' CaseClause* [ DefaultClause ] '}'
CaseClause  ::= 'case' Expression ':' Statement*
DefaultClause ::= 'default' ':' Statement*
```

**Examples:**
```fly
switch (value) {
    case 1:
        // code for case 1
        break
    case 2:
        // code for case 2
        break
    case 3:
    case 4:
        // code for case 3 and 4 (fall-through)
        break
    default:
        // default code
}

// Without parentheses
switch value {
    case 0:
        result = "zero"
        break
    default:
        result = "other"
}
```

### 9.5 Loop Statements

#### 9.5.1 While Loop

**Syntax:**
```
WhileStmt ::= 'while' [ '(' ] Expression [ ')' ] Statement
```

**Examples:**
```fly
// While with parentheses
while (count < 10) {
    count++
}

// While without parentheses
while count < 10 {
    count++
}

// Infinite loop
while true {
    // loop body
    if (shouldBreak) break
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

// For loop without parentheses
for int i = 0; i < length; i++ {
    process(array[i])
}
```

### 9.6 Jump Statements

#### 9.6.1 Return Statement

`return` exits a void function early. Functions with a return type use `out` to carry the result; `return` may still be used to exit early from such functions.

**Syntax:**
```
ReturnStmt ::= 'return'
```

**Examples:**
```fly
// Return exits the function
return

// Early return based on condition
if (done) {
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
    if (condition) {
        break  // exit loop
    }
}

switch (value) {
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
    if (i % 2 == 0) {
        continue  // skip even numbers
    }
    process(i)
}
```

### 9.7 Error Handling Statements

Fly error handling is built on two keywords — `fail` and `handle` — and a built-in `error` type. The mechanism is **not** exception-based stack unwinding. Instead, every function receives a hidden first parameter: a pointer to an `error` struct. When `fail` fires, it writes into that struct and either jumps past the surrounding `handle` block (if one exists in the same function) or returns immediately. The error value propagates upward through the call stack only when no caller intercepts it with `handle`.

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
if (err) { /* error occurred */ }
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

**Behavior of `fail`:**

| Context | What happens |
|---|---|
| Inside a `handle` block (same function) | Writes to error struct; jumps directly to the `safe` block (skips remaining handle body) |
| Outside any `handle` (no enclosing handle in the current function) | Writes to error struct; returns `void` immediately |

Any code after `fail` within the same basic block is unreachable.

```fly
validate(const int age) {
    if (age < 0) {
        fail 400, "age must be non-negative"
        // unreachable
    }
    if (age > 150) {
        fail 1001
    }
    // continues here if no fail
}
```

#### 9.7.3 Automatic Error Propagation

Every function (except `main`) has a hidden first parameter: a pointer to the caller's error struct. When a function fails **without** a `handle` in its own body, it writes to that pointer and returns void. The caller's code continues from where the call returned — the error data is already in the shared struct.

```fly
fetchData() {
    fail 503, "service unavailable"   // writes error, returns void
}

void main() {
    fetchData()   // error is written to main's error struct
    // execution continues here, but error struct is now populated
    // main() will return exit code 503
}
```

Because propagation is not stack unwinding, a failing callee does NOT unwind the caller — the next line after the call still executes. Use a `handle` block to intercept failures before they reach the caller.

#### 9.7.4 Handle Statement

`handle` creates a guarded region. Functions called inside the region share a dedicated error handler. You can optionally declare a named `error` variable to inspect the outcome after the block.

**Syntax:**
```
HandleStmt ::= [ 'error' Identifier ] 'handle' ( Statement | Block )
```

**How it works:**

The compiler emits two LLVM basic blocks for each `handle`:
- **`handle`** — the guarded code
- **`safe`** — the continuation (code after the handle)

When `fail` fires **directly inside the handle body** (same function), execution jumps to `safe`, skipping the rest of the handle body. When `fail` fires **in a callee**, the callee returns void and the handle body continues at the next statement.

After the handle, check `if (err)` to detect whether any error was written.

**Forms:**

**1. Unnamed — discard error details:**
```fly
void main() {
    handle {
        riskyOperation()
        anotherOp()
    }
    // execution always reaches here; error is silently swallowed
}
```

**2. Named — inspect whether an error occurred:**
```fly
void main() {
    error err handle {
        riskyOperation()
    }
    if (err) {
        // error occurred — take fallback path
        return
    }
    // success path
}
```

**3. Single-statement shorthand:**
```fly
quickCheck() {
    error err handle riskyOp()
    if (err) { return }
}
```

**4. Nested handles:**
```fly
process() {
    error outer handle {
        error inner handle {
            deepOp()   // inner intercepts first
        }
        if (inner) {
            fail    // re-raise to outer
        }
        followupOp()
    }
    if (outer) {
        // handle top-level failure
    }
}
```

#### 9.7.5 Complete Examples

**Example 1: Propagation without handle**
```fly
openFile(const string path) {
    if (path == "") {
        fail 400, "empty path"
    }
}

void main() {
    openFile("")       // writes error 400 to main's struct, returns
    openFile("/tmp")   // STILL CALLED — propagation is not unwinding
    // main returns exit code 400
}
```

**Example 2: Intercepting with handle**
```fly
openFile(const string path) {
    if (path == "") { fail 400, "empty path" }
}

void main() {
    error err handle {
        openFile("")       // fails and writes error; handle body continues
        openFile("/tmp")   // STILL CALLED (callee fail ≠ jump in caller)
    }
    if (err) {
        // err is set; handle the 400 error
    }
}
```

**Example 3: Direct fail in handle — jumps immediately**
```fly
void main() {
    error err handle {
        if (someCondition) {
            fail 500    // jumps directly to safe block
        }
        neverReached()  // skipped when fail fires above
    }
    if (err) { /* code = 500 */ }
}
```

**Example 4: Re-raise to caller**
```fly
inner() {
    fail 503
}

outer() {
    error err handle {
        inner()
    }
    if (err) {
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

connect(const string host) {
    NetError e = new unique NetError()
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

#### 9.7.7 Key Differences from try-catch

| Feature | Fly `fail`/`handle` | Traditional `try`/`catch` |
|---|---|---|
| Signal error | `fail` | `throw` |
| Intercept | `handle { }` | `try { } catch { }` |
| Payload | int, string, or object (comma-separated, up to one each) | typed exception object |
| Stack unwinding | **No** — failing callee just returns; caller continues | **Yes** — stack frames are unwound |
| Callee fail bypasses rest of caller? | **No** (unless fail is direct in handle block) | **Yes** |
| Error propagation | via hidden pointer parameter; must re-`fail` manually | automatic until caught |
| Overhead | zero-cost on success; single function return on failure | unwinder overhead |

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
- If no namespace is declared, a default namespace based on the filename is used

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

#### 10.2.2 Class import (Java style)

When the last component of the path names a **class** (or enum/struct), that type is placed directly in the current scope — no prefix needed. This is the Fly equivalent of Java's `import java.util.List`.

```fly
import fly.data.List    // 'List' class is in scope
import fly.data.Stack   // 'Stack' class is in scope

void main() {
    List  l = new List()    // no 'data.' prefix needed
    Stack s = new Stack()
    l.free()
    s.free()
}
```

There is no coupling between the filename and the class name. The import path navigates the **namespace hierarchy**; the filename is irrelevant. By convention, stdlib files use the capitalized class name (`list.fly` → `List`), but this is optional for user code.

#### 10.2.3 Wildcard import

`.*` brings **all public symbols** (classes, enums, structs, functions) declared directly in the target namespace into the current scope. The target must be a namespace — using `.*` on a class or function is a compile-time error.

```fly
import fly.data.*       // List, Stack, Queue, Deque, Map, Set, Tree all in scope

void main() {
    List l = new List()
    Map  m = new Map()
    l.free()
    m.free()
}
```

```fly
// Error: fly.data.List is a class, not a namespace
import fly.data.List.*  // → compile error: wildcard requires a namespace target
```

#### 10.2.4 Alias import

Binds the imported namespace or symbol under a different local name. Cannot be combined with wildcard (`.*`).

```fly
import fly.str as s     // 's' is in scope
import fly.data.List as L

void main() {
    int n = s.len("hello")
    L myList = new L()
    myList.free()
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
    out = r * r
}
```

```fly
// File: main.fly — class import (Java style)
import geom.Circle

void main() {
    Circle c = new Circle()
    c.radius = 5
    delete c
}
```

```fly
// File: main.fly — namespace import
import geom

void main() {
    geom.Circle c = new geom.Circle()
    int a = geom.area(5)
    delete c
}
```

```fly
// File: main.fly — wildcard import
import geom.*

void main() {
    Circle c = new Circle()
    int a = area(5)      // function in scope too
    delete c
}
```

---

## 11. Modifiers

### 11.1 Visibility Modifiers

Control the accessibility of declarations.

| Modifier    | Scope                                          | Applies To                    |
|-------------|------------------------------------------------|-------------------------------|
| `private`   | Only within the same file/class                | Functions, classes, members   |
| `protected` | Within the class and derived classes           | Class members                 |
| `public`    | Accessible from anywhere                       | Functions, classes, members   |
| (default)   | Package-private (same namespace)               | Functions, classes            |

**Examples:**
```fly
// Private function
private internalHelper() {}

// Protected member
class Base {
    protected int value
}

// Public class
public class PublicAPI {
    public exportedMethod() {}
}

// Default visibility
packageFunction() {}
class DefaultClass {}
```

### 11.2 Constant Modifier

The `const` modifier marks values as immutable.

**Examples:**
```fly

// Constant function parameter (const is required for all parameters)
process(const int size) {
    // size cannot be modified
}

// Constant local variable
func() {
    const int limit = 50
    // limit = 100  // Error: cannot modify const
}
```

### 11.3 Static Modifier

The `static` modifier creates class-level members.

**Examples:**
```fly
class Counter {
    static int totalCount = 0
    
    public static int getTotal() {
        out = totalCount
    }
    
    public increment() {
        totalCount++
    }
}

// Usage
Counter c = new Counter()
c.increment()
int total = Counter.getTotal()   // total = 1
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
process() {
    // Constant local variable
    const int maxRetries = 3
}
```

---

## 12. Comments

### 12.1 Line Comments

Line comments start with `//` and continue to the end of the line.

**Examples:**
```fly
// This is a line comment
int value = 42  // End-of-line comment

// Multiple line comments
// can be used for
// multi-line documentation
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

calculate() {
    /* inline comment */ return
}
```

**Note:** Block comments can span multiple lines and are preserved by the parser for documentation purposes.

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

Modifiers       ::= ( 'public' | 'private' | 'protected' | 'const' | 'static' )*
```

### 13.2 Type System

```
Type            ::= BuiltinType 
                  | NamedType 
                  | ArrayType

BuiltinType     ::= 'bool' | 'byte' | 'char' 
                  | 'short' | 'ushort' | 'int' | 'uint' 
                  | 'long' | 'ulong' | 'float' | 'double' 
                  | 'string' | 'error'

NamedType       ::= Name ( '.' Name )*

ArrayType       ::= Type '[' [ Expression ] ']'
```

### 13.3 Declarations

```
ClassDecl       ::= Modifiers ( 'class' | 'struct' ) 
                    Identifier [ '<' TypeParam ( ',' TypeParam )* '>' ]
                    [ ':' Identifier ] '{' ClassMember* '}'

TypeParam       ::= Identifier

GenericFunc     ::= Modifiers [ ReturnType ] Identifier
                    '<' TypeParam ( ',' TypeParam )* '>'
                    '(' [ ParamList ] ')' Block

InterfaceDecl   ::= Modifiers 'interface' 
                    Identifier [ ':' Identifier ] '{' InterfaceMember* '}'

EnumDecl        ::= Modifiers 'enum' Identifier '{' EnumEntryList '}'

EnumEntryList   ::= EnumEntry ( ',' EnumEntry )*

EnumEntry       ::= Identifier

FunctionDecl    ::= Modifiers [ ReturnType ] Identifier '(' [ ParamList ] ')' ( Block | ';' )

ReturnType      ::= Type ( ',' Type )*

ParamList       ::= Param ( ',' Param )*

Param           ::= [ 'const' ] Type Identifier
```

**Inheritance Rules:**
- **Class**: Single inheritance; can extend one struct or one interface
- **Struct**: Can extend only another struct
- **Interface**: Can extend only another interface
- **Enum**: Cannot extend anything

### 13.4 Statements

```
Statement       ::= Block 
                  | IfStmt 
                  | SwitchStmt 
                  | WhileStmt 
                  | ForStmt
                  | ReturnStmt 
                  | BreakStmt 
                  | ContinueStmt 
                  | FailStmt 
                  | HandleStmt
                  | ExprStmt 
                  | VarDeclStmt 
                  | AssignStmt

Block           ::= '{' Statement* '}'

IfStmt          ::= 'if' [ '(' ] Expr [ ')' ] Statement 
                    ( 'elsif' [ '(' ] Expr [ ')' ] Statement )* 
                    [ 'else' Statement ]

SwitchStmt      ::= 'switch' [ '(' ] Expr [ ')' ] '{' 
                    CaseClause* [ DefaultClause ] '}'

WhileStmt       ::= 'while' [ '(' ] Expr [ ')' ] Statement

ForStmt         ::= 'for' VarDecl ( ',' VarDecl )* ';' Expr ';' 
                    Expr ( ',' Expr )* Statement

ReturnStmt      ::= 'return'

BreakStmt       ::= 'break'

ContinueStmt    ::= 'continue'

FailStmt        ::= 'fail' [ Expr ]

HandleStmt      ::= [ 'error' Identifier '=' ] 'handle' ( Statement | Block )

VarDeclStmt     ::= Modifiers Type Identifier [ '=' Expr ]

AssignStmt      ::= Identifier AssignOp Expr
```

### 13.5 Expressions

```
Expression      ::= TernaryExpr

TernaryExpr     ::= LogicalOrExpr [ '?' Expr ':' Expr ]

LogicalOrExpr   ::= LogicalAndExpr ( '||' LogicalAndExpr )*

LogicalAndExpr  ::= BitwiseOrExpr ( '&&' BitwiseOrExpr )*

BitwiseOrExpr   ::= BitwiseXorExpr ( '|' BitwiseXorExpr )*

BitwiseXorExpr  ::= BitwiseAndExpr ( '^' BitwiseAndExpr )*

BitwiseAndExpr  ::= EqualityExpr ( '&' EqualityExpr )*

EqualityExpr    ::= RelationalExpr ( ( '==' | '!=' ) RelationalExpr )*

RelationalExpr  ::= ShiftExpr ( ( '<' | '>' | '<=' | '>=' ) ShiftExpr )*

ShiftExpr       ::= AddExpr ( ( '<<' | '>>' ) AddExpr )*

AddExpr         ::= MultExpr ( ( '+' | '-' ) MultExpr )*

MultExpr        ::= UnaryExpr ( ( '*' | '/' | '%' ) UnaryExpr )*

UnaryExpr       ::= ( '++' | '--' | '!' | '-' | '+' ) UnaryExpr 
                  | PostfixExpr

PostfixExpr     ::= PrimaryExpr ( '++' | '--' | '(' ArgList ')' 
                  | '[' Expr ']' | '.' Identifier )*

PrimaryExpr     ::= Literal 
                  | Identifier 
                  | '(' Expr ')' 
                  | 'new' Identifier '(' ArgList ')'
                  | ArrayValue

ArrayValue      ::= '{' [ Expr ( ',' Expr )* ] '}'

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

// Class declaration (single inheritance)
public class Application {
    // Private fields
    private string name
    private int value
    private Status currentStatus
    
    // Static field
    static int instanceCount = 0
    
    // Void method — no return type
    public initialize(const string appName) {
        name = appName
        value = 0
        currentStatus = Status.IDLE
        instanceCount++
    }
    
    // Method with return type — 'out' carries the result
    public int getValue() {
        out = value
    }
    
    // Public method with error handling
    public process() {
        error err = handle {
            calculateResult()
        }
        
        if (err) {
            // Error occurred
            currentStatus = Status.STOPPED
        }
    }
    
    // Private helper method that may fail
    private calculateResult() {
        if (value < 0) {
            fail "Invalid value"     // Fail with string message
        }
        if (value > 1000) {
            fail 999                 // Fail with error code
        }
    }
    
    // Method demonstrating void error handling
    public validate() {
        error validationErr = handle {
            if (name == "") {
                fail "Name cannot be empty"
            }
        }
        
        if (validationErr) {
            currentStatus = Status.STOPPED
        }
    }
    
    // Setter method
    public setValue(const int newValue) {
        value = newValue
    }
    
    // Static method
    public static incrementCount() {
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
    draw()
}

// Class implementing an interface (single inheritance)
public class Shape : Drawable {
    private int width
    private int height
    
    draw() {
        // drawing logic
    }
}

// Main entry point
// Note: main() automatically returns 0 if all errors are handled,
// or returns 1 if an unhandled error occurs
void main() {
    // Create application instance
    Application app = new Application()
    app.initialize("MyApp")
    
    // Error handling example: validate the application
    handle app.validate()
    
    // Set status
    Status status = Status.RUNNING
    
    // Control flow with error handling
    if (status == Status.RUNNING) {
        error processErr = handle {
            app.process()
        }
        
        if (processErr) {
            // Handle error gracefully
            status = Status.STOPPED
        } else {
            handleResult()
        }
    } elsif (status == Status.PAUSED) {
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
    while (count < 10) {
        count++
    }
    
    // Switch statement
    switch (count) {
        case 10:
            // count is 10
            break
        default:
            // other value
    }
    
    // Create structure
    Point p = new Point()
    p.x = 10
    p.y = 20
    
    // Error handling with structure
    error distErr = handle {
        int dist = p.x * p.x + p.y * p.y
        if (dist > 1000) {
            fail "Distance too large"
        }
    }
}

// Private helper function (void)
private handleResult() {
    // handle result logic
}

// Function with const parameter
private processNumber(const int num) {
    if (num % 2 == 0) {
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

All keywords are reserved and cannot be used as identifiers:

```
as          bool        break       byte        case
char        class       const       continue    default
double      elsif       else        enum        error
fail        false       float       for         handle
if          import      interface   int         long
namespace   new         null        private     protected
public      return      short       static      string
struct      switch      true        uint        ulong
ushort      while
```

---

## Appendix B: Operator Precedence

From highest to lowest precedence:

1. Postfix: `++`, `--`, `()`, `[]`, `.`
2. Unary: `++`, `--`, `!`, `-`, `+` (prefix)
3. Multiplicative: `*`, `/`, `%`
4. Additive: `+`, `-`
5. Shift: `<<`, `>>`
6. Relational: `<`, `>`, `<=`, `>=`
7. Equality: `==`, `!=`
8. Bitwise AND: `&`
9. Bitwise XOR: `^`
10. Bitwise OR: `|`
11. Logical AND: `&&`
12. Logical OR: `||`
13. Ternary: `?:`
14. Assignment: `=`, `+=`, `-=`, `*=`, `/=`, `%=`, `&=`, `|=`, `^=`, `<<=`, `>>=`

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
| Catch with variable | `error err = handle { ... }` | `catch (Exception err) { ... }` |
| Error type | `error` | `Exception` or custom class |

### Common Patterns

```fly
// Pattern 1: Simple fail
operation() {
    fail                        // Throw exception
}

// Pattern 2: Fail with integer code
check() {
    fail 404                    // Error code
}

// Pattern 3: Fail with string message
load() {
    fail "File not found"       // Error message
}

// Pattern 4: Simple handle
handle operation()              // Catch and ignore

// Pattern 5: Handle with error capture
error err = handle {
    riskyOperation()
}
if (err) {
    // Handle error
}

// Pattern 6: Handle with recovery
error err = handle {
    computation()
}
if (err) {
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
    if (err) {
        // Handle gracefully
    }
}
```

---

**© Fly Project - https://flylang.org**  
**Licensed under Apache License v2.0**  
**Documentation Version 1.0 - December 2025**

