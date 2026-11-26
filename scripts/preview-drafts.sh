#!/usr/bin/env bash
# Preview the draft docs (content/docs/Fly-*.md) locally.
#
#   scripts/preview-drafts.sh [zola serve options]
#
# The drafts are never published (`ignored_content` in config.toml). This copies
# them into content/docs/draft/ (gitignored), serves the site with `zola serve`
# and removes the copy on exit. The preview is at /docs/draft/; rerun the script
# to pick up further edits to the drafts.
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
dest="$root/content/docs/draft"

cleanup() { rm -rf "$dest"; }
trap cleanup EXIT INT TERM

rm -rf "$dest"
"$root/scripts/snapshot-docs.sh" draft > /dev/null
sed -i 's/^source = .*/&\nnote = "Local preview of the draft docs of the version in development: never published."/' "$dest/_index.md"

echo "draft docs: http://127.0.0.1:1111/docs/draft/"
cd "$root"
zola serve "$@"
