+++
title = "Install"
template = "install.html"
sort_by = "weight"
+++

## Installation Notes

> Fly is in **pre-release**: expect breaking changes between versions.

### What's in the archive

Releases 0.14.x and later are **self-contained**. The archive has `bin/` and `lib/` at its root, with no
enclosing folder, so unpack it into a directory of its own.

| Folder | Contents |
|---|---|
| `bin/` | `fly`, `fly-lsp`, `fly-registry`, the linker (`ld.lld`) and `lldb` |
| `lib/` | the runtime, the standard library and its headers |

The commands below install Fly in `~/fly` (`%USERPROFILE%\fly` on Windows). For another version, change `VERSION`.

<div class="install-os" data-os="linux">

### Linux (x86_64)

```sh
VERSION={{ config.extra.docs.latest }}
mkdir -p ~/fly
tar -xzf fly-$VERSION-linux-x86_64.tar.gz -C ~/fly
echo 'export PATH="$HOME/fly/bin:$PATH"' >> ~/.profile
export PATH="$HOME/fly/bin:$PATH"
fly --version
```

</div>
<div class="install-os" data-os="macos">

### macOS (Apple Silicon)

```sh
VERSION={{ config.extra.docs.latest }}
mkdir -p ~/fly
tar -xzf fly-$VERSION-macos-arm64.tar.gz -C ~/fly
echo 'export PATH="$HOME/fly/bin:$PATH"' >> ~/.zprofile
export PATH="$HOME/fly/bin:$PATH"
fly --version
```

</div>
<div class="install-os" data-os="windows">

### Windows (x64, PowerShell)

No Visual Studio is needed to use Fly.

```powershell
$VERSION = "{{ config.extra.docs.latest }}"
Expand-Archive "fly-$VERSION-win-x64.zip" -DestinationPath "$HOME\fly"
$userPath = [Environment]::GetEnvironmentVariable("Path", "User")
[Environment]::SetEnvironmentVariable("Path", "$HOME\fly\bin;$userPath", "User")
$env:Path = "$HOME\fly\bin;$env:Path"
fly --version
```

</div>

### Verify the download

Every release publishes a `SHA256SUMS` file next to the archives. Download it into the same folder as the
archive, then:

```sh
sha256sum -c SHA256SUMS --ignore-missing          # Linux
shasum -a 256 -c SHA256SUMS --ignore-missing      # macOS
```

```powershell
# Windows: compare with the line in SHA256SUMS
(Get-FileHash "fly-$VERSION-win-x64.zip").Hash.ToLower()
```
