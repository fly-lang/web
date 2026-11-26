+++
title = "Building Fly"
description = "How Fly builds itself: the chain of pinned seeds (C++ up to 0.14.x, self-host from 0.15), the three bootstrap stages, what each shipped artifact is compiled by, the guards that run at every stage, and the known limits of the current bootstrap."
template = "docs-page.html"
weight = 7
+++

# Building Fly from source

Fly is **self-hosted**: the compiler, the runtime and the standard library are
written in Fly. Building it therefore needs a Fly compiler to start from. That
starting compiler is the **seed**, and the process that goes from the seed to
the released toolchain is the **staged bootstrap**.

This page describes the bootstrap as it is implemented in `ci/linux/*.sh` and
`ci/windows/*.ps1` of the `release/0.14.X` branch. The scripts are the source of
truth; the two sets mirror each other step for step. The POSIX scripts serve
both Linux x86_64 and macOS arm64 (from 0.14.9): what differs between the two
systems is set once in `ci/linux/platform.sh`.

---

## The chain of seeds

Each release series is built by one pinned seed, and each series has its own
branch, so the whole chain can be rebuilt from sources in order:

| Series | Branch | Seed | Seed written in |
|---|---|---|---|
| 0.14.x | `release/0.14.X` | 0.13.19, the C++ reference (`release/0.13.X`) | C++ |
| 0.15.x | `release/0.15.X` | **0.14.9**, the last 0.14.x release | Fly |

From 0.15 the seed is the previous **self-host** release: the C++ compiler is
frozen at 0.13.19 and only starts the chain from scratch (0.13.19 builds 0.14.x,
whose 0.14.9 builds 0.15.x). The rest of this section describes the C++ seed of
the 0.14.x series.

The C++ seed is deliberately small. It has no standard library, no package manager
and no test runner: it exists to turn the 0.14.x sources into a first working
`fly`. The rule that follows from this is the **bootstrap subset**:

> The sources under `compiler/`, `runtime/` and `std/lib` may only use syntax
> the pinned seed can parse, because the seed compiles all of them at stage 1.
> From 0.15 the limit is what the previous self-host release compiles.

Language features that go beyond the seed are available to user programs and to
the tools (which are built at stage 2), not to the compiler tree itself.

### The seed pin

The seed version is pinned in `ci/linux/stage0.sh` and `ci/windows/stage0.ps1`
(`FLY_VERSION`: **0.13.19** on `release/0.14.X`, **0.14.9** on `release/0.15.X`),
with the SHA-256 of each platform's archive. A C++ seed was re-cut only when the
tree needed something from it. The history of the C++ pins documents why each
one was needed:

| Seed | What it brought to the tree |
|---|---|
| 0.13.13 | array subscript `a[i]`, `T[N]` declarations, reference-counted array buffers |
| 0.13.14 | first seed with **no std**: `bin/fly` + two headers + the C runtime archive |
| 0.13.15 | no CLI test runner; a lone `suite` is the test entry |
| 0.13.16 | a generated `.fly.h` is a declaration file, with no `{}` stubs |
| 0.13.17 | `.fly.h` is the only textual file in a lib directory (declarations + generic templates) |
| 0.13.18 | `return expr` replaces the implicit `out` variable; every path must return; NRVO |
| 0.13.19 | macOS arm64 (the language of 0.13.18) — the last C++ seed |
| 0.14.9 | first self-host seed (`release/0.15.X`): stage 1 runs the same commands as stage 2 |

`stage0` checks the installed seed with `--version` and replaces it when it does
not match the pin; the swap is atomic, so a failed download never leaves a
machine without a seed.

---

## The stages

```text
stage 0   download          seed fly (0.14.9 on 0.15.x)  +  LLVM 20.1.8
              │
stage 1   seed compiles     runtime → std → compiler+driver  ──link──▶  build/stage1/bin/fly
              │             then stage1 fly rebuilds the std
              │             then: test_runtime, test_std, test_compiler   (run by stage1 fly)
              │
stage 2   stage1 fly        runtime, std, compiler+driver     ──link──▶  build/stage2/bin/fly
          compiles          tools: fly-lsp, fly-registry
                            then: every suite + ABI, semantic, leak, debugger guards
              │
stage 3   stage2 fly        compiler+driver object, compared byte for byte with stage 2's
```

### Stage 0 — provisioning

Downloads two things into `build/`, and needs no system package manager:

- **The fork LLVM** (`fly-lang/llvm-project`, 20.1.8) → `build/llvm`: the LLVM
  shared library, `ld.lld`, `llvm-config`, the compiler-rt builtins that every
  Fly executable links, and the `lldb` debugger set. On Windows it also
  provides the llvm-mingw UCRT sysroot, so no Visual Studio is required.
- **The pinned seed** → `build/stage0`.

### Stage 1 — the seed builds the self-host

`STAGE=1`. The seed compiles, in order:

1. **runtime** — `runtime/lib/Runtime<Platform>.fly` into `fly_runtime_lib`,
   plus the TLS stub and the real TLS backend as separate archives.
2. **std** — the whole `std/lib` directory with `--lib`: one archive and one
   `.fly.h` header per public module.
3. **compiler** — `--src-dir compiler` discovers `Driver.fly` (the only
   `main()`) and pulls the entire compiler through its imports into **one**
   object. The build is monolithic on purpose: as a separate static archive, the
   compiler's generic instantiations were de-duplicated by the linker against
   the driver's, which corrupted memory.
4. **link** — `ld.lld` links the object with std, runtime and LLVM into
   `build/stage1/bin/fly`.
5. **std again** — the `fly` just linked rebuilds the std in `build/stage1/lib`
   (`FLY_STD_BUILDER=self`). The seed's build only served to link that compiler.

Only then do the suites run, and they are compiled and executed by the `fly`
that stage 1 just linked — never by the seed. `FLY_STAGE1_TESTS=0` skips them
for a fast local build.

### Stage 2 — the self-host builds what ships

`STAGE=2`. `build/stage1/bin/fly` recompiles the runtime, the std and the
compiler, links `build/stage2/bin/fly`, and then builds the tools with that
compiler. `build/stage2/{bin,lib}` is exactly what a release packages.

### Stage 3 — the fixpoint

`build/stage2/bin/fly` compiles the compiler once more, and the object must be
byte-identical to the one stage 2 produced. If it differs, the compiler's
output depends on which compiler built it: a defect no suite can see, because a
suite runs a single compiler. CI runs it on every build.

---

## What compiles what

The provenance of every shipped artifact, as of the next release (0.14.8):

| Artifact | Compiled by | Notes |
|---|---|---|
| `fly` (compiler + driver) | stage 1 `fly` | a self-host compiled by a self-host that the seed compiled |
| `fly-lsp`, `fly-registry` | stage 2 `fly` | the compiler that ships |
| standard library | stage 1 `fly` | built at stage 2 like the compiler; was the seed's build up to 0.14.7 |
| runtime | stage 1 `fly` | all Fly on every platform; on Linux it also carried C objects from the seed up to 0.14.7 |
| `lldb`, `ld.lld`, LLVM | fork LLVM release | provisioned, not compiled |

The seed still builds a standard library at stage 1, but only to link the
stage 1 compiler, which it also compiles. As soon as that compiler exists it
rebuilds the standard library in `build/stage1/lib`, and everything after it
— the stage 1 suites and all of stage 2 — uses a library laid out the way the
self-hosted compiler lays out classes.

Up to 0.14.7 the shipped standard library was the seed's build. The two
compilers disagreed on class layout, and the self-hosted compiler resolved an
interface call through a vtable slot computed for its whole module — so a
program and a library compiled separately would pick different slots. Every
class now carries one table of methods per interface it implements, found by
the interface's identity, which both sides of any boundary agree on.

Nothing that ships is compiled by the seed any more. The last piece was the
Linux runtime: up to 0.14.7 its archive kept the C primitives of the seed's
runtime (sources only in the 0.13.x repository) next to the Fly runtime, made
weak so the C definitions won. The Fly runtime defines every one of those
primitives, and the self-hosted compiler lowers the runtime's atomic operations
to LLVM atomic instructions instead of calls into a libatomic, so the stage 2
archive is the Fly runtime alone. Stage 1 still links the seed's C objects:
the seed compiles that runtime and cannot lower the atomics itself.

---

## Guards

Some defects are invisible to ordinary suites, which compile valid programs
with a single compiler. Stage 2 therefore ends with dedicated guards, and
stage 3 follows it:

| Guard | Script | What it catches |
|---|---|---|
| Fixpoint | `stage3` | the compiler generating different code depending on which compiler built it. |
| ABI | `test_abi` | a calling-convention mismatch between seed and self-host. Each probe has a library compiled by the seed and a program compiled by the self-host. |
| Leaks | `test_leaks` | an ownership defect in codegen. Each probe loops millions of times over one pattern and fails above a 20 MB peak. |
| Semantics | `test_sema` | a compile-time check that went missing. Each probe under `ci/sema_probes` is a program that must be rejected with a specific diagnostic, or an edge case that must keep compiling. |
| Tools | `test_lsp`, `test_registry`, `test_tools` | the shipped companion binaries. |
| Debugger | `test_dbg` | the bundled `lldb` starts and drives a program. |

Test order is always bottom-up — runtime, std, compiler — so a failure is
reported at the lowest broken layer.

---

## Building locally

**Linux**

```sh
. ./ci/linux/stage0.sh      # sourced: exports PATH / LIBRARY_PATH
./ci/linux/stage1.sh
./ci/linux/stage2.sh
```

Requires `libc6-dev` and `gcc` for the host C runtime start-up objects, plus
`ar` and `objcopy`.

**Windows** — PowerShell 7 (`pwsh`), not Windows PowerShell 5.1:

```powershell
. .\ci\windows\stage0.ps1
.\ci\windows\stage1.ps1
.\ci\windows\stage2.ps1
```

Useful switches, as environment variables:

| Variable | Effect |
|---|---|
| `FLY_VERSION` | override the seed pin |
| `FLY_BUNDLE_LLVM=1` | self-contained stage: LLVM, `ld.lld` and `lldb` copied next to `fly` (what CI and releases use) |
| `FLY_STAGE1_TESTS=0` | skip the stage 1 suites |
| `FLY_DEBUG_SYMBOLS=1` | DWARF in every stage: the seed emits it at stage 1, the self-host from stage 2 |
| `STAGE=1\|2` | select the stage for an individual `build_*` / `test_*` script |
| `FLY=<path>` | test another compiler binary with a `test_*` script |

Each `build_*` and `test_*` script can be run on its own with `STAGE` set.

### Layout

```text
build/
  llvm/            fork LLVM (stage 0)
  stage0/          the seed: bin/fly + lib/
  stage1/          bin/fly0 (the seed, hard-linked), bin/fly, lib/, driver/
  stage2/          bin/, lib/   ← the release
  test/            suite binaries and probe output
```

A `fly` binary finds its libraries at `<exe>/../lib`. That is why stage 1 hard
links the seed as `build/stage1/bin/fly0`: the seed then picks up
`build/stage1/lib` rather than its own.

---

## Releases

`release.yml` runs on a push to `main`. It calls the Linux and Windows build
workflows without debug symbols, takes the version from the root
`Manifest.fly` — the single source of truth since 0.15.2 (before, it was
`compiler/lib/basic/Version.fly`; the compiler now reads it through the
generated `fly.meta.package` module) — and publishes
`fly-<version>-linux-x86_64.tar.gz` and `fly-<version>-win-x64.zip`.

---

## Known limits

These are properties of the bootstrap today, tracked in the
[roadmap](@/docs/0.15.2/Fly-Roadmap.md):

- **Platforms.** CI builds Linux x86_64, Windows x64 and, from 0.14.9, macOS
  arm64 (Apple Silicon), each through stage 3. Linux aarch64 (ARM 64-bit Linux)
  is a later platform. On
  macOS TLS has no backend yet (`tlsAvailable()` is `false`).
- **Debug information without class fields.** From 0.14.8 the self-host emits
  DWARF with `--debug-symbols`: functions, files, source lines, parameters and
  locals, enough for breakpoints, backtraces, stepping and `frame variable`. The
  fields of a class are not described yet.
