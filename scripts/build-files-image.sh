#!/bin/sh
# Build (and with --push, push) the archive files image.
#
#   scripts/build-files-image.sh          build locally
#   scripts/build-files-image.sh --push   build and push to ghcr.io
#
# Needs files/ complete, including files/mcm_dzi/ (not in git):
#   node scripts/download-archive.mjs --dzi
# Pushing needs `docker login ghcr.io` with a token that has write:packages.
# After pushing, rebuild the site image (run the GitHub workflow manually).
set -eu

IMAGE=ghcr.io/digitalica/msxcomputermagazine-files:latest
cd "$(dirname "$0")/.."

for dir in mcm_dzi archief wp-content mccm mccw; do
  [ -d "files/$dir" ] || { echo "missing files/$dir" >&2; exit 1; }
done
[ -f files/mcm_dzi/mcm_dzi.dzi ] || { echo "missing files/mcm_dzi/mcm_dzi.dzi" >&2; exit 1; }

DOCKER=${DOCKER:-docker}
"$DOCKER" build -f docker/files.Dockerfile -t "$IMAGE" files/

if [ "${1:-}" = "--push" ]; then
  "$DOCKER" push "$IMAGE"
fi
