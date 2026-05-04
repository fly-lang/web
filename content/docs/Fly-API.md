+++
title = "Fly API"
description = "Reference for the Fly standard library — fly.str, fly.math, fly.os, fly.thread."
template = "docs-page.html"
weight = 4
+++

The Fly standard library is organised in namespaces. Import a namespace with `import`, then call its functions using the Fly input/output parameter convention: `const` parameters are read-only inputs, plain parameters receive the result.

---

## fly.str

String utilities. All transform functions allocate a new string on the heap — the caller is responsible for freeing it.

```fly
import fly.str
```

### Conversion

#### `convert`
```fly
public convert(const int src, string out)
```
Converts an integer to its decimal string representation.

| Parameter | Direction | Description |
|---|---|---|
| `src` | input | integer to convert |
| `out` | output | resulting string (heap-allocated) |

```fly
string s
str.convert(42, s)   // s = "42"
```

---

### Query

#### `len`
```fly
public len(const string src, int out)
```
Writes the number of characters in `src` into `out`.

#### `isEmpty`
```fly
public isEmpty(const string src, bool out)
```
Writes `true` into `out` if `src` has zero length.

#### `contains`
```fly
public contains(const string src, const string sub, bool out)
```
Writes `true` into `out` if `sub` appears anywhere in `src`.

#### `startsWith`
```fly
public startsWith(const string src, const string prefix, bool out)
```
Writes `true` into `out` if `src` begins with `prefix`.

#### `endsWith`
```fly
public endsWith(const string src, const string suffix, bool out)
```
Writes `true` into `out` if `src` ends with `suffix`.

#### `indexOf`
```fly
public indexOf(const string src, const string sub, int out)
```
Writes the index of the first occurrence of `sub` in `src` into `out`, or `-1` if not found.

#### `lastIndexOf`
```fly
public lastIndexOf(const string src, const string sub, int out)
```
Writes the index of the last occurrence of `sub` in `src` into `out`, or `-1` if not found.

#### `equals`
```fly
public equals(const string a, const string b, bool out)
```
Writes `true` into `out` if `a` and `b` are identical.

#### `equalsIgnoreCase`
```fly
public equalsIgnoreCase(const string a, const string b, bool out)
```
Writes `true` into `out` if `a` and `b` are equal ignoring case.

#### `count`
```fly
public count(const string src, const string sub, int out)
```
Writes the number of non-overlapping occurrences of `sub` in `src` into `out`.

**Query example:**

```fly
import fly.str

main() {
    string msg = "Hello, Fly!"
    int length
    bool empty
    bool found

    str.len(msg, length)            // length = 11
    str.isEmpty(msg, empty)         // empty  = false
    str.contains(msg, "Fly", found) // found  = true
}
```

---

### Transform

All transform functions write a newly allocated string into `out`. The caller must free it when done, or use a smart pointer.

#### `toUpper`
```fly
public toUpper(const string src, string out)
```
Writes an upper-case copy of `src` into `out`.

#### `toLower`
```fly
public toLower(const string src, string out)
```
Writes a lower-case copy of `src` into `out`.

#### `trim`
```fly
public trim(const string src, string out)
```
Writes a copy of `src` with leading and trailing whitespace removed into `out`.

#### `trimLeft`
```fly
public trimLeft(const string src, string out)
```
Removes leading whitespace only.

#### `trimRight`
```fly
public trimRight(const string src, string out)
```
Removes trailing whitespace only.

#### `substring`
```fly
public substring(const string src, const int start, const int end, string out)
```
Writes the substring of `src` from index `start` (inclusive) to `end` (exclusive) into `out`.

#### `replace`
```fly
public replace(const string src, const string from, const string to, string out)
```
Writes a copy of `src` with all occurrences of `from` replaced by `to` into `out`.

#### `repeat`
```fly
public repeat(const string src, const int count, string out)
```
Writes `src` repeated `count` times into `out`.

#### `concat`
```fly
public concat(const string a, const string b, string out)
```
Writes the concatenation of `a` and `b` into `out`.

**Transform example:**

```fly
import fly.str

main() {
    string result
    str.concat("Hello, ", "Fly!", result)   // result = "Hello, Fly!"
    str.toUpper(result, result)             // result = "HELLO, FLY!"
}
```

---

## fly.math

*Coming soon.*

---

## fly.os

*Coming soon.*

---

## fly.thread

*Coming soon.*
