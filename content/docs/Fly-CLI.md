+++
title = "Command Line"
description = "The fly command: directory-based builds, the three option axes (format, stage, shape), test runs with --suite, projects with Manifest.fly, and the fly-lsp, fly-registry and lldb companion tools."
template = "docs-page.html"
weight = 5
+++

# The `fly` Command Line

`fly` compiles **a directory, not a list of files**. You never pass source
files on the command line: the compiler discovers the entry point from the
source root and pulls everything else in through the import graph.

```sh
fly                      # build the program rooted in the current directory
fly --src-dir src        # build the program rooted in ./src
fly --suite              # build ONE test binary with every suite, and run it
fly --lib                # the whole directory becomes a library
```

An **unknown option is an error** — `fly` never ignores a flag silently.

---

## Source discovery

The source root is the current directory, or `--src-dir <dir>`. What becomes
the entry depends on the build mode:

| Mode | Entry |
|---|---|
| default | the single file declaring a top-level `main()` (two mains = error) |
| `--suite` (test mode) | every file declaring a `suite` (see [Testing](@/docs/Fly-Testing.md)) |
| `--lib` / `--lib-dyn` | every source — the directory *is* the library |

The entry's import closure pulls the remaining sources automatically; std
namespaces resolve against the `.fly.h` headers next to the compiler (and any
`-L` package dir) and link from their archives.

The walk **never descends** into hidden directories (`.git`, `.fly`, …) or
directories named `build` — stray `.fly` files in build outputs cannot break
discovery.

`--src-dir` is **repeatable**. The **first** one is the discovery root; all of
them resolve imports. That is how a program links against a source tree it does
not live in:

```sh
fly --src-dir tools/lsp --src-dir compiler/lib
```

### `--entry`: a second executable from the same tree

`--entry <file>` names the file declaring `main()` outright, so discovery never
runs.

Without it, the command above would fail: `compiler/lib` also declares a
`main()`, and two entry points in the discovery set is an error. With it, only
the named file is the entry; the rest of every source root is still reachable
through its import closure.

```sh
fly --entry tools/lsp/lib/FlyLsp.fly --src-dir tools/lsp --src-dir compiler/lib -o fly-lsp
```

`--entry` is incompatible with `--lib`/`--lib-dyn` and with `--test`/`--suite`,
which select their inputs by their own rule.

---

## The three option axes

Output is controlled by three **independent** axes. Combining them
incompatibly is a diagnosed error — no axis silently rewrites another.

**FORMAT — what artifact a compiled unit produces:**

| Option | Artifact |
|---|---|
| *(default)* | native object, then linked |
| `--emit-ll` | LLVM IR (`.ll`) |
| `--emit-bc` | LLVM bitcode (`.bc`) |
| `--emit-as`, `-S` | target assembly (`.s`) |

**STAGE — how far the pipeline runs:**

| Option | Effect |
|---|---|
| *(default)* | compile and link |
| `-c` | compile only, do not link |
| `--no-output` | parse and analyse only (type-check), produce nothing |

**SHAPE — what the link produces:**

| Option | Product |
|---|---|
| *(default)* | executable |
| `--lib`, `--lib-static` | one merged object + a `.fly.h` per public module (the archive is built on top) |
| `--lib-dyn`, `--lib-dynamic` | dynamic library (`.so`/`.dylib`/`.dll`), implies PIC |
| `--header` | only the `.fly.h` headers, no object |

---

## Output and paths

| Option | Meaning |
|---|---|
| `-o <file>` | output name (default: named after the entry — the suite, or the source-root directory when no single file can name it) |
| `--out-dir <dir>` | directory for the output *and* its intermediate objects |
| `--src-dir <dir>` | source root for discovery and import resolution (default: `.`). **Repeatable** — the first is the discovery root, all of them resolve imports |
| `--entry <file>` | the file declaring `main()`, instead of discovering it |
| `-L <dir>` | package dir: its `.fly.h` headers resolve imports, its `.a`/`.lib` archives are linked |
| `--runtime-lib-dir <dir>` | dir holding `fly_std_lib.a` / `fly_runtime_lib.a` (default: `<exe>/../lib`) |
| `--mingw-sysroot <dir>` | mingw/UCRT sysroot for Windows linking (default: `<exe>/mingw`) |
| `--llvm-lib-dir <dir>` | dir holding `LLVM-20.lib` for programs using the LLVM-C API (default: `<exe>/llvm/lib`) |
| `--link-lib <name>` | link a native library (`-lname`) |
| `--tls` | link the **real** TLS backend (OpenSSL on Linux, Schannel on Windows) instead of the no-op stub |

`--tls` is opt-in for a reason: linking the real backend makes the program
depend on the platform's TLS library, so a build without it still links on a
machine that has none. Without the flag,
[`fly.net.tls.tlsAvailable()`](@/docs/Fly-API.md) returns `false` and every
connect attempt fails cleanly instead of pretending otherwise.

---

## Test options

Covered in depth in [Testing](@/docs/Fly-Testing.md); the short version:

| Option | Meaning |
|---|---|
| `--suite` | build ONE test executable containing **every** discovered suite, run it, and exit with the run's code (0 = all passed, 1 = failures) |
| `--suite=<Sel>` | run a subset. `Sel` is a **suite name** (`MathSuite`), a **qualified name** (`fly.math.test.MathSuite`), or a **namespace** — a namespace runs every suite declared under it, sub-namespaces included (`--suite=fly.compiler.target.test`) |
| `--test` | bare: compile in test mode (`test {}` blocks compiled in) without running anything — for library builds whose blocks a later suite activates |
| `--test=<Method>` | run only that test-method (`Prova` matches `Prova` or `ProvaTest`). A filter matching **zero** methods across the selected suites is a compile error — a typo cannot produce a green run |

A selector that matches no suite or namespace is an error.

---

## Codegen and target

| Option | Meaning |
|---|---|
| `-O<n>` | optimisation level 0–3 |
| `--target <triple>` | target triple (default: the host; on Windows `x86_64-w64-windows-gnu` — Fly links UCRT-only through its bundled toolchain, no MSVC) |
| `--target-cpu <cpu>` | target CPU (default: generic) |
| `-j`, `--jobs <n>` | LLVM parallelism (0 = auto) |
| `--mcmodel <m>` | memory code model |
| `--mthread-model <m>` | `posix` (default) or `single` — validated, not yet applied by the backend |
| `-static` / `-pie` | link-mode flags (ELF/Linux targets; ignored by the Windows linker) |
| `--debug-symbols` | request DWARF debug symbols (the self-host codegen does not emit them yet) |
| `--trace-calls` | instrument every function entry/exit with [`fly.log`](@/docs/Fly-API.md) trace hooks (see below) |

### `--trace-calls`: function-call tracing

`--trace-calls` makes the compiler inject a call to `fly.log.enter` in every
function's entry block and a call to `fly.log.exit` — or `fly.log.exitFail`,
when the function is left through an unhandled `fail` — before every return.
It is Fly's `-finstrument-functions`: no source change, no import (the std
archive is linked into every program anyway).

What gets instrumented: every function, method and constructor **of your
code**. The `fly.*` namespaces (std, runtime, the compiler) are excluded, both
to keep the trace readable and because the hooks themselves live in `fly.log`.
A `fail` caught by an enclosing `handle` branches to the handle block — that is
not a function exit, and correctly logs nothing.

The instrumented binary is **silent by default**: the hooks print at `TRACE`,
and the logger's threshold defaults to `INFO`. Tracing is switched on at *run*
time:

```sh
fly --trace-calls -o app
FLY_LOG_LEVEL=trace ./app
```

```text
2026-07-31 15:00:27 [TRACE] > main
2026-07-31 15:00:27 [TRACE]   > fib
2026-07-31 15:00:27 [TRACE]     > fib
2026-07-31 15:00:27 [TRACE]     < fib
2026-07-31 15:00:27 [TRACE]   < fib
2026-07-31 15:00:27 [TRACE]   > risky
2026-07-31 15:00:27 [TRACE]   <! risky
2026-07-31 15:00:27 [TRACE] < main
```

Indentation follows call depth; `<!` marks the failing exit. The hook call
carries its own zeroed error slot, so tracing never disturbs a failure being
propagated — exit codes and `error <code>: <message>` output are identical
with and without the flag.

---

## Diagnostics and introspection

| Option | Meaning |
|---|---|
| `-v`, `--verbose` | log each compilation phase to stderr |
| `--debug` | verbose logging (also requests DWARF) |
| `--print-stats` | compilation statistics to stderr (files parsed, deps pulled, headers loaded, dirs scanned) |
| `--stats-file <f>` | write the statistics as JSON to `<f>` |
| `--log-file <f>` | log every diagnostic to `<f>` |
| `--log-format <fmt>` | diagnostics log format: `txt` (default) or `json` |
| `--ftime-report` | accepted; not yet implemented |
| `-w`, `--no-warning` | accepted; the compiler currently emits no warnings to suppress |
| `--version` | print the version |
| `--help` | full usage |

---

## Exit codes

| Situation | Exit |
|---|---|
| build succeeded | 0 |
| compile/link error | 1 |
| `--suite` run | the **run's** code: 0 = every case passed, 1 = any failure |
| running a built program | `main()`'s error protocol: 0 clean, otherwise the code of the last unhandled failure (printed as `error <code>: <message>`) |

---

## Projects — `Manifest.fly`

Everything above is the **unitary** mode: one directory, one artifact, no
configuration. A project adds a `Manifest.fly` in the working directory (a
lowercase `manifest.fly` is also accepted) and a set of subcommands that manage
multiple targets, dependencies, a lockfile and an incremental cache.

**There is no TOML.** The manifest *is* Fly source — a class extending
`fly.meta.Manifest`, with literal field initialisers — and the package manager
reads it with **the compiler's own parser**, walking the AST. One language, one
parser, and a manifest your editor already understands. The read is parse-only:
no semantic analysis, no codegen.

### `fly init`

```sh
fly init --name hello --version 0.1.0
```

writes `src/main.fly` and this `Manifest.fly`:

```fly
namespace pkg.manifest
import fly.meta.*

class Manifest : fly.meta.Manifest {
    string name = "hello"
    string version = "0.1.0"
    string flyVersion = "0.14.4"
    Target[] targets = { new Target("hello", "src/main.fly", "") }
}
```

Both flags are optional (`my-project` and `0.1.0` by default). `fly init`
refuses to overwrite an existing manifest.

### Manifest schema

The fields are those of `fly.meta.Manifest`; every one is optional except
`name` and `version`. Object collections are array literals of `new T(...)`
with **positional** arguments, and trailing arguments may be omitted.

| Field | Type | Meaning |
|---|---|---|
| `name` | `string` | package name (`[a-z0-9_-]`) |
| `version` | `string` | `MAJOR.MINOR.PATCH` |
| `description`, `license`, `homepage`, `repository` | `string` | metadata |
| `flyVersion` | `string` | the toolchain version the project expects |
| `registry` | `string` | default registry alias |
| `authors` | `string[]` | |
| `linkLibs` | `string[]` | native libraries to link (`-lname`) |
| `targets` | `Target[]` | `new Target(name, path, lib)` — `lib` is `""` for an executable, else `"static"`, `"dynamic"` or `"both"` |
| `dependencies`, `devDependencies` | `Dependency[]` | see below |

> The `fly.meta` schema also declares `profiles`, `repositories`, `test`,
> `workspace` and `hooks`. The current reader **does not consume them yet**:
> declaring them is accepted but has no effect. In particular `--release` uses
> the built-in profile — `-O3`, no debug info, assertions off — and the default
> profile is `-O0` with debug info and assertions on.

A `Dependency` is `new Dependency(name, version, registry, git, tag, branch,
rev, path)` and the fields that matter depend on the kind:

```fly
Dependency[] dependencies = {
    new Dependency("json", "^1.2.0"),                                   // registry
    new Dependency("util", "", "", "https://github.com/acme/util", "v1.0"),  // git tag
    new Dependency("local", "", "", "", "", "", "", "../local")         // path
}
```

### Commands

Run from the directory holding the manifest.

| Command | Meaning |
|---|---|
| `fly init [--name N] [--version V]` | scaffold `Manifest.fly` + `src/main.fly` |
| `fly build [--target N]` | build every target, or one |
| `fly run [--bin N]` | build, then run the (named) executable target |
| `fly test` | build and run the project's suites (the `*Suite.fly` files) |
| `fly clean` | remove the profile's output directory |
| `fly lock` | resolve dependencies and write `Lock.fly` |
| `fly add <name> [--git URL] [--tag T \| --branch B \| --rev R] [--dev]` | add a dependency and re-lock |
| `fly remove <name>` | drop a dependency and re-lock |
| `fly why <name>` | explain why a package is in the graph |
| `fly version` | the toolchain version |

Shared flags: `--release` (or `--profile <name>`) selects the profile, and
`--force` bypasses the incremental cache.

> The table is the complete set. `update`, `upgrade`, `vendor`, `search`,
> `publish`, `workspace`, `deploy`, `cache` and `doctor` are **not implemented**
> in the self-hosted driver: `fly` rejects them as an unknown argument and exits
> 1, rather than pretending to do something.

### The lockfile

`fly lock` — and any command that changes the dependency set — resolves the
graph with **minimal version selection** and writes `Lock.fly`. Like the
manifest it is Fly source (a class extending `fly.meta.Lockfile`), read back
with the same parser. It is machine-generated: commit it, do not edit it.

Each locked package records its name, version, source (`git+<url>` or
`registry+<name>`), the resolved `rev`/`tag`, a checksum and its own
dependencies. The lockfile also stores a checksum **of the manifest**, so a
manifest edit invalidates it.

### Build layout and incremental builds

Output goes to `target/<profile>` (plus `/<triple>` when cross-compiling), and
`target/test` for test binaries.

Each target gets a **fingerprint** under `<outdir>/.fly/<key>.fp`: a hash of
its sources, its flags, the lockfile and the compiler's own identity — content
hashes only, never mtimes. A target whose fingerprint is unchanged is skipped;
`--force` rebuilds regardless. Because debug and release have separate output
directories, their fingerprints cannot collide.

### The package cache

Git and registry dependencies are cached under `~/.fly/cache`, or wherever
`FLYLANG_CACHE` points. Cache entries are keyed by host, owner, repository and
the resolved 40-character revision, so two projects pinning the same commit
share one copy.

Downloaded archives are unpacked **in-process** with `fly.targz`, and their
gzip CRC-32 and length are verified before anything is written — a truncated
or corrupt download is rejected rather than stored. No `tar` or `curl` process
is involved anywhere.

---

## Companion binaries

The toolchain ships three more executables next to `fly`, built or bundled by
the same pipeline and covered by the same test suites: `fly-lsp` and
`fly-registry` are compiled by the self-hosted compiler; `lldb` (with
`lldb-dap`) comes from the project's own LLVM build.

### `fly-lsp` — the language server

Speaks the Language Server Protocol over **stdio**; it takes no options. Point
your editor's Fly client at the binary:

```sh
<install>/bin/fly-lsp
```

Diagnostics come from the real compiler pipeline — the same parser and semantic
analysis `fly` itself runs — so what the editor underlines is what the build
will report.

| Capability | Method |
|---|---|
| Diagnostics | `textDocument/publishDiagnostics` (on open and change) |
| Go to definition | `textDocument/definition` |
| Go to type definition | `textDocument/typeDefinition` |
| Go to implementation | `textDocument/implementation` |
| Hover | `textDocument/hover` |
| References | `textDocument/references` |
| Highlight | `textDocument/documentHighlight` |
| Document outline | `textDocument/documentSymbol` |
| Folding | `textDocument/foldingRange` |
| Completion | `textDocument/completion` |
| Signature help | `textDocument/signatureHelp` |
| Semantic tokens | `textDocument/semanticTokens/full` |
| Inlay hints | `textDocument/inlayHint` |
| Workspace symbols | `workspace/symbol` |

Method calls resolve through the receiver's static type, so `p.getX()`, `p.x`,
`this.x` and static access all navigate — including members inherited from a
base class.

**Current limits**, stated rather than hidden: the server compiles from **disk**,
so unsaved edits are analysed only after a save; a receiver that is itself a
call (`f().m()`) is not typed; an overloaded name resolves to the first
declaration; references, highlights and implementations cover the **open file**;
and for functions, methods and fields the reported column points at the type
rather than the name (the line is exact — locals give the exact column).

### `fly-registry` — the package registry server

Serves the endpoints the package manager speaks, so a team can host its own
index without any third-party service.

```sh
fly-registry --storage ./packages --port 5000 --token secret
```

| Option | Default | Meaning |
|---|---|---|
| `--storage <dir>` | `$FLY_HOME/registry`, else `~/.fly/registry` | package storage root |
| `--port <n>`, `-p <n>` | `5000` | listening port (`0` = pick a free one and print it) |
| `--host <addr>` | `0.0.0.0` | bind address |
| `--token <t>` | `$FLY_REGISTRY_TOKEN` | bearer token required to publish; unset = publishing is open |
| `--once` | off | serve exactly one request and exit (for scripts and tests) |

| Endpoint | Meaning |
|---|---|
| `GET /v1/{name}` | JSON array of published versions |
| `GET /v1/{name}/{version}` | the package's `Manifest.fly` |
| `GET /v1/{name}/{version}/download` | the source tarball (`.tar.gz`) |
| `POST /v1/{name}/{version}` | publish a tarball (bearer token when one is set) |
| `GET /v1/search?q={query}` | JSON array of matching package names |

Publishing **verifies** the upload: the gzip CRC-32 and length are checked and
the `Manifest.fly` is extracted in-process before anything is stored, so a
truncated upload is rejected with `400` instead of being served to every client
afterwards. Package and version names that would escape the storage directory
(absolute, drive-qualified, or containing `..`) are rejected with `400`.

Storage layout is one directory per package:
`<storage>/<name>/<version>.tar.gz` alongside
`<storage>/<name>/<version>/Manifest.fly`.

### `lldb` — the debugger

The LLDB driver from the project's own LLVM build, shipped under its original
name next to `fly` — fully **self-contained**: no Python, no libedit, no
curses, no system LLDB. On Windows `liblldb.dll` sits beside it; on Linux
`liblldb` lives in `lib/` and `lldb-server` (which lldb launches local
processes through) in `bin/`.

```sh
<install>/bin/lldb ./myprog
```

Batch mode drives the built-in command interpreter — scripting is via `-o`
commands (Python scripting is intentionally off):

```sh
lldb -b -o 'breakpoint set -f main.fly -l 3' -o run -o bt -o continue -- ./myprog
```

**Current limits**, stated rather than hidden: the self-hosted compiler does
not emit DWARF yet — `--debug-symbols` is accepted but produces no debug info.
Today `lldb` gives **source-level** debugging (breakpoints by `file.fly:line`,
stepping, variable inspection) for binaries compiled by the reference compiler
with `--debug-symbols`, and for the toolchain's own debug CI builds; a
self-host-compiled program debugs at the symbol/assembly level until DWARF
emission lands. Debug info is tagged as C, so types and mangled method names
read C-flavoured.

#### `lldb-dap` — IDE debugging

`lldb-dap` (same engine, Debug Adapter Protocol over stdio) ships alongside for
editor integration. Any DAP-capable editor can point at it; in VS Code (with
the *LLDB DAP* extension):

```json
{
  "type": "lldb-dap",
  "request": "launch",
  "name": "Debug (fly)",
  "program": "${workspaceFolder}/out/myprog",
  "debugAdapterExecutable": "<install>/bin/lldb-dap"
}
```
