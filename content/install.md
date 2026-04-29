+++
title = "Install"
template = "install.html"
+++

## Notes

- **Linux / macOS**: after downloading, make the binary executable with `chmod +x fly` and move it to a directory in your `PATH` (e.g. `/usr/local/bin`).
- **Windows**: unzip the archive and add the folder to your `PATH` environment variable.
- Fly is currently in **pre-release**. Expect breaking changes between versions.

## Build from Source

```bash
git clone https://github.com/fly-lang/fly.git
cd fly
cmake -B build -DCMAKE_BUILD_TYPE=Release
cmake --build build
```

Requires CMake 3.20+, LLVM 20, and a C++17 compiler.
