#!/usr/bin/env bash
# Freeze the documentation of one Fly release into content/docs/<version>/.
#
#   scripts/snapshot-docs.sh <version> [<git-ref>]
#
# <version>  the published Fly release, e.g. 0.16.0 (no leading "v")
# <git-ref>  the commit of THIS repo whose content/docs/ documents that release;
#            omitted = the working tree (what you use on release day, before the
#            docs start describing the next version)
#
# The pages are copied verbatim except for internal links: `@/docs/Fly-X.md`
# becomes `@/docs/<version>/Fly-X.md`, and root-relative `/docs/fly-x/` becomes
# `/docs/<version>/fly-x/`, so a snapshot only ever links inside itself.
#
# After running it, add <version> to `extra.docs.versions` in config.toml
# (newest first) and, if it is the newest release, set `extra.docs.latest`.
# Build instructions for the release go in content/install/<version>.md.
set -euo pipefail

if [ $# -lt 1 ] || [ $# -gt 2 ]; then
    sed -n '2,12p' "$0" | sed 's/^# \{0,1\}//'
    exit 2
fi

version="$1"
ref="${2:-}"

case "$version" in
    v*) echo "error: give the version without the leading v ($version)" >&2; exit 2 ;;
esac

root="$(cd "$(dirname "$0")/.." && pwd)"
dest="$root/content/docs/$version"

if [ -e "$dest" ]; then
    echo "error: $dest already exists (remove it first to regenerate)" >&2
    exit 1
fi

mkdir -p "$dest"

rewrite() {
    # $1 = source path, contents on stdin
    sed -e "s#@/docs/Fly-#@/docs/$version/Fly-#g" \
        -e "s#](/docs/fly-#](/docs/$version/fly-#g" \
        -e 's/\r$//'
}

if [ -n "$ref" ]; then
    source_desc="$(git -C "$root" log -1 --format='%h %cs %s' "$ref")"
    files="$(git -C "$root" ls-tree --name-only "$ref" content/docs/ | grep -E '/Fly-[^/]*\.md$')"
    for f in $files; do
        git -C "$root" show "$ref:$f" | rewrite "$f" > "$dest/$(basename "$f")"
    done
else
    source_desc="working tree, $(date +%F)"
    for f in "$root"/content/docs/Fly-*.md; do
        rewrite "$f" < "$f" > "$dest/$(basename "$f")"
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
