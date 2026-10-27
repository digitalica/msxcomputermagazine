#!/bin/sh
# Build (and with --push, push) the archive files image.
#
#   scripts/build-files-image.sh                  build the files image
#   scripts/build-files-image.sh --push           build and push it
#   scripts/build-files-image.sh --tiles [--push] also (re)build the tiles image first
#
# The files image starts from the tiles image (page viewer tiles, 9 GB, never
# change). Without --tiles that image must exist locally or in the registry.
# --tiles needs files/mcm_dzi/ (not in git): node scripts/download-archive.mjs --dzi
#
# Pushing needs `docker login ghcr.io` with a token that has write:packages.
# After pushing, rebuild the site image (run the GitHub workflow manually).
set -eu

TILES=ghcr.io/digitalica/msxcomputermagazine-tiles:latest
FILES=ghcr.io/digitalica/msxcomputermagazine-files:latest
DOCKER=${DOCKER:-docker}
cd "$(dirname "$0")/.."

tiles=false
push=false
for arg in "$@"; do
  case "$arg" in
    --tiles) tiles=true ;;
    --push) push=true ;;
    *) echo "unknown option: $arg" >&2; exit 1 ;;
  esac
done

for dir in archief wp-content mccm mccw; do
  [ -d "files/$dir" ] || { echo "missing files/$dir" >&2; exit 1; }
done

if $tiles; then
  [ -f files/mcm_dzi/mcm_dzi.dzi ] || { echo "missing files/mcm_dzi/ (tiles)" >&2; exit 1; }
  "$DOCKER" build -f docker/tiles.Dockerfile -t "$TILES" files/
  if $push; then "$DOCKER" push "$TILES"; fi
fi

# --no-cache: the layer cache can miss added files (Podman reused a stale
# wp-content layer once), and without the tiles this build is fast anyway.
"$DOCKER" build --no-cache -f docker/files.Dockerfile -t "$FILES" files/
if $push; then "$DOCKER" push "$FILES"; fi
