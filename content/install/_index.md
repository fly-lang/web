+++
title = "Install"
template = "install.html"
sort_by = "weight"
+++

## Notes

- A self-hosted release archive (0.14.x and later) is **self-contained**: `bin/` holds `fly`, `fly-lsp`,
  `fly-registry`, the linker and `lldb`; `lib/` holds the runtime, the standard
  library and its headers. Unpack it anywhere and put `bin/` on your `PATH`.
- **Linux / macOS**: `tar -xzf fly-<version>-<platform>.tar.gz`, then add the
  extracted `bin/` directory to your `PATH`.
- **Windows**: unzip the archive and add its `bin\` folder to your `PATH`. No
  Visual Studio is needed to use Fly.
- Every release publishes a `SHA256SUMS` file next to the archives.
- Fly is currently in **pre-release**. Expect breaking changes between versions.
