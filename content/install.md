+++
title = "Install"
template = "install.html"
+++

## Notes

- **Linux / macOS**: after downloading, make the binary executable with `chmod +x fly` and move it to a directory in your `PATH` (e.g. `/usr/local/bin`).
- **Windows**: unzip the archive and add the folder to your `PATH` environment variable.
- Fly is currently in **pre-release**. Expect breaking changes between versions.

## Build from Source

Requires CMake 3.24+, LLVM 20.1.8, and a C++17 compiler.

### Linux

**1. Clone the repository**

```bash
git clone https://github.com/fly-lang/fly.git
```

**2. Install build dependencies**

Debian / Ubuntu:
```bash
sudo apt install build-essential libxml2-dev zlib1g-dev libtinfo-dev
# stacktrace support (debug purpose)
sudo apt install binutils-dev libdw-dev libdwarf-dev
```

Fedora / RHEL / CentOS Stream:
```bash
sudo dnf install gcc gcc-c++ make libxml2-devel zlib-devel ncurses-devel
# stacktrace support (debug purpose)
sudo dnf install binutils-devel elfutils-devel libdwarf-devel
```

openSUSE:
```bash
sudo zypper install gcc gcc-c++ make libxml2-devel zlib-devel ncurses-devel
# stacktrace support (debug purpose)
sudo zypper install binutils-devel libdw-devel libdwarf-devel
```

Arch Linux:
```bash
sudo pacman -S base-devel libxml2 zlib ncurses
# stacktrace support (debug purpose)
sudo pacman -S libdwarf elfutils
```

**3. Configure and build**

```bash
cd fly
mkdir build && cd build
cmake ..
cmake --build .
```

**4. Run tests (must be run from the build directory)**

```bash
ctest
```

---

### macOS

**1. Clone the repository**

```bash
git clone https://github.com/fly-lang/fly.git
```

**2. Install Xcode Command Line Tools**

```bash
xcode-select --install
```

**3. Install build dependencies via [Homebrew](https://brew.sh)**

```bash
brew install cmake libxml2 zlib
```

**4. Configure and build**

```bash
cd fly
mkdir build && cd build
cmake ..
cmake --build .
```

**5. Run tests**

```bash
ctest
```

---

### Windows

**1. Clone the repository**

```bash
git clone --config core.autocrlf=false https://github.com/fly-lang/fly.git
```

**2. Install dependencies via vcpkg**

```bash
vcpkg install zstd:x64-windows
```

**3. Configure and build**

```bash
cd fly
mkdir build && cd build
cmake .. -DCMAKE_TOOLCHAIN_FILE="%VCPKG_INSTALLATION_ROOT%\scripts\buildsystems\vcpkg.cmake"
cmake --build . --config Release
```

**4. Run tests**

```bash
ctest -C Release
```
