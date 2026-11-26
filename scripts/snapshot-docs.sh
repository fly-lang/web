#!/usr/bin/env bash
# Publish the documentation of a Fly release.
#
#   scripts/snapshot-docs.sh <version> [<git-ref>]   freeze the docs into content/docs/<version>/
#   scripts/snapshot-docs.sh --latest <version>      make <version> the target of /docs/fly-*/
#
# <version>  the published Fly release, e.g. 0.16.0 (no leading "v")
# <git-ref>  the commit of THIS repo whose content/docs/Fly-*.md documents that
#            release; omitted = the working tree (what you use on release day)
#
# content/docs/Fly-*.md are the draft docs of the version in development: Zola
# does not publish them (`ignored_content` in config.toml). Only snapshots are
# published. The pages are copied verbatim except for internal links:
# `@/docs/Fly-X.md` becomes `@/docs/<version>/Fly-X.md` and `/docs/fly-x/`
# becomes `/docs/<version>/fly-x/`, so a snapshot only ever links inside itself.
#
# --latest moves the `aliases` that keep the version-less URLs /docs/fly-x/
# working (redirects) from every other snapshot onto <version>.
#
# Then in config.toml add <version> to `extra.docs.versions` (newest first) and,
# if it is the newest release, set `extra.docs.latest`. Build instructions for
# the release go in content/install/<version>.md.
set -euo pipefail

usage() {
    sed -n '2,22p' "$0" | sed 's/^# \{0,1\}//'
    exit 2
}

root="$(cd "$(dirname "$0")/.." && pwd)"

if [ "${1:-}" = "--latest" ]; then
    [ $# -eq 2 ] || usage
    version="$2"
    dest="$root/content/docs/$version"
    if [ ! -d "$dest" ]; then
        echo "error: no snapshot $dest (freeze it first)" >&2
        exit 1
    fi
    for f in "$root"/content/docs/*/Fly-*.md; do
        sed -i '/^aliases = \["\/docs\/fly-/d' "$f"
    done
    for f in "$dest"/Fly-*.md; do
        slug="$(basename "$f" .md | tr '[:upper:]' '[:lower:]')"
        sed -i "1a aliases = [\"/docs/$slug/\"]" "$f"
    done
    echo "/docs/fly-*/ now redirect to the $version pages"
    exit 0
fi

if [ $# -lt 1 ] || [ $# -gt 2 ]; then
    usage
fi

version="$1"
ref="${2:-}"

case "$version" in
    v*) echo "error: give the version without the leading v ($version)" >&2; exit 2 ;;
esac

dest="$root/content/docs/$version"

if [ -e "$dest" ]; then
    echo "error: $dest already exists (remove it first to regenerate)" >&2
    exit 1
fi

mkdir -p "$dest"

rewrite() {
    sed -e "s#@/docs/Fly-#@/docs/$version/Fly-#g" \
        -e "s#](/docs/fly-#](/docs/$version/fly-#g" \
        -e 's/\r$//'
}

if [ -n "$ref" ]; then
    source_desc="$(git -C "$root" log -1 --format='%h %cs %s' "$ref")"
    files="$(git -C "$root" ls-tree --name-only "$ref" content/docs/ | grep -E '/Fly-[^/]*\.md$')"
    for f in $files; do
        git -C "$root" show "$ref:$f" | rewrite > "$dest/$(basename "$f")"
    done
else
    source_desc="working tree, $(date +%F)"
    for f in "$root"/content/docs/Fly-*.md; do
        rewrite < "$f" > "$dest/$(basename "$f")"
    done
fi

cat > "$dest/_index.md" <<EOF
+++
title = "Documentation"
template = "docs.html"
sort_by = "weight"

[extra]
version = "$version"
source = "$source_desc"
+++

The documentation of Fly $version as it was published with the release.
EOF

echo "snapshot of $version from: $source_desc"
ls "$dest"
