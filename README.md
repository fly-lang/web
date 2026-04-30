<p align="center">
  <a href="https://flylang.org">
    <img src="https://github.com/fly-lang/graphics/blob/main/logo/fly_logo_300.png?raw=true" alt="Fly Logo" width="180">
  </a>
</p>

<h3 align="center">flylang.org</h3>

<p align="center">
  Source code of the official <a href="https://flylang.org">Fly Programming Language</a> website.
  <br><br>
  <a href="https://flylang.org">flylang.org</a>
  ·
  <a href="https://github.com/fly-lang/web/issues">Report a Bug</a>
  ·
  <a href="https://github.com/fly-lang/web/discussions">Discussions</a>
</p>

<p align="center">
  <a href="https://github.com/fly-lang/web/actions/workflows/deploy.yml">
    <img src="https://github.com/fly-lang/web/actions/workflows/deploy.yml/badge.svg" alt="Deploy">
  </a>
  <a href="https://flylang.org">
    <img src="https://img.shields.io/website?url=https%3A%2F%2Fflylang.org&label=flylang.org" alt="Website">
  </a>
  <a href="https://github.com/fly-lang/web/blob/main/LICENSE.txt">
    <img src="https://img.shields.io/badge/license-Apache--2.0-blue.svg" alt="License">
  </a>
</p>

---

## About

This repository contains the source of [flylang.org](https://flylang.org), built with
[Zola](https://www.getzola.org/) (a static site generator) and deployed to
[Cloudflare Pages](https://pages.cloudflare.com/) on every push to `main`.

## Tech Stack

| Layer | Tool |
|-------|------|
| Static site generator | [Zola](https://www.getzola.org/) 0.19+ |
| Styles | SCSS (compiled by Zola) |
| Hosting | [Cloudflare Pages](https://pages.cloudflare.com/) |
| CI/CD | GitHub Actions |

## Getting Started

### Install Zola

**macOS (Homebrew)**
```bash
brew install zola
```

**Linux (snap)**
```bash
snap install zola --edge
```

**Linux (manual)**
```bash
ZOLA_VERSION="0.19.2"
curl -sL "https://github.com/getzola/zola/releases/download/v${ZOLA_VERSION}/zola-v${ZOLA_VERSION}-x86_64-unknown-linux-gnu.tar.gz" | tar xz
sudo mv zola /usr/local/bin/
```

**Windows (winget)**
```bash
winget install getzola.zola
```

Verify the installation:
```bash
zola --version
```

### Local development

```bash
git clone https://github.com/fly-lang/web.git
cd web
zola serve
```

The site is available at `http://127.0.0.1:1111` with live reload on every change.

### Build

```bash
zola build
```

Output is written to the `public/` directory.

## Project Structure

```
web/
├── config.toml          # Zola configuration
├── content/             # Markdown pages
│   ├── _index.md        # Home
│   ├── install.md       # Install page
│   ├── documentation.md
│   ├── community.md
│   ├── code-of-conduct.md
│   ├── privacy-policy.md
│   └── licenses.md
├── templates/           # Tera HTML templates
│   ├── base.html        # Shared layout (nav + footer)
│   ├── index.html       # Home page
│   ├── page.html        # Generic page
│   └── install.html     # Install page (dynamic releases)
├── sass/
│   └── main.scss        # All styles
├── static/              # Copied as-is to public/
│   ├── img/
│   ├── fonts/
│   └── js/
│       └── install.js   # Fetches latest release from GitHub API
└── .github/
    └── workflows/
        └── deploy.yml   # Build Zola → deploy su Cloudflare Pages
```

## Deployment

Deployments sono automatici tramite GitHub Actions:

- **Push su `main`** → build + deploy in produzione su [flylang.org](https://flylang.org)
- **Pull request** → build + preview deploy con URL univoco

### Secret richiesti su GitHub

| Secret | Dove trovarlo |
|--------|---------------|
| `CLOUDFLARE_API_TOKEN` | Cloudflare → My Profile → API Tokens |
| `CLOUDFLARE_ACCOUNT_ID` | Cloudflare → sidebar destra della home |

## Contact

| | |
|-|-|
| Website | [flylang.org](https://flylang.org) |
| Email | [dev@flylang.org](mailto:dev@flylang.org) |
| Twitter / X | [@fly_lang](https://twitter.com/fly_lang) |
| Matrix | [#flylang:matrix.org](https://matrix.to/#/#flylang:matrix.org) |
| GitHub | [fly-lang](https://github.com/fly-lang) |

## License

Code — [Apache License 2.0](LICENSE.txt)  
Artwork — [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/)
