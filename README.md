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
| Static site generator | [Zola](https://www.getzola.org/) 0.23.6 (Tera 2 templates) |
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
ZOLA_VERSION="0.23.6"
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
├── config.toml               # Zola configuration; [extra.docs] lists the doc versions
├── content/                  # Markdown pages
│   ├── _index.md             # Home
│   ├── docs/                 # Documentation (the canonical language spec)
│   │   ├── _index.md         # /docs/ = index of the latest release
│   │   ├── Fly-*.md          # Draft docs of the version in development (NOT published)
│   │   └── <version>/        # Published docs of each release (0.15.2, 0.15.0, …)
│   ├── install/              # Install page
│   │   ├── _index.md         # Notes shown on the page
│   │   └── <version>.md      # Build-from-source instructions of each release
│   ├── community.md
│   ├── code-of-conduct.md
│   ├── privacy-policy.md
│   └── licenses.md
├── templates/                # Tera 2 HTML templates
│   ├── base.html             # Shared layout (nav + footer)
│   ├── index.html            # Home page
│   ├── page.html             # Generic page
│   ├── docs.html             # Docs index of a version
│   ├── docs-page.html        # A docs page (sidebar + version picker)
│   ├── docs-components.html  # Version picker and banners
│   ├── install.html          # Install page (downloads + build instructions)
│   └── install-version.html  # /install/<version>/ → redirect to the install page
├── scripts/
│   ├── snapshot-docs.sh      # Publishes the docs of a release into content/docs/<version>/
│   └── preview-drafts.sh     # Serves the unpublished draft docs locally at /docs/draft/
├── sass/
│   └── main.scss             # All styles
├── static/                   # Copied as-is to public/
│   ├── img/
│   ├── fonts/
│   └── js/
│       └── install.js        # Release downloads from the GitHub API, version switching
└── .github/
    └── workflows/
        └── deploy.yml        # Zola build → deploy to Cloudflare Pages
```

## Versions

The site publishes the documentation of **released** versions only, every release
and not just the latest. Nothing describing an unreleased version goes online.

- **Docs** — each release has its documentation frozen at `/docs/<version>/`; `/docs/`
  shows the latest release (`extra.docs.latest` in `config.toml`), and the
  version-less page URLs (`/docs/fly-cli/`, …) redirect to the latest release's pages.
  A picker on every docs page opens the same page in another release, and a banner on
  older releases points to the latest one.
- **Drafts** — `content/docs/Fly-*.md` are the working copy of the docs, edited along
  with the version in development. Zola skips them (`ignored_content` in
  `config.toml`), so they are published only when a release snapshots them. To preview
  them locally, run `scripts/preview-drafts.sh` (`zola serve` with the drafts at
  `/docs/draft/`, a gitignored copy removed on exit).
- **Install** — downloads default to the latest release; a picker (or `?version=<x>`
  in the URL) shows the packages of any other release, read from the GitHub releases
  API. The build-from-source instructions follow the chosen release, because each one
  builds from its own tag with the toolchain of its time. `/install/<version>/` links
  straight to them.

### Adding a release

When a new Fly version is released:

1. Publish its docs from the drafts, then move the `/docs/fly-*/` redirects onto it:
   ```bash
   scripts/snapshot-docs.sh 0.16.0
   scripts/snapshot-docs.sh --latest 0.16.0
   ```
   The first command copies `content/docs/Fly-*.md` into `content/docs/0.16.0/` and
   rewrites the internal links so the snapshot only links to itself (pass a commit of
   this repo as a second argument to snapshot the docs as they were at that commit).
2. In `config.toml`, add the version at the front of `extra.docs.versions` and set
   `extra.docs.latest`.
3. Add `content/install/<version>.md` with its build instructions (`git clone --branch
   v<version>`), copying the previous release's page.

## Deployment

Deployments are automatic, through GitHub Actions:

- **Push to `main`** → build + production deploy to [flylang.org](https://flylang.org)
- **Pull request** → build + preview deploy with a unique URL

### Required GitHub secrets

| Secret | Where to find it |
|--------|------------------|
| `CLOUDFLARE_API_TOKEN` | Cloudflare → My Profile → API Tokens |
| `CLOUDFLARE_ACCOUNT_ID` | Cloudflare → right sidebar of the dashboard home |

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
