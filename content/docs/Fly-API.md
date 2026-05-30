+++
title = "Fly API"
description = "Reference for the Fly standard library — fly.str, fly.math, fly.mem, fly.os, fly.sync, fly.data, fly.assert."
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

main() {
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

main() {
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

main() {
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

#### `readLines`
```fly
public fly.os.env.StringArray readLines(Reader r)
```
Reads all lines from `r` until EOF and returns them as a `StringArray`.

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

main() {
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

main() {
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
import fly.data.wrapper // fly.data.Wrapper<T>
```

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

Generic single-value holder; requires generics (v0.12+).

```fly
public class Wrapper<T> {
    public Wrapper(T v)
    public T    get()
    public void set(T v)
}
```

**Example:**

```fly
import fly.data.list
import fly.data.map

main() {
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

main() {
    int n = str.len("hello")
    assert.assertEqI(n, 5, 1)        // passes
    assert.assertTrue(n > 0, 2)      // passes
}
```
