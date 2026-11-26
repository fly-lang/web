+++
title = "Fly 0.16"
weight = 0
template = "install-version.html"
path = "install/0.16/"

[extra]
version = "0.16"
ref = "release/0.16.X"
seed = "0.15.2"
date = ""
branch = "release/0.16.X"
+++

Fly 0.16 builds itself in three stages, starting from the pinned seed **Fly 0.15.2**, the latest release:

| Stage | What happens |
|---|---|
| 0 | downloads the pinned seed (Fly 0.15.2) and LLVM into `build/` |
| 1 | the seed runs `fly build` on this tree's `Manifest.fly`, one target at a time (runtime, std, compiler), and links the first `fly`; the suites then run under it |
| 2 | that `fly` runs `fly build --profile dist --out-dir build/stage2`: runtime, std, compiler, tools and the LLVM pieces, then every guard; `build/stage2` is what a release ships |

The seed version is pinned in `ci/linux/stage0.sh` and `ci/windows/stage0.ps1`.
You don't need a system LLVM or `sudo`. Everything LLVM-related comes from the
LLVM archive that stage 0 downloads into `build/llvm` (upstream LLVM 20.1.8, built by
[fly-lang/llvm](https://github.com/fly-lang/llvm)).

#### Linux

Requires `gcc` and `libc6-dev` (for the C start-up objects), `ar` and `objcopy`.

```bash
git clone --branch release/0.16.X https://github.com/fly-lang/fly.git
cd fly
. ./ci/linux/stage0.sh        # sourced: it exports PATH and the library paths
./ci/linux/stage1.sh
./ci/linux/stage2.sh
```

#### macOS (Apple Silicon)

Requires the Xcode Command Line Tools (`xcode-select --install`) for the macOS SDK
that programs link against. The same POSIX scripts as Linux are used; Homebrew is
not needed.

```bash
git clone --branch release/0.16.X https://github.com/fly-lang/fly.git
cd fly
. ./ci/linux/stage0.sh
./ci/linux/stage1.sh
./ci/linux/stage2.sh
```

#### Windows

Requires **PowerShell 7** (`pwsh`). The scripts do not run under Windows PowerShell 5.1.
You don't need Visual Studio to build Fly or to use it, because programs link against
a bundled llvm-mingw / UCRT sysroot.

```powershell
git clone --branch release/0.16.X --config core.autocrlf=false https://github.com/fly-lang/fly.git
cd fly
. .\ci\windows\stage0.ps1
.\ci\windows\stage1.ps1
.\ci\windows\stage2.ps1
```

The result is `build/stage2/bin/fly` (`fly.exe` on Windows).

#### Useful switches

| Environment variable | Effect |
|---|---|
| `FLY_BUNDLE_LLVM=1` | make each stage self-contained: LLVM, the linker and `lldb` next to `fly` (what CI and releases use) |
| `FLY_STAGE1_TESTS=0` | skip the stage 1 suites for a faster local build |
| `FLY_DEBUG_SYMBOLS=1` | DWARF in the stage 1 build |
| `STAGE=1` / `STAGE=2` | select the stage for a single `ci/*/test_*` script (one target alone: `fly build --target <name>`) |

#### Run the tests

```bash
STAGE=2 ./ci/linux/test_runtime.sh     # the runtime layer
STAGE=2 ./ci/linux/test_std.sh         # the standard library
STAGE=2 ./ci/linux/test_compiler.sh    # the compiler and the driver
```

The Windows scripts have the same names under `ci\windows\`, with a `.ps1` extension.
