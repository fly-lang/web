+++
title = "Fly API"
description = "Reference for the Fly standard library — fly.str, fly.math, fly.mem, fly.os, fly.sync, fly.data, fly.json, fly.codec, fly.hash, fly.net, fly.targz, fly.log, fly.assert."
template = "docs-page.html"
weight = 4
+++

The Fly standard library is organised in namespaces. Import a namespace with `import`, then call its functions using standard return-type syntax. `const` parameters are read-only inputs; the return value is assigned directly at the call site.

---

## fly.str

String utilities. Transform functions return a heap-allocated string — the caller is responsible for freeing it.

```fly
import fly.str
```

### Conversion

#### `convert`
```fly
public string convert(const int src)
```
Returns the decimal string representation of `src`.

```fly
string s = str.convert(42)   // s = "42"
```

---

### Query

#### `len`
```fly
public int len(const string src)
```
Returns the number of characters in `src`.

#### `isEmpty`
```fly
public bool isEmpty(const string src)
```
Returns `true` if `src` has zero length.

#### `equals`
```fly
public bool equals(const string a, const string b)
```
Returns `true` if `a` and `b` are identical.

#### `equalsIgnoreCase`
```fly
public bool equalsIgnoreCase(const string a, const string b)
```
Returns `true` if `a` and `b` are equal ignoring case.

#### `contains`
```fly
public bool contains(const string src, const string sub)
```
Returns `true` if `sub` appears anywhere in `src`.

#### `startsWith`
```fly
public bool startsWith(const string src, const string prefix)
```
Returns `true` if `src` begins with `prefix`.

#### `endsWith`
```fly
public bool endsWith(const string src, const string suffix)
```
Returns `true` if `src` ends with `suffix`.

#### `indexOf`
```fly
public int indexOf(const string src, const string sub)
```
Returns the index of the first occurrence of `sub` in `src`, or `-1` if not found.

#### `lastIndexOf`
```fly
public int lastIndexOf(const string src, const string sub)
```
Returns the index of the last occurrence of `sub` in `src`, or `-1` if not found.

#### `count`
```fly
public int count(const string src, const string sub)
```
Returns the number of non-overlapping occurrences of `sub` in `src`.

**Example:**

```fly
import fly.str

void main() {
    string msg = "Hello, Fly!"
    int length = str.len(msg)              // length = 11
    bool empty = str.isEmpty(msg)          // empty  = false
    bool found = str.contains(msg, "Fly")  // found  = true
}
```

---

### Transform

#### `toUpper`
```fly
public string toUpper(const string src)
```
Returns an upper-case copy of `src`.

#### `toLower`
```fly
public string toLower(const string src)
```
Returns a lower-case copy of `src`.

#### `trim`
```fly
public string trim(const string src)
```
Returns a copy of `src` with leading and trailing whitespace removed.

#### `trimLeft`
```fly
public string trimLeft(const string src)
```
Returns a copy of `src` with leading whitespace removed.

#### `trimRight`
```fly
public string trimRight(const string src)
```
Returns a copy of `src` with trailing whitespace removed.

#### `substring`
```fly
public string substring(const string src, const int start, const int end)
```
Returns the substring of `src` from index `start` (inclusive) to `end` (exclusive).

#### `replace`
```fly
public string replace(const string src, const string from, const string to)
```
Returns a copy of `src` with all occurrences of `from` replaced by `to`.

#### `repeat`
```fly
public string repeat(const string src, const int count)
```
Returns `src` repeated `count` times.

#### `concat`
```fly
public string concat(const string a, const string b)
```
Returns the concatenation of `a` and `b`.

**Example:**

```fly
import fly.str

void main() {
    string result = str.concat("Hello, ", "Fly!")   // result = "Hello, Fly!"
    result = str.toUpper(result)                    // result = "HELLO, FLY!"
}
```

---

## fly.math

Mathematical functions and a random-number generator (xoshiro256**).

```fly
import fly.math
```

### Types

```fly
public struct fly_rng {
    ulong s0
    ulong s1
    ulong s2
    ulong s3
}
```
State for the xoshiro256** PRNG. Initialise with `randSeed` before use.

---

### Base

#### `absI` / `absF`
```fly
public long   absI(const long x)
public double absF(const double x)
```
Absolute value for integers and floats.

#### `sign`
```fly
public int sign(const double x)
```
Returns `1` if `x > 0`, `-1` if `x < 0`, `0` if `x == 0`.

#### `minI` / `minF`
```fly
public long   minI(const long a, const long b)
public double minF(const double a, const double b)
```
Returns the smaller of `a` and `b`.

#### `maxI` / `maxF`
```fly
public long   maxI(const long a, const long b)
public double maxF(const double a, const double b)
```
Returns the larger of `a` and `b`.

#### `clampI` / `clampF`
```fly
public long   clampI(const long x, const long lo, const long hi)
public double clampF(const double x, const double lo, const double hi)
```
Clamps `x` to the range `[lo, hi]`.

#### `gcd`
```fly
public long gcd(const long a, const long b)
```
Greatest common divisor of `a` and `b`.

#### `lcm`
```fly
public long lcm(const long a, const long b)
```
Least common multiple of `a` and `b`.

#### `divmod`
```fly
public void divmod(const long a, const long b, long out_q, long out_r)
```
Writes quotient into `out_q` and remainder into `out_r`.

---

### Trigonometry

```fly
public double sin(const double x)
public double cos(const double x)
public double tan(const double x)
public double asin(const double x)
public double acos(const double x)
public double atan(const double x)
public double atan2(const double y, const double x)
public double sinh(const double x)
public double cosh(const double x)
public double tanh(const double x)
public double hypot(const double a, const double b)
```

#### `toRadians` / `toDegrees`
```fly
public double toRadians(const double x)
public double toDegrees(const double x)
```
Convert between degrees and radians.

---

### Exponential & Logarithms

```fly
public double sqrt(const double x)
public double cbrt(const double x)
public double pow(const double base, const double exp)
public double exp(const double x)
public double exp2(const double x)
public double log(const double x)
public double log2(const double x)
public double log10(const double x)
public double logN(const double x, const double n)
```

`logN` computes log base `n` of `x`.

---

### Rounding

```fly
public double floor(const double x)
public double ceil(const double x)
public double round(const double x)
public double trunc(const double x)
public double roundTo(const double x, const int n)
```

`roundTo(x, n)` rounds `x` to `n` decimal places (negative `n` rounds to tens, hundreds, …).

---

### Classification

#### `isNaN` / `isInf` / `isFinite`
```fly
public bool isNaN(const double x)
public bool isInf(const double x)
public bool isFinite(const double x)
```

#### `approxEqual`
```fly
public bool approxEqual(const double a, const double b, const double epsilon)
```
Returns `true` if `|a − b| ≤ epsilon`.

#### `copySign`
```fly
public double copySign(const double x, const double y)
```
Returns `x` with the sign of `y`.

---

### Bitwise

```fly
public bool isPow2(const long n)
public long nextPow2(const long n)
public int  popcount(const long n)
public int  leadingZeros(const long n)
public int  trailingZeros(const long n)
```

---

### Random (xoshiro256**)

#### `randSeed`
```fly
public void randSeed(fly_rng rng, const ulong seed)
```
Seeds the RNG state from `seed` using SplitMix64.

#### `rngNext`
```fly
public ulong rngNext(fly_rng rng)
```
Advances the RNG and returns the next raw 64-bit value (also updates `rng`).

#### `randFloat`
```fly
public double randFloat(fly_rng rng)
```
Returns a uniformly distributed `double` in `[0, 1)`.

#### `randInt`
```fly
public long randInt(fly_rng rng, const long a, const long b)
```
Returns a uniformly distributed integer in `[a, b]`.

#### `randNormal`
```fly
public double randNormal(fly_rng rng, const double mu, const double sigma)
```
Returns a sample from the normal distribution N(μ, σ) using Box-Muller.

---

### Special functions

```fly
public double fma(const double a, const double b, const double c)
public double lerp(const double a, const double b, const double t)
public double saturate(const double x)
public long   factorialI(const int n)
public double factorialF(const int n)
public long   comb(const long n, const long k)
public long   perm(const long n, const long k)
public double erf(const double x)
public double erfc(const double x)
public double gamma(const double x)
public double asinh(const double x)
public double acosh(const double x)
public double atanh(const double x)
```

`fma(a, b, c)` = `a*b + c` (fused multiply-add).  
`lerp(a, b, t)` = `a*(1−t) + b*t`.  
`saturate(x)` clamps `x` to `[0, 1]`.

**Example:**

```fly
import fly.math

void main() {
    fly_rng rng = new fly_rng()
    math.randSeed(rng, 42)
    double v = math.randFloat(rng)       // v in [0, 1)
    long n   = math.randInt(rng, 1, 10)  // n in [1, 10]
    double s = math.sqrt(2.0)            // s ≈ 1.4142
}
```

---

## fly.mem

Raw memory operations and typed read/write primitives.

```fly
import fly.mem
```

### Types

```fly
public struct Ptr {
    ulong addr
}
```
A raw memory address wrapper (`void*` equivalent).

---

### Allocation

#### `alloc`
```fly
public long alloc(const ulong size)
```
Allocates `size` bytes and returns the raw address.

#### `realloc`
```fly
public long realloc(const long ptr, const ulong newSize)
```
Resizes a previous allocation; returns the new address (may differ from `ptr`).

#### `free`
```fly
public void free(const long ptr, const ulong size)
```
Frees a previously allocated block.

#### `isNull`
```fly
public bool isNull(const long ptr)
```
Returns `true` if `ptr` is `0` (unset).

#### `toPtr`
```fly
public Ptr toPtr(const long addr)
```
Wraps a raw address as a `Ptr`.

---

### Bulk operations

#### `copy`
```fly
public void copy(const long dst, const long src, const ulong size)
```
Copies `size` bytes from `src` to `dst` (non-overlapping regions).

#### `move`
```fly
public void move(const long dst, const long src, const ulong size)
```
Copies `size` bytes, handling overlapping regions correctly.

#### `zero`
```fly
public void zero(const long dst, const ulong size)
```
Fills `size` bytes at `dst` with zeroes.

#### `compare`
```fly
public int compare(const long a, const long b, const ulong size)
```
Returns `0` if equal, `< 0` if `a < b`, `> 0` if `a > b` (C `memcmp` semantics).

---

### Typed reads

```fly
public byte  readByte(const long ptr, const int offset)
public short readShort(const long ptr, const int offset)
public int   readInt(const long ptr, const int offset)
public long  readLong(const long ptr, const int offset)
```
All offsets are in bytes.

### Typed writes

```fly
public void writeByte(const long ptr, const int offset, const byte val)
public void writeShort(const long ptr, const int offset, const short val)
public void writeInt(const long ptr, const int offset, const int val)
public void writeLong(const long ptr, const int offset, const long val)
```

---

## fly.os.env

Process environment, command-line arguments, working directory, and process control.

```fly
import fly.os.env
```

### Types

```fly
public struct StringArray {
    long items
    int  count
}
```
A heap-allocated array of Fly strings returned by functions that produce variable-length string lists.

---

### Environment variables

#### `get`
```fly
public string get(const string key)
```
Returns the value of environment variable `key`, or an empty string if not set.

#### `set`
```fly
public void set(const string key, const string value)
```
Sets `key` to `value`, overwriting any existing value.

#### `delete`
```fly
public void delete(const string key)
```
Removes `key` from the environment. No-op if not present.

#### `all`
```fly
public StringArray all()
```
Returns all environment variables as `"KEY=VALUE"` strings.

#### `allCount`
```fly
public int allCount()
```
Returns the number of environment variables currently set.

#### `expand`
```fly
public string expand(const string s)
```
Replaces `$VAR` and `${VAR}` references in `s` with their values. Unknown variables expand to an empty string.

---

### Working directory

#### `cwdGet`
```fly
public string cwdGet()
```
Returns the current working directory path.

#### `cwdSet`
```fly
public void cwdSet(const string path)
```
Changes the current working directory to `path`.

---

### Arguments & system info

#### `argsGet`
```fly
public StringArray argsGet()
```
Returns all command-line arguments (`args[0]` is the executable path).

#### `argsCount`
```fly
public int argsCount()
```
Returns the number of command-line arguments (including `argv[0]`).

#### `hostname`
```fly
public string hostname()
```
Returns the system hostname, or an empty string on failure.

#### `osname`
```fly
public string osname()
```
Returns the OS name, e.g. `"linux"` or `"darwin"`.

#### `exit`
```fly
public void exit(const int code)
```
Terminates the process with the given exit code. Never returns.

---

## fly.os.fs

File system operations.

```fly
import fly.os.fs
```

### Types

```fly
public struct File {
    int  fd
    byte flags
}
```
An open OS file descriptor. Obtain via `open()`, `create()`, or `openOpts()`; release via `close()`.

```fly
public struct Stat {
    ulong size
    ulong mtime_sec
    ulong mtime_nsec
    uint  mode
    int   is_file
    int   is_dir
    int   is_symlink
}
```
File metadata. Times are Unix epoch seconds/nanoseconds.

```fly
public struct DirEntry {
    string name
    Stat   stat
}
```
A single directory entry with its basename and metadata.

```fly
public struct DirEntries {
    long  items
    ulong len
    ulong cap
}
```
A heap-allocated array of `DirEntry` values.

---

### Open / close

#### `open`
```fly
public File open(const string path)
```
Opens `path` for reading (`O_RDONLY`). Check `out.fd >= 0` for success.

#### `create`
```fly
public File create(const string path)
```
Creates or truncates `path` for writing (`O_WRONLY|O_CREAT|O_TRUNC`, mode 0644).

#### `openOpts`
```fly
public File openOpts(const string path, const int flags, const int perm)
```
Opens `path` with explicit `flags` and permission bits (follow `open(2)` conventions).

#### `close`
```fly
public void close(File f)
```
Closes the file descriptor of `f`.

#### `sync`
```fly
public void sync(File f)
```
Calls `fsync` on `f`, flushing OS buffers to persistent storage.

---

### Read / write (path-based)

#### `read`
```fly
public fly.os.io.Buf read(const string path)
```
Reads the entire file at `path` into a heap-allocated `Buf`. `out.size == 0` if the file is empty or missing.

#### `readStr`
```fly
public string readStr(const string path)
```
Reads the entire file at `path` and returns its contents as a string.

#### `write`
```fly
public void write(const string path, fly.os.io.Buf data, const int perm)
```
Writes `data` to `path`, creating or truncating the file.

#### `writeStr`
```fly
public void writeStr(const string path, const string content, const int perm)
```
Writes string `content` to `path`, creating or truncating the file.

#### `append`
```fly
public void append(const string path, fly.os.io.Buf data, const int perm)
```
Appends `data` to `path`, creating the file if needed.

#### `appendStr`
```fly
public void appendStr(const string path, const string content, const int perm)
```
Appends string `content` to `path`, creating the file if needed.

---

### Binary read / write

`readStr` and `writeStr` go through the platform CRT in **text mode**. On
Windows that means a read collapses `\r\n` into `\n` and stops at a `0x1A`
byte, and a write expands every `\n` into `\r\n`. For source text that is
harmless; for a tarball, an image or any other byte-exact payload it is silent
corruption. These two switch the descriptor to binary mode before a single byte
moves, and are ordinary text I/O on POSIX, where nothing is translated.

#### `readBytes`
```fly
public string readBytes(const string path)
```
Reads the whole file **verbatim** — no newline translation, no early stop at
`0x1A`. Empty when the file is empty or missing.

#### `writeBytes`
```fly
public void writeBytes(const string path, const string content, const int perm)
```
Writes `content` verbatim, with no newline translation. Use this for any
payload that is not source text.

---

### Seek

#### `seekTo`
```fly
public long seekTo(File f, const long offset, const int whence)
```
Moves the file offset. `whence`: `0`=`SEEK_SET`, `1`=`SEEK_CUR`, `2`=`SEEK_END`. Returns the new offset.

#### `seekPos`
```fly
public long seekPos(File f)
```
Returns the current file offset (`SEEK_CUR` with offset 0).

---

### Metadata

#### `stat` / `lstat`
```fly
public Stat stat(const string path)
public Stat lstat(const string path)
```
`stat` follows symlinks; `lstat` reports the symlink itself.

#### `size`
```fly
public ulong size(const string path)
```
Returns the byte size of the file. Returns `0` if the file does not exist.

#### `exists`
```fly
public bool exists(const string path)
```
Returns `true` if `path` exists (any file type).

#### `isFile` / `isDir`
```fly
public bool isFile(const string path)
public bool isDir(const string path)
```

---

### File operations

#### `truncate`
```fly
public void truncate(const string path, const long size)
```
Truncates the file to exactly `size` bytes (extends with null bytes if shorter).

#### `chmod`
```fly
public void chmod(const string path, const int mode)
```
Changes the permission bits of `path` (e.g. `0644` = `420`).

#### `delete`
```fly
public void delete(const string path)
```
Removes the file or empty directory at `path`.

#### `copy`
```fly
public void copy(const string src, const string dst)
```
Copies the file at `src` to `dst` (creates or truncates `dst`, mode 0644).

#### `move` / `rename`
```fly
public void move(const string src, const string dst)
public void rename(const string src, const string dst)
```
Renames `src` to `dst` (aliases for `rename(2)`).

---

### Directory operations

#### `dirCreate`
```fly
public void dirCreate(const string path, const int perm)
```
Creates a single directory. Fails if any parent is missing.

#### `dirCreateAll`
```fly
public void dirCreateAll(const string path, const int perm)
```
Creates `path` and all missing parent directories (`mkdir -p`).

#### `dirDelete`
```fly
public void dirDelete(const string path)
```
Removes an empty directory.

#### `dirDeleteAll`
```fly
public void dirDeleteAll(const string path)
```
Removes `path` and all its contents recursively (`rm -rf`).

#### `dirRead`
```fly
public DirEntries dirRead(const string path)
```
Returns all entries in the directory at `path`.

---

### Symlinks & temporary files

#### `symlinkCreate`
```fly
public void symlinkCreate(const string target, const string link)
```
Creates a symbolic link `link` pointing to `target`.

#### `symlinkRead`
```fly
public string symlinkRead(const string path)
```
Returns the target of the symbolic link at `path`.

#### `tempFile`
```fly
public string, File tempFile(const string dir, const string pattern)
```
Creates a temporary file in `dir` using `pattern` as a name template. Returns the path and an open `File`.

#### `tempDir`
```fly
public string tempDir(const string dir, const string pattern)
```
Creates a temporary directory. Returns its path.

---

## fly.os.io

Byte-stream I/O interfaces and utilities.

```fly
import fly.os.io
```

### Types

```fly
public struct Buf {
    long  ptr
    ulong size
    ulong cap
}
```
A heap-allocated byte buffer. `ptr` points to the first byte; `size` is the number of valid bytes; `cap` is the total allocated capacity.

```fly
public interface Reader {
    long read(Buf buf, const ulong n)
    void close()
}
```
Sequential byte-stream source. `read` fills `buf` with up to `n` bytes and returns the count read; returns `0` at EOF.

```fly
public interface Writer {
    long write(Buf buf, const ulong n)
    void flush()
    void close()
}
```
Sequential byte-stream sink. `write` sends up to `n` bytes from `buf` and returns the count written.

```fly
public class FileReader : Reader { int fd }
public class FileWriter : Writer { int fd }
public class BufReader  : Reader { ... }
public class BufWriter  : Writer { ... }
```

---

### Constructor helpers

#### `fileReader`
```fly
public Reader fileReader(const int fd)
```
Wraps a raw OS file descriptor as a `Reader`.

#### `fileWriter`
```fly
public Writer fileWriter(const int fd)
```
Wraps a raw OS file descriptor as a `Writer`.

#### `readerNew`
```fly
public BufReader readerNew(Reader inner, const ulong cap)
```
Wraps `inner` in a `BufReader` with a `cap`-byte internal buffer.

#### `writerNew`
```fly
public BufWriter writerNew(Writer inner, const ulong cap)
```
Wraps `inner` in a `BufWriter` with a `cap`-byte internal buffer.

---

### Read utilities

#### `readAll`
```fly
public Buf readAll(Reader r)
```
Reads from `r` until EOF and returns all data in a freshly allocated `Buf`.

#### `readLine`
```fly
public string readLine(Reader r)
```
Reads bytes until `'\n'` or EOF and returns the line content (without the trailing newline).

#### `readExact`
```fly
public Buf readExact(Reader r, const ulong n)
```
Reads **exactly** `n` bytes, looping over short reads.

Neither `readAll` nor a bare read can frame a length-delimited message on a
stream that never ends: `readAll` waits for EOF, and a read is allowed to return
fewer bytes than asked for. The returned `Buf`'s `size` is the only sentinel — a
size below `n` means EOF (or an error) arrived first.

#### `readLines`
```fly
public fly.os.env.StringArray readLines(Reader r)
```
Reads all lines from `r` until EOF and returns them as a `StringArray`.

---

### Binary mode

#### `setBinaryMode`
```fly
public bool setBinaryMode(const int fd)
```
Stops the platform CRT from translating this descriptor's bytes; a **no-op on
POSIX**, where nothing is translated. Returns `false` on error.

On Windows the UCRT opens descriptors in text mode: reads collapse `\r\n` into
`\n` and stop at a `0x1A` byte, writes expand every `\n` into `\r\n`. Any
byte-exact protocol must call this on its descriptors first — a
`Content-Length:` framed stream over stdio is destroyed without it. For files,
use `fs.readBytes` / `fs.writeBytes`, which do this themselves.

---

### Write utilities

#### `writeAll`
```fly
public bool writeAll(Writer w, Buf buf)
```
Writes all bytes of `buf` to `w`. Returns `true` on success.

#### `writeString`
```fly
public bool writeString(Writer w, const string s)
```
Writes the bytes of string `s` to `w`. Returns `true` on success.

---

### Copy utilities

#### `copy`
```fly
public long copy(Reader src, Writer dst)
```
Copies all bytes from `src` to `dst` until EOF. Returns the total byte count.

#### `copyN`
```fly
public long copyN(Reader src, Writer dst, const ulong n)
```
Copies at most `n` bytes from `src` to `dst`. Returns the actual count.

#### `pipe`
```fly
public long pipe(Reader pipe_r, Writer pipe_w)
```
Alias for `copy`.

---

### Console output

#### `print` / `printLn`
```fly
public void print(const string s)
public void printLn(const string s)
```
Write `s` to stdout. `printLn` appends a newline.

#### `printErr` / `printErrLn`
```fly
public void printErr(const string s)
public void printErrLn(const string s)
```
Write `s` to stderr. `printErrLn` appends a newline.

**Example:**

```fly
import fly.os.fs
import fly.os.io

void main() {
    fly.os.fs.File f = fs.open("notes.txt")
    Reader r = io.fileReader(f.fd)
    string line = io.readLine(r)
    io.printLn(line)
    r.close()
}
```

---

## fly.os.path

Path manipulation utilities.

```fly
import fly.os.path
```

### Composition

#### `join`
```fly
public string join(const string base, const string comp)
```
Joins `base` and `comp` with exactly one `/` separator.

#### `joinN`
```fly
public string joinN(const string parts, const int n)
```
Joins `n` path segments from the array `parts`.

#### `absolute`
```fly
public string absolute(const string path)
```
Returns the absolute form of `path` (resolves relative to the current working directory).

#### `normalize`
```fly
public string normalize(const string path)
```
Resolves `.` and `..` components and removes duplicate separators.

#### `normalizeInPlace`
```fly
public void normalizeInPlace(string path)
```
Normalizes `path` in place (modifies the string directly).

#### `rel`
```fly
public string rel(const string base, const string target)
```
Returns the relative path from `base` to `target`.

---

### Decomposition

#### `basename`
```fly
public string basename(const string path)
```
Returns the last path component (filename with extension).

#### `dirname`
```fly
public string dirname(const string path)
```
Returns all but the last path component.

#### `ext`
```fly
public string ext(const string path)
```
Returns the file extension including the leading dot, or an empty string.

#### `stem`
```fly
public string stem(const string path)
```
Returns the filename without extension.

#### `split`
```fly
public string, string split(const string path)
```
Returns `(dirname, basename)` as two values.

#### `splitExt`
```fly
public string, string splitExt(const string path)
```
Returns `(stem, ext)` as two values.

#### `comp`
```fly
public fly.os.env.StringArray comp(const string path)
```
Returns all path components as a `StringArray`.

#### `compCount`
```fly
public int compCount(const string path)
```
Returns the number of path components.

#### `sep`
```fly
public byte sep()
```
Returns the platform path separator byte (`/` on Unix).

---

### Predicates

#### `isAbsolute` / `isRelative`
```fly
public bool isAbsolute(const string path)
public bool isRelative(const string path)
```

#### `isFile` / `isDir` / `isSym`
```fly
public bool isFile(const string path)
public bool isDir(const string path)
public bool isSym(const string path)
```

---

### Glob & pattern matching

#### `glob`
```fly
public fly.os.env.StringArray glob(const string pattern)
```
Returns all paths matching the glob `pattern`.

#### `globCount`
```fly
public int globCount(const string pattern)
```
Returns the count of paths matching `pattern`.

#### `match`
```fly
public bool match(const string pattern, const string name)
```
Returns `true` if `name` matches the glob `pattern`.

---

## fly.os.proc

Spawning child processes. **No shell is involved**: the program is executed
directly with an argument vector, so nothing here depends on `/bin/sh` — which
does not exist on Windows — and no argument needs quoting.

```fly
import fly.os.proc
```

### Types

An **ArgList** is an opaque `pointer` handle, not a class: a header-declared
class carrying a generic does not survive the archive boundary intact. Build it
with `argsNew`, fill it with `argsAdd`, release it with `argsFree`. Pass `0` for
a command that takes no arguments.

### Argument lists

#### `argsNew`
```fly
public pointer argsNew()
```
Allocates an empty ArgList and returns its handle.

#### `argsAdd`
```fly
public void argsAdd(const pointer h, const string a)
```
Appends a **copy** of `a` to the list, growing it if needed.

#### `argsFree`
```fly
public void argsFree(const pointer h)
```
Releases the list and every string it owns.

---

### Execution

#### `exec`
```fly
public int exec(const string path, const pointer h)
```
Spawns `path` with `argv[0] = path` followed by the ArgList arguments (`h` may
be `0`), inherits the environment and **waits**. Returns the child's exit code
(0–255), `128 + signo` if it was killed by a signal, or `-1` if the spawn
failed.

#### `run`
```fly
public int run(const string path)
```
Convenience: `exec` with no extra arguments.

#### `execCapture`
```fly
public int execCapture(const string path, const pointer h, const string outPath)
```
Like `exec`, but the child's **stdout is redirected to `outPath`** (created and
truncated); the caller reads the file afterwards. Same return contract as
`exec`.

**stderr is inherited, not discarded** — a failing command's diagnostics reach
the user instead of vanishing.

```fly
import fly.os.proc
import fly.os.fs

void main() {
    pointer args = proc.argsNew()
    proc.argsAdd(args, "rev-parse")
    proc.argsAdd(args, "HEAD")
    int rc = proc.execCapture("git", args, "head.txt")
    proc.argsFree(args)
    if rc == 0 {
        string sha = fs.readStr("head.txt")
    }
}
```

---

## fly.os.time

Wall clock, monotonic clock, durations, and date formatting.

```fly
import fly.os.time
```

### Types

```fly
public struct Time {
    long sec
    long nsec
}
```
An absolute point in time (seconds + nanoseconds since the Unix epoch). Negative values represent times before 1970.

```fly
public struct Duration {
    long nsec
}
```
An elapsed time interval stored as a nanosecond count.

---

### Current time

#### `now`
```fly
public Time now()
```
Returns the current wall-clock time (UTC, Unix epoch).

#### `monotonic`
```fly
public Time monotonic()
```
Returns the current monotonic clock value. Use for measuring elapsed time.

#### `nowSec`
```fly
public long nowSec()
```
Returns the current Unix timestamp in seconds.

---

### Arithmetic

#### `since`
```fly
public Duration since(Time t)
```
Returns the `Duration` elapsed since `t` (i.e., `now − t`).

#### `diff`
```fly
public Duration diff(Time a, Time b)
```
Returns `b − a`. Negative if `b` is before `a`.

#### `add`
```fly
public Time add(Time t, Duration d)
```
Returns `t` advanced by duration `d`.

#### `compare`
```fly
public int compare(Time a, Time b)
```
Returns `-1` if `a < b`, `0` if equal, `1` if `a > b`.

---

### Conversions

#### `unix` / `unixNano`
```fly
public long unix(Time t)
public long unixNano(Time t)
```
Returns the Unix timestamp in seconds or nanoseconds.

#### `fromUnix` / `fromUnixNano`
```fly
public Time fromUnix(const long sec)
public Time fromUnixNano(const long nsec)
```
Constructs a `Time` from a Unix timestamp.

---

### Duration helpers

#### `durationSecs` / `durationMillis` / `durationMicros`
```fly
public long durationSecs(Duration d)
public long durationMillis(Duration d)
public long durationMicros(Duration d)
```
Returns the duration as seconds, milliseconds, or microseconds.

#### `durationFormat`
```fly
public string durationFormat(Duration d)
```
Returns a human-readable string such as `"1h2m3s"`.

#### `durationFormatNsec`
```fly
public string durationFormatNsec(const long nsec)
```
Same as `durationFormat` but takes a raw nanosecond count.

---

### Formatting & parsing

#### `format`
```fly
public string format(Time t, const string pattern)
```
Formats `t` using a Go-style reference-time pattern. Recognised tokens:

| Token | Meaning |
|---|---|
| `2006` | 4-digit year |
| `01` | month (zero-padded) |
| `02` | day (zero-padded) |
| `15` | hour (24 h) |
| `04` | minute |
| `05` | second |

All other bytes are emitted verbatim.

#### `formatSec`
```fly
public string formatSec(const long sec, const string pattern)
```
Formats a raw Unix timestamp (seconds) using the same pattern tokens.

#### `parse`
```fly
public Time parse(const string s, const string pattern)
```
Parses a date/time string `s` according to `pattern`. Unrecognised bytes are skipped.

#### `parseToSec`
```fly
public long parseToSec(const string s, const string pattern)
```
Same as `parse`, but returns the result as a Unix timestamp in seconds.

---

### Sleep

#### `sleep`
```fly
public void sleep(Duration d)
```
Suspends execution for at least the duration `d`. Returns immediately if `d ≤ 0`.

**Example:**

```fly
import fly.os.time

void main() {
    Time t0 = time.now()
    Duration ms100 = new Duration()
    ms100.nsec = 100000000
    time.sleep(ms100)
    Duration elapsed = time.since(t0)
    string s = time.durationFormat(elapsed)   // e.g. "100ms"
}
```

---

## fly.sync

Thread primitives: threads, mutexes, read-write mutexes, wait groups, once guards, and semaphores.

```fly
import fly.sync
```

### Thread

```fly
public struct Thread { long handle }
```

#### `spawnThread`
```fly
public Thread spawnThread(const long fn, const long arg, const ulong stack)
```
Starts `fn(arg)` in a new OS thread. `stack = 0` uses the default stack size (1 MiB).

---

### Mutex

```fly
public struct Mutex { long state }
```
A simple spin/sleep mutual-exclusion lock.

```fly
public Mutex newMutex()
public void  mutexLock(Mutex m)
public void  mutexUnlock(Mutex m)
public bool  mutexTryLock(Mutex m)
```

---

### RWMutex

```fly
public struct RWMutex { long state; long waiters }
```
A read-write lock: multiple concurrent readers or one exclusive writer.

```fly
public RWMutex newRWMutex()
public void    rwmutexRlock(RWMutex rw)
public void    rwmutexRunlock(RWMutex rw)
public void    rwmutexWlock(RWMutex rw)
public void    rwmutexWunlock(RWMutex rw)
```

---

### WaitGroup

```fly
public struct WaitGroup { long state }
```
Waits for a collection of goroutine-like tasks to finish.

```fly
public WaitGroup newWaitGroup()
public void      wgAdd(WaitGroup wg, const int n)
public void      wgDone(WaitGroup wg)
public void      wgWait(WaitGroup wg)
```

`wgAdd(wg, n)` increments the counter by `n`. `wgDone` decrements by 1. `wgWait` blocks until the counter reaches 0.

---

### Once

```fly
public struct Once { long state }
```
Runs an initialisation block exactly once, regardless of concurrent calls.

```fly
public Once newOnce()
public bool onceAcquire(Once o)
public void onceRelease(Once o)
```

Usage pattern:
```fly
if sync.onceAcquire(o) {
    // initialisation
    sync.onceRelease(o)
}
```

---

### Semaphore

```fly
public struct Semaphore { long state }
```
A counting semaphore.

```fly
public Semaphore newSemaphore(const int initial)
public void      semaphoreAcquire(Semaphore s)
public void      semaphoreRelease(Semaphore s)
public bool      semaphoreTryAcquire(Semaphore s)
```

---

## fly.data

Generic heap-allocated data structures. All collections store `long` values (raw addresses or integers). Use `fly.mem.toPtr` / casting to store typed pointers.

```fly
import fly.data.list    // fly.data.List
import fly.data.stack   // fly.data.Stack
import fly.data.queue   // fly.data.Queue
import fly.data.deque   // fly.data.Deque
import fly.data.set     // fly.data.Set
import fly.data.map     // fly.data.Map
import fly.data.tree    // fly.data.Tree
import fly.data.wrapper // fly.data.Wrapper<T>  (generic)
```

All untyped collections (`List`, `Stack`, `Queue`, …) store `long` values. To build a **typed** collection, wrap values in `Wrapper<T>` and store the wrapper reference — see [Wrapper\<T\>](#wrappert) and the [List\<string\> pattern](#liststring-pattern) below.

---

### List

Dynamic array with O(1) amortised append and O(1) indexed access.

```fly
public class List {
    public List()
    public void add(const long val)
    public long get(const int idx)
    public void set(const int idx, const long val)
    public int  size()
    public void remove(const int idx)
    public void clear()
    public void free()
}
```

---

### Stack

LIFO stack backed by a dynamic array.

```fly
public class Stack {
    public Stack()
    public void push(const long val)
    public long pop()
    public long peek()
    public bool isEmpty()
    public int  size()
    public void free()
}
```

---

### Queue

FIFO queue backed by a linked list.

```fly
public class Queue {
    public Queue()
    public void enqueue(const long val)
    public long dequeue()
    public long peek()
    public bool isEmpty()
    public int  size()
    public void free()
}
```

---

### Deque

Double-ended queue with O(1) push/pop at both ends.

```fly
public class Deque {
    public Deque()
    public void pushBack(const long val)
    public void pushFront(const long val)
    public long popFront()
    public long popBack()
    public long peekFront()
    public long peekBack()
    public bool isEmpty()
    public int  size()
    public void free()
}
```

---

### Set

Hash set with O(1) average add/has/remove.

```fly
public class Set {
    public Set()
    public void add(const long key)
    public bool has(const long key)
    public void remove(const long key)
    public int  size()
    public void free()
}
```

---

### Map

Hash map with `long` keys and `long` values, O(1) average operations.

```fly
public class Map {
    public Map()
    public void put(const long key, const long val)
    public long get(const long key)
    public bool has(const long key)
    public void remove(const long key)
    public int  size()
    public void free()
}
```

---

### Tree

Ordered map backed by a binary search tree, O(log n) operations.

```fly
public class Tree {
    public Tree()
    public void put(const long key, const long val)
    public long get(const long key)
    public bool has(const long key)
    public void remove(const long key)
    public int  size()
    public void free()
}
```

---

### Wrapper\<T\>

Generic single-value holder (requires generics, v0.12+). The type parameter `T` is resolved at compile time via monomorphization — each instantiation (`Wrapper<string>`, `Wrapper<int>`, …) is a distinct, zero-overhead type.

```fly
public class Wrapper<T> {
    public Wrapper(T v)
    public T    get()
    public void set(T v)
}
```

**Basic example:**

```fly
import fly.data.wrapper

void main() {
    // String wrapper
    Wrapper<string> ws = new Wrapper<string>("hello")
    string s = ws.get()   // s = "hello"
    ws.set("world")
    s = ws.get()           // s = "world"

    // Integer wrapper
    Wrapper<int> wi = new Wrapper<int>(42)
    int n = wi.get()       // n = 42
    wi.set(n + 1)
    n = wi.get()            // n = 43
}
```

---

### List\<string\> Pattern

`fly.data.List` stores raw `long` values (object addresses or integers). To maintain a **typed list of strings**, combine `List` with `Wrapper<string>`: each string is boxed into a `Wrapper<string>` instance, and the instance reference is added to the list.

```fly
import fly.data.list
import fly.data.wrapper

void main() {
    List lst = new List()

    // Add strings — box each into a Wrapper<string>
    lst.add(new Wrapper<string>("apple"))
    lst.add(new Wrapper<string>("banana"))
    lst.add(new Wrapper<string>("cherry"))

    int n = lst.size()   // n = 3

    // Read strings — retrieve wrapper, then unwrap
    for int i = 0; i < n; i++ {
        Wrapper<string> item = lst.get(i)
        string text = item.get()
        // use text …
    }

    lst.free()
}
```

The same pattern works for any heap-allocated type — replace `Wrapper<string>` with `Wrapper<int>`, `Wrapper<MyClass>`, etc.

---

**Untyped example (raw long):**

```fly
import fly.data.list
import fly.data.map

void main() {
    List l = new List()
    l.add(10)
    l.add(20)
    int n = l.size()      // n = 2
    long v = l.get(0)     // v = 10

    Map m = new Map()
    m.put(1, 100)
    long val = m.get(1)   // val = 100
    l.free()
    m.free()
}
```

---

## fly.codec

Text encodings. Pure Fly, no FFI. Every decoder reports failure through an
`errOut` parameter rather than by returning an empty string — decoding an empty
input legitimately yields `""`.

```fly
import fly.codec
```

### Base64

#### `base64Encode`
```fly
public string base64Encode(const string s)
```
RFC 4648 §4: the `+/` alphabet, `=` padded. Returns `""` only for empty input.

#### `base64Decode`
```fly
public string base64Decode(const string s, int errOut)
```
Accepts **either** alphabet and optional padding. `errOut`: `0` ok, `1` bad
character (whitespace included), `2` impossible length, `3` non-zero leftover
bits.

#### `base64UrlEncode`
```fly
public string base64UrlEncode(const string s)
```
RFC 4648 §5: the `-_` alphabet, **no padding** — safe in a URL path, a query
value or a JWT segment.

#### `base64UrlDecode`
```fly
public string base64UrlDecode(const string s, int errOut)
```
The inverse; identical to `base64Decode`, which already accepts both alphabets.

---

### Percent-encoding

#### `percentEncode`
```fly
public string percentEncode(const string s)
```
Escapes everything outside the RFC 3986 unreserved set **except** the URL
structure characters (`/ : ? # [ ] @ ! $ & ' ( ) * + , ; =`), so a whole path or
URL passes through intact. Never fails.

#### `percentEncodeComponent`
```fly
public string percentEncodeComponent(const string s)
```
Escapes the reserved characters too, leaving only `A-Za-z0-9` and `- . _ ~`.
This is what a single query **value** or path **segment** needs: a `/` or `&`
inside one must not be read as structure. Never fails.

#### `percentDecode`
```fly
public string percentDecode(const string s, int errOut)
```
The inverse of both encoders, accepting hex escapes in either case. `errOut`:
`0` ok, `2` a truncated escape at the end of the input, `4` a `%` not followed
by two hex digits.

A `+` is left **alone** rather than turned into a space: that substitution
belongs to `application/x-www-form-urlencoded`, not to percent-encoding, and
applying it here would corrupt any path or JSON body containing a plus.

---

### Hex

#### `hexEncode`
```fly
public string hexEncode(const string s)
```
Two **lowercase** hex characters per byte. Never fails.

#### `hexDecode`
```fly
public string hexDecode(const string s, int errOut)
```
The inverse, accepting either case. `errOut`: `0` ok, `1` a non-hex character,
`2` an odd length.

---

## fly.hash

SHA-256 and CRC-32, in pure Fly.

```fly
import fly.hash
```

#### `sha256Hex`
```fly
public string sha256Hex(const string data)
```
The bare 64-character lowercase hex digest of a string's bytes.

#### `sha256String`
```fly
public string sha256String(const string data)
```
`"sha256:<hex>"` — the prefixed form the lockfile and the registry protocol use.

#### `sha256File`
```fly
public string sha256File(const string path)
```
`"sha256:<hex>"` of a file's contents; `""` when the file is missing. Reads the
file in binary mode, so the digest is the same on every platform.

#### `sha256Verify`
```fly
public bool sha256Verify(const string path, const string expected)
```
`true` when the file hashes to `expected` (`"sha256:<hex>"`). A missing file
hashes to `""` and therefore never matches.

#### `crc32`
```fly
public uint crc32(const string data)
```
The CRC-32 of a string's bytes. Empty input is `0`.

#### `crc32Hex`
```fly
public string crc32Hex(const string data)
```
The CRC-32 as 8 lowercase hex characters.

---

## fly.json

A JSON reader and writer.

```fly
import fly.json
```

A value is an **opaque handle** (`pointer`), not a class. Ownership is a tree:
`freeValue` releases a value and everything below it, and a value handed to
`add`/`put` is **adopted** by its parent — never free it yourself afterwards.

`parse` reports failure through `errOut`, using codes `301`–`310` (`301` bad
literal, `302` unexpected byte, `303` unterminated string, `304` bad escape,
`305` bad array element, `306` missing object key, `307` missing `:`, `308` bad
object member, `309` malformed `\u`, `310` unpaired surrogate). The returned
handle is never `0`: on error it is a partially-built tree the caller must
still free.

> Numbers keep their **raw source text** — std has no string→double conversion
> yet — so `asNumber` always returns `0.0`. Read numbers with `asInt`, or
> `asString` for the literal.

### Reading

#### `parse`
```fly
public pointer parse(const string src, int errOut)
```
JSON text → the root value.

#### `kind`
```fly
public int kind(const pointer v)
```
`0` null, `1` bool, `2` number, `3` string, `4` array, `5` object; `-1` for a
null handle.

#### `isNull` · `isBool` · `isNumber` · `isString` · `isArray` · `isObject`
```fly
public bool isNull(const pointer v)
public bool isBool(const pointer v)
public bool isNumber(const pointer v)
public bool isString(const pointer v)
public bool isArray(const pointer v)
public bool isObject(const pointer v)
```
Kind predicates.

#### `asBool` · `asString` · `asInt` · `asNumber`
```fly
public bool asBool(const pointer v)
public string asString(const pointer v)
public int asInt(const pointer v)
public double asNumber(const pointer v)
```
`asString` also returns a **number's raw literal text**. `asInt` takes the
integral part of that literal (a leading `-` is honoured, a fraction or exponent
ignored). Each returns the zero value for any other kind.

#### `size` · `at`
```fly
public int size(const pointer v)
public pointer at(const pointer v, const int i)
```
Array length, and element `i` — `0` when `v` is not an array or `i` is out of
range.

#### `keyCount` · `keyAt` · `has` · `get`
```fly
public int keyCount(const pointer v)
public string keyAt(const pointer v, const int i)
public bool has(const pointer v, const string key)
public pointer get(const pointer v, const string key)
```
Object members, in **insertion order**. `get` returns `0` when the key is absent
or `v` is not an object.

#### `freeValue`
```fly
public void freeValue(const pointer v)
```
Releases a value and everything below it. Safe on a null handle.

---

### Building

#### `newNull` · `newBool` · `newInt` · `newNumber` · `newString` · `newArray` · `newObject`
```fly
public pointer newNull()
public pointer newBool(const bool b)
public pointer newInt(const int n)
public pointer newNumber(const string literal)
public pointer newString(const string s)
public pointer newArray()
public pointer newObject()
```
`newNumber` takes the literal text verbatim (std has no double→string
formatter). `newString` stores the value **raw**; escaping happens at write
time.

#### `add`
```fly
public void add(const pointer arr, const pointer child)
```
Appends `child` to an array, which **adopts** it. No-op when `arr` is not an
array.

#### `put`
```fly
public void put(const pointer obj, const string key, const pointer child)
```
Sets `key` on an object, which **adopts** the value. Replacing an existing key
keeps its original position and frees the old value.

---

### Writing

#### `write`
```fly
public string write(const pointer v)
```
Compact serialization. A null handle writes `null`.

#### `writePretty`
```fly
public string writePretty(const pointer v, const int indent)
```
`indent` spaces per nesting level, newline-separated. `indent <= 0` behaves
exactly like `write`.

#### `escape` · `unescape`
```fly
public string escape(const string s)
public string unescape(const string s, int errOut)
```
The JSON-escaped **body** of a string, without the surrounding quotes, and its
inverse. `unescape` decodes the full escape set including `\uXXXX` and surrogate
pairs (UTF-8 encoded); `errOut` is `0`, `304`, `309` or `310`.

```fly
import fly.json
import fly.os.io

void main() {
    pointer root = json.newObject()
    json.put(root, "name", json.newString("fly"))

    pointer tags = json.newArray()
    json.add(tags, json.newString("lang"))
    json.put(root, "tags", tags)          // the object ADOPTS tags

    string text = json.write(root)        // {"name":"fly","tags":["lang"]}
    io.printLn(text)

    int err = 0
    pointer back = json.parse(text, err)
    io.printLn(json.asString(json.get(back, "name")))   // fly

    json.freeValue(back)
    json.freeValue(root)                  // frees tags too
}
```

> A JSON document cannot be written as a Fly string literal: escapes are kept
> **raw**, so `"{\"a\":1}"` is the literal characters `{\"a\":1}` and does not
> parse. Build documents with the constructors above, or read the text from a
> file or a socket.

---

## fly.net

Blocking IPv4 sockets — TCP client and server, UDP datagrams, and poll sets.

```fly
import fly.net
```

This module declares **no classes and no structs**: every parameter and return
is a plain `long`/`int`/`pointer`/`string`, so the generated header is a flat
list of function signatures.

**Errors are sentinels, not `fail`/`handle`.** A socket is a `long` and
**negative means invalid**: every function that yields a socket returns `< 0` on
failure, and every function that takes one treats a negative handle as an
immediate failure — an unchecked handle propagates harmlessly instead of
faulting.

**Addresses are packed host-order ints**: `a<<24 | b<<16 | c<<8 | d`, so
`127.0.0.1` is `2130706433`. There is no `htons` or `inet_addr` anywhere; the
network-order conversion happens inside the runtime.

> IPv6 is not supported. Do not call the `fly.runtime` socket externs directly
> even though the header exports them — their signatures are platform-specific
> (a socket is an `int` fd on POSIX and a 64-bit `SOCKET` on Windows).

### Addressing

#### `ipv4`
```fly
public int ipv4(const int a, const int b, const int c, const int d)
```
Packs four octets into an address; octets are masked to 0–255.

#### `loopback` · `anyAddr`
```fly
public int loopback()
public int anyAddr()
```
`127.0.0.1` and `0.0.0.0` (the wildcard bind address).

#### `ipToStr`
```fly
public string ipToStr(const int addr)
```
`"a.b.c.d"` for a packed address. Never empty.

#### `parseIp`
```fly
public int parseIp(const string s)
```
Dotted-quad text → packed address; `0` when `s` is not one.

---

### DNS and peers

#### `resolve`
```fly
public int resolve(const string host)
```
Host name or dotted-quad → packed IPv4; a literal address is parsed without a
lookup.

#### `resolveAll` · `addrCount` · `addrAt` · `addrFree`
```fly
public pointer resolveAll(const string host)
public int addrCount(const pointer h)
public int addrAt(const pointer h, const int i)
public void addrFree(const pointer h)
```
Every IPv4 address for `host` as an opaque list; `resolveAll` returns `0` when
the name does not resolve. `addrFree` is safe on a null handle.

#### `reverseDns`
```fly
public string reverseDns(const int addr)
```
The host name registered for a packed address, or `""`.

#### `peerAddr` · `peerPort`
```fly
public int peerAddr(const long s)
public int peerPort(const long s)
```
The other end of a connected socket; `0` / `-1` when not connected.

---

### Socket lifecycle

#### `socket`
```fly
public long socket()
```
A blocking IPv4 TCP socket; `< 0` on failure.

#### `close`
```fly
public void close(const long s)
```
Releases a socket. A negative handle is ignored.

#### `shutdownWrite` · `shutdownBoth`
```fly
public bool shutdownWrite(const long s)
public bool shutdownBoth(const long s)
```
Half-close the sending side so the peer sees EOF while this side can still
read, or stop both directions.

---

### Socket options

#### `reuseAddr`
```fly
public bool reuseAddr(const long s)
```
Allows rebinding a port still in `TIME_WAIT`. POSIX only.

#### `setRecvTimeout` · `setSendTimeout`
```fly
public bool setRecvTimeout(const long s, const int ms)
public bool setSendTimeout(const long s, const int ms)
```
Bound how long a blocking receive or send waits.

#### `setNonBlocking`
```fly
public bool setNonBlocking(const long s, const bool on)
```
When on, an operation that would block fails instead.

#### `setNoDelay` · `setKeepAlive` · `setBroadcast`
```fly
public bool setNoDelay(const long s, const bool on)
public bool setKeepAlive(const long s, const bool on)
public bool setBroadcast(const long s, const bool on)
```
`TCP_NODELAY`, `SO_KEEPALIVE`, and `SO_BROADCAST` (required before a datagram
socket may send to a broadcast address).

#### `setLinger`
```fly
public bool setLinger(const long s, const bool on, const int sec)
```
`SO_LINGER`: with `on`, `close` blocks for up to `sec` seconds while unsent data
drains.

#### `setRecvBuf` · `setSendBuf`
```fly
public bool setRecvBuf(const long s, const int bytes)
public bool setSendBuf(const long s, const int bytes)
```
Kernel buffer sizes; the OS may round the value.

#### `sockError`
```fly
public int sockError(const long s)
```
The socket's pending error code, **cleared by reading it**. `0` means none.

---

### Server

#### `bind` · `listen` · `accept`
```fly
public bool bind(const long s, const int addr, const int port)
public bool listen(const long s, const int backlog)
public long accept(const long s)
```
`port` `0` requests an ephemeral port — read it back with `localPort`. `accept`
blocks and returns the new socket, `< 0` on error.

#### `localPort`
```fly
public int localPort(const long s)
```
The port the socket is actually bound to, in host order; `-1` on error.

#### `listenOn`
```fly
public long listenOn(const int addr, const int port, const int backlog)
```
`socket` + `reuseAddr` + `bind` + `listen` in one call.

---

### Client

#### `connect` · `connectTo`
```fly
public bool connect(const long s, const int addr, const int port)
public long connectTo(const string host, const int port)
```
`connectTo` resolves, creates the socket and connects; `< 0` on any failure.

---

### Transfer

#### `send` · `sendAll` · `sendStr`
```fly
public long send(const long s, const pointer buf, const int n)
public bool sendAll(const long s, const pointer buf, const int n)
public bool sendStr(const long s, const string data)
```
`send` is **one** attempt and may write fewer bytes than asked; `sendAll` loops
until everything is out. Sending an empty string succeeds without touching the
socket.

#### `recv` · `recvExact`
```fly
public long recv(const long s, const pointer buf, const int n)
public long recvExact(const long s, const pointer buf, const int n)
```
`recv` is one blocking receive — `0` at EOF, `< 0` on error. `recvExact` loops
over short reads; a result below `n` means EOF or an error arrived first.

#### `recvStr` · `recvUpTo` · `recvAll`
```fly
public string recvStr(const long s, const int max)
public string recvUpTo(const long s, const int limit)
public string recvAll(const long s)
```
One receive into a fresh string; read until EOF or `limit` bytes; read until EOF.

---

### UDP

#### `socketUdp`
```fly
public long socketUdp()
```
A blocking IPv4 UDP socket; bind it with `bind`.

#### `fromNew` · `fromAddr` · `fromPort` · `fromFree`
```fly
public pointer fromNew()
public int fromAddr(const pointer from)
public int fromPort(const pointer from)
public void fromFree(const pointer from)
```
The 8-byte scratch that `recvFrom` fills with the sender's address and port.

#### `sendTo` · `sendStrTo`
```fly
public long sendTo(const long s, const pointer buf, const int n, const int addr, const int port)
public bool sendStrTo(const long s, const string data, const int addr, const int port)
```
One datagram to `addr:port`.

#### `recvFrom` · `recvStrFrom`
```fly
public long recvFrom(const long s, const pointer buf, const int n, const pointer from)
public string recvStrFrom(const long s, const int max, const pointer from)
```
One datagram, with the sender written into `from`.

---

### Poll sets

Watch several sockets at once. A poll set is an opaque handle holding entries;
each entry has an index that stays valid until a removal.

#### `pollRead` · `pollWrite` · `pollErr` · `pollHup`
```fly
public int pollRead()    // 1
public int pollWrite()   // 2
public int pollErr()     // 4
public int pollHup()     // 8
```
The event bits. `pollErr` and `pollHup` are **always reported** whether or not
they were requested.

#### `pollNew` · `pollFree` · `pollCount`
```fly
public pointer pollNew()
public void pollFree(const pointer ps)
public int pollCount(const pointer ps)
```
`pollFree` does **not** close the watched sockets — they belong to the caller.

#### `pollAdd` · `pollSet` · `pollRemove`
```fly
public int pollAdd(const pointer ps, const long s, const int events)
public bool pollSet(const pointer ps, const int i, const int events)
public bool pollRemove(const pointer ps, const int i)
```
`pollAdd` returns the new entry's index. **`pollRemove` moves the last entry
into the hole**, so indices after a removal are not stable.

#### `pollSocketAt` · `pollEventsAt`
```fly
public long pollSocketAt(const pointer ps, const int i)
public int pollEventsAt(const pointer ps, const int i)
```
What is registered at entry `i`.

#### `pollWait` · `pollRevents` · `pollIsReady`
```fly
public int pollWait(const pointer ps, const int timeoutMs)
public int pollRevents(const pointer ps, const int i)
public bool pollIsReady(const pointer ps, const int i, const int mask)
```
`pollWait` blocks until at least one socket is ready and returns how many are,
`0` on timeout, `-1` on error; then read each entry's `pollRevents`.

```fly
import fly.net

void main() {
    long srv = net.listenOn(net.loopback(), 0, 8)
    int port = net.localPort(srv)

    long c = net.connectTo("127.0.0.1", port)
    net.sendStr(c, "ping")

    long conn = net.accept(srv)
    string msg = net.recvStr(conn, 64)      // "ping"

    net.close(conn)
    net.close(c)
    net.close(srv)
}
```

---

## fly.net.tls

TLS over a `fly.net` socket.

```fly
import fly.net.tls
```

**Availability is a runtime fact, not a compile-time one** — ask
`tlsAvailable()` before relying on any of this. A real backend is linked only
when the program was built with [`--tls`](@/docs/Fly-CLI.md); without it the
link picks up a no-op stub, so a program that never asked for TLS still links on
a machine with no libssl, and `tlsAvailable()` tells the truth instead of
failing mysteriously at handshake time.

| Platform | Backend with `--tls` |
|---|---|
| Linux | OpenSSL (1.1.0 or later) |
| Windows | Schannel |
| macOS | none — `tlsAvailable()` is `false` |

A connection is an opaque `pointer`, `0` = invalid.

**Verification is on by default** and is a per-connection parameter — there is
no global switch. Turning it off accepts *any* certificate and defeats the point
of TLS; it exists only for a host with a self-signed certificate you already
trust by other means.

#### `tlsAvailable`
```fly
public bool tlsAvailable()
```
`true` when a real backend is linked into this program.

#### `tlsConnect` · `tlsConnectOpts`
```fly
public pointer tlsConnect(const string host, const int port)
public pointer tlsConnectOpts(const string host, const int port, const bool verify, const int timeoutMs)
```
Resolve, connect and handshake in one call. `tlsConnect` is `tlsConnectOpts`
with verification **on** and no timeout. Both return `0` on any failure, having
closed the socket they opened.

#### `tlsWrap`
```fly
public pointer tlsWrap(const long sock, const string sniHost, const bool verify)
```
Puts an already-connected socket into a TLS session. `sniHost` is sent as the
SNI server name and, when `verify`, checked against the certificate. The socket
becomes **owned** by the connection.

#### `tlsSocket`
```fly
public long tlsSocket(const pointer c)
```
The underlying socket, for use in a poll set; `-1` for a null handle. Reading or
writing it directly would bypass the encryption.

#### `tlsSend` · `tlsSendStr`
```fly
public long tlsSend(const pointer c, const pointer buf, const int n)
public bool tlsSendStr(const pointer c, const string data)
```
One write. Unlike a raw socket this is either all of `n` or an error — the
record layer does not do short writes.

#### `tlsRecv` · `tlsRecvStr` · `tlsRecvAll`
```fly
public long tlsRecv(const pointer c, const pointer buf, const int n)
public string tlsRecvStr(const pointer c, const int max)
public string tlsRecvAll(const pointer c)
```
One blocking read (`0` at a clean end of session, `< 0` on error or timeout),
one read into a fresh string, or everything until the session ends.

#### `tlsLastError`
```fly
public int tlsLastError(const pointer c)
```
The backend's raw error code for the last failed operation, `0` when none. Not
translated.

#### `tlsClose` · `tlsFree`
```fly
public void tlsClose(const pointer c)
public void tlsFree(const pointer c)
```
`tlsClose` sends `close_notify`, releases the session **and closes the socket**.
`tlsFree` releases the session and leaves the socket open. Both are safe on a
null handle — and after either, the handle is dead.

---

## fly.net.http

HTTP/1.1 over `fly.net`: parse a request, build a response, and a small
blocking client.

```fly
import fly.net.http
```

There are **no Request/Response objects**: every accessor takes the raw message
and re-scans it. That keeps the whole parse/serialize surface pure string-in,
string-out — testable without a socket.

> **CRLF trap:** Fly string literals keep escapes raw, so `"\r\n"` is four
> characters, not a line terminator. Build message text with the functions here
> rather than by hand.

### Parsing a request

#### `method` · `target` · `version` · `path` · `query`
```fly
public string method(const string raw)
public string target(const string raw)
public string version(const string raw)
public string path(const string raw)
public string query(const string raw)
```
Request-line fields; `""` when it is unparseable. `target` includes the query,
`path` is the part before `?`.

#### `header` · `contentLength`
```fly
public string header(const string raw, const string name)
public int contentLength(const string raw)
```
Case-insensitive header lookup, value trimmed. `contentLength` is `-1` when the
header is absent or not a number.

#### `headerEnd` · `body` · `isComplete`
```fly
public int headerEnd(const string raw)
public string body(const string raw)
public bool isComplete(const string raw)
```
Index of the first byte after the `CRLFCRLF`, the body, and whether the whole
message has arrived — `isComplete` is the receive loop's stop condition.

#### `isChunked` · `dechunk` · `bodyDecoded`
```fly
public bool isChunked(const string raw)
public string dechunk(const string body)
public string bodyDecoded(const string raw)
```
Chunked transfer coding. `bodyDecoded` is the one to reach for: it returns the
body with the transfer coding already removed.

#### `queryParam`
```fly
public string queryParam(const string raw, const string key)
```
The value of `key` in the query string; `""` when absent.

---

### Building a response

#### `statusText`
```fly
public string statusText(const int status)
```
The reason phrase for a status code; `"Unknown"` for anything unrecognised.

#### `response` · `responseWith` · `responseConn`
```fly
public string response(const int status, const string contentType, const string bodyStr)
public string responseWith(const int status, const string contentType, const string extraHeaders, const string bodyStr)
public string responseConn(const int status, const string contentType, const string extraHeaders, const bool keepAlive, const string bodyStr)
```
A complete HTTP/1.1 response. The first two send `Connection: close`;
`responseConn` chooses the disposition.

#### `joinHeaders`
```fly
public string joinHeaders(const string a, const string b)
```
Combines two header lines into one `extraHeaders` argument.

---

### Building a request

#### `buildRequest` · `buildRequestConn`
```fly
public string buildRequest(const string method, const string targetPath, const string host, const string extraHeaders, const string bodyStr)
public string buildRequestConn(const string method, const string targetPath, const string host, const string extraHeaders, const string bodyStr, const bool keepAlive)
```
A complete request; `buildRequest` sends `Connection: close`, i.e. one exchange
per connection.

#### `basicAuth`
```fly
public string basicAuth(const string user, const string password)
```
The value for an `Authorization` header: `"Basic "` followed by the base64 of
`user:password`.

---

### Reading a response

#### `respStatus` · `respHeader` · `respBody`
```fly
public int respStatus(const string raw)
public string respHeader(const string raw, const string name)
public string respBody(const string raw)
```
Status code (`-1` when unparseable), case-insensitive header lookup, and body.

---

### URLs and redirects

#### `urlScheme` · `urlHost` · `urlPort` · `urlPath`
```fly
public string urlScheme(const string url)
public string urlHost(const string url)
public int urlPort(const string url)
public string urlPath(const string url)
```
`urlPort` falls back to 80 for `http` and 443 for `https`; `urlPath` is `"/"`
when the URL has none.

#### `isRedirect` · `resolveUrl` · `redirectTarget`
```fly
public bool isRedirect(const int status)
public string resolveUrl(const string baseUrl, const string location)
public string redirectTarget(const string resp, const string baseUrl)
```
`isRedirect` is a 3xx carrying a `Location` worth following (304 is not).
`resolveUrl` makes an absolute URL from a `Location` value and the URL it came
from.

---

### Multipart bodies

#### `mpNew` · `mpContentType` · `mpAddField` · `mpAddFile` · `mpFinish` · `mpFree`
```fly
public pointer mpNew(const string boundary)
public string mpContentType(const pointer h)
public void mpAddField(const pointer h, const string name, const string value)
public void mpAddFile(const pointer h, const string name, const string filename, const string contentType, const string content)
public string mpFinish(const pointer h)
public void mpFree(const pointer h)
```
A `multipart/form-data` builder. `boundary` must not occur in any part's
content. `mpContentType` gives the value for the request's `Content-Type`
header; `mpFinish` appends the closing delimiter and returns the body.

---

### Client

#### `exchange`
```fly
public string exchange(const string host, const int port, const string rawRequest)
```
Connect, send one request, read the whole response, close.

#### `get` · `getFollow` · `getStatus`
```fly
public string get(const string url)
public string getFollow(const string url, const int maxHops)
public int getStatus(const string url)
```
GET a URL and return the **body** with the transfer coding removed, optionally
following up to `maxHops` redirects; or just the status code (`-1` on failure).

#### `download` · `postFile`
```fly
public bool download(const string url, const string destPath)
public bool postFile(const string url, const string filePath, const string token)
```
GET a URL into a file (written in binary mode), and POST a file's contents as
`application/octet-stream` with an optional bearer token.

---

### Keep-alive connections

#### `connOpen` · `connExchange` · `connIsClosing` · `connClose`
```fly
public long connOpen(const string host, const int port, const int timeoutMs)
public string connExchange(const long s, const string rawRequest)
public bool connIsClosing(const string resp)
public void connClose(const long s)
```
A connection intended for several exchanges. Check `connIsClosing` on each
response: it is `true` when the server will not accept another request.

---

### HTTPS

#### `httpsAvailable` · `httpsSupported`
```fly
public bool httpsAvailable()
public bool httpsSupported()
```
`httpsAvailable` is the whole-client backend (WinHTTP on Windows);
`httpsSupported` is `true` when an `https` URL can be served **at all**, by
either that or `fly.net.tls`.

#### `getSecure` · `getSecureStatus` · `downloadSecure` · `postFileSecure`
```fly
public string getSecure(const string url)
public int getSecureStatus(const string url)
public bool downloadSecure(const string url, const string destPath)
public bool postFileSecure(const string url, const string filePath, const string token)
```
The `https` counterparts. The plain `get`/`download`/`postFile` above dispatch
to these automatically for an `https` URL.

When no backend is available an `https` URL **fails** rather than silently
downgrading to plaintext, and nothing falls back to an external tool.

```fly
import fly.net.http

void main() {
    string body = http.get("http://example.com/v1/pkg")
    int code = http.getStatus("http://example.com/v1/pkg")   // 200
}
```

---

## fly.targz

Gzip decompression (DEFLATE) and the ustar archive format — pure Fly, no FFI, no
platform branches. This is what removes `tar` from the package-manager path.

```fly
import fly.targz
```

Compression is deliberately **not** implemented: nothing in the toolchain
produces archives, only consumes them.

Every entry point takes an `errOut` and the caller checks it rather than the
emptiness of the result — inflating an empty stream legitimately yields `""`.

| Code | Meaning |
|---|---|
| 1 | truncated input — the stream ended mid-symbol |
| 2 | not a gzip stream (bad magic) |
| 3 | unsupported gzip method or reserved flag |
| 4 | invalid DEFLATE block type |
| 5 | invalid Huffman code |
| 6 | a back-reference pointing before the start of the output |
| 7 | CRC-32 mismatch — the payload is corrupt |
| 8 | ISIZE mismatch — the payload is truncated |
| 9 | bad tar header checksum — not an ustar archive, or corrupt |
| 10 | an entry could not be written to disk |
| 11 | an entry whose path escapes the destination directory |

### Decompression

#### `isGzip`
```fly
public bool isGzip(const string data)
```
`true` when the data starts with the gzip magic and names DEFLATE as its method.
Cheap enough to use as a dispatch test.

#### `gunzip`
```fly
public string gunzip(const string data, int errOut)
```
Decompresses a complete gzip member and **verifies** it: the CRC-32 catches
corruption in transit and the ISIZE catches truncation, which a CRC alone would
miss on a stream that happens to end at a block boundary.

#### `inflate`
```fly
public string inflate(const string data, int errOut)
```
Decompresses a raw DEFLATE stream, with no gzip or zlib wrapper.

---

### Reading an archive

#### `tarOpen` · `tarNext` · `tarFree`
```fly
public pointer tarOpen(const string data)
public bool tarNext(const pointer h)
public void tarFree(const pointer h)
```
A reader over the archive bytes, owning its own copy. `tarNext` advances to the
next **real** entry — GNU long-name and pax extension records are consumed
rather than reported — and returns `false` at the end of the archive *or* on
error.

#### `tarName` · `tarSize` · `tarType` · `tarIsFile` · `tarIsDir` · `tarData`
```fly
public string tarName(const pointer h)
public long tarSize(const pointer h)
public int tarType(const pointer h)
public bool tarIsFile(const pointer h)
public bool tarIsDir(const pointer h)
public string tarData(const pointer h)
```
The current entry. `tarType` is the raw typeflag (`48`/`0` regular, `53`
directory, `50` symlink); the two predicates spare the caller the ASCII codes.

#### `tarErr`
```fly
public int tarErr(const pointer h)
```
`0` while the archive reads cleanly, `9` once a header failed its checksum —
`tarNext` returning `false` means "end of archive" only when this is `0`.

---

### Extracting

#### `safeName`
```fly
public bool safeName(const string name)
```
Rejects any entry path that would write outside the destination: an absolute
path, a Windows drive-qualified path, or one containing a `..` segment.
Archives arrive off the network from a package registry; without this check a
hostile — or merely careless — tarball can drop a file anywhere the compiler
can write.

#### `extractTar` · `extractTarGz`
```fly
public bool extractTar(const string data, const string destDir, int errOut)
public bool extractTarGz(const string archivePath, const string destDir, int errOut)
```
Unpack an in-memory ustar archive, or read a `.tar.gz` from disk, verify it and
unpack it — creating parent directories as needed and stopping at the first
problem. `extractTarGz` is the whole of what `tar -xzf` was being spawned for.

---

## fly.log

Leveled logging with timestamps, an optional file sink, and the function-trace
hooks behind the compiler's [`--trace-calls`](@/docs/Fly-CLI.md)
instrumentation.

```fly
import fly.log
import fly.log.LogLevel
```

Every line goes to **stderr** as `YYYY-MM-DD HH:MM:SS [LEVEL] message`;
`setFile` mirrors it into an append-mode file as well. A message prints when
its level's value is **≤** the configured threshold: `ERROR` passes every
threshold, `TRACE` only the most verbose one.

The threshold is lazy: the first emission (or `getLevel`/`isOff`) reads the
**`FLY_LOG_LEVEL`** environment variable — a level name in any case, or `off`
to silence everything; unset or unknown falls back to `INFO`. A program that
calls `setLevel`/`setLevelName`/`setOff` first wins, and the environment is
never consulted. So an instrumented binary is **silent by default** and turns
into a tracer with nothing but `FLY_LOG_LEVEL=trace` in the environment.

The call-depth indentation kept by the trace hooks is process-global and not
thread-safe; tracing assumes a single-threaded program.

### Levels

```fly
public enum LogLevel { ERROR, WARN, INFO, DEBUG, TRACE }
```

The severity ladder. `.value` runs `1` (`ERROR`) to `5` (`TRACE`); `.name` is
the tag printed between brackets.

#### `setLevel`
```fly
public void setLevel(const LogLevel level)
```
Sets the threshold. A message prints when its level's value is ≤ this one.

#### `setLevelName`
```fly
public void setLevelName(const string name)
```
`setLevel` by name, case-insensitive (`"error"`…`"trace"`); `"off"` behaves
like `setOff`. An unknown name is ignored.

#### `setOff` · `isOff`
```fly
public void setOff()
public bool isOff()
```
`setOff` silences every level — there is deliberately no `OFF` enum entry, so
"off" is a state, not a level. `isOff` reports it.

#### `getLevel`
```fly
public LogLevel getLevel()
```
The effective threshold (reading the environment first if nothing was set).
When `isOff()` it returns `ERROR`, the least verbose defined entry — check
`isOff()` to distinguish.

#### `parseLevel` · `isLevelName`
```fly
public LogLevel parseLevel(const string name)
public bool isLevelName(const string name)
```
Name → entry, case-insensitive; an unknown name parses to `INFO`.
`isLevelName` tells whether the name is one of the five entries.

#### `setFile`
```fly
public void setFile(const string path)
```
Mirrors every printed line into `path`, appending (the file is created on the
first write). The stderr sink stays active.

#### `reset`
```fly
public void reset()
```
Back to the pristine state: threshold unset (the next emission re-reads
`FLY_LOG_LEVEL`), depth zero, file sink off. Mainly for tests.

### Messages

#### `trace` · `debug` · `info` · `warn` · `err`
```fly
public void trace(const string msg)
public void debug(const string msg)
public void info(const string msg)
public void warn(const string msg)
public void err(const string msg)
```
One function per level. The ERROR-level one is `err`, not `error` — `error` is
a reserved type keyword (the `io.printErr` precedent).

```fly
log.setLevel(LogLevel.DEBUG)
log.info("server listening")     // 2026-07-31 15:00:27 [INFO] server listening
log.debug("cache warmed")        // printed: DEBUG ≤ DEBUG
log.trace("byte-level detail")   // filtered: TRACE > DEBUG
```

### Call tracing

The hooks `--trace-calls` injects at every function entry and exit. They are
ordinary public functions, so hand-written instrumentation can call them too.
All three print at `TRACE` with two spaces of indentation per call depth.

#### `enter`
```fly
public void enter(const string name)
```
Logs `> name`, then goes one level deeper.

#### `exit` · `exitFail`
```fly
public void exit(const string name)
public void exitFail(const string name)
```
One level back up, then `< name` — or `<! name` for a function left through an
unhandled `fail`.

> The mangled symbols of these three functions are part of the compiler's ABI:
> `--trace-calls` emits calls to them by symbol. Renaming them or changing
> their signatures breaks the instrumentation (the compiler's CodeGenTraceSuite
> pins the symbols).

---

## fly.assert

Test assertion helpers. All functions call `exit(code)` on failure.

```fly
import fly.assert
```

#### `errExit`
```fly
public void errExit(const int code)
```
Exits the process with `code` unconditionally.

#### `assertEqI`
```fly
public void assertEqI(const int got, const int exp, const int code)
```
Asserts `got == exp` (integers).

#### `assertEqL`
```fly
public void assertEqL(const long got, const long exp, const int code)
```
Asserts `got == exp` (longs).

#### `assertStr`
```fly
public void assertStr(const string got, const string exp, const int code)
```
Asserts `got == exp` (strings).

#### `assertNotEmpty`
```fly
public void assertNotEmpty(const string s, const int code)
```
Asserts that `s` is not empty.

#### `assertTrue` / `assertFalse`
```fly
public void assertTrue(const bool b, const int code)
public void assertFalse(const bool b, const int code)
```

#### `assertGtI`
```fly
public void assertGtI(const int got, const int threshold, const int code)
```
Asserts `got > threshold`.

#### `assertApprox`
```fly
public void assertApprox(const double got, const double exp, const int code)
```
Asserts `|got − exp| ≤ 1e-9`.

#### `assertApproxEps`
```fly
public void assertApproxEps(const double got, const double exp, const double eps, const int code)
```
Asserts `|got − exp| ≤ eps`.

**Example:**

```fly
import fly.assert
import fly.str

void main() {
    int n = str.len("hello")
    assert.assertEqI(n, 5, 1)        // passes
    assert.assertTrue(n > 0, 2)      // passes
}
```
