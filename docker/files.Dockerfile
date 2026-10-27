# Base image with the archive files: the tiles image + the rest of files/
# (pdfs, disks, uploads, MCCM/MCCW sites) at /srv/files.
# The site image (../Dockerfile) builds on top of this one.
#
# Built by hand (scripts/build-files-image.sh), with files/ as build context.
# Fast: the 9 GB of page viewer tiles are in the tiles image (tiles.Dockerfile).
ARG TILES_IMAGE=ghcr.io/digitalica/msxcomputermagazine-tiles:latest
FROM ${TILES_IMAGE}

# Links the package to the repository, so GitHub Actions can pull it.
LABEL org.opencontainers.image.source="https://github.com/digitalica/msxcomputermagazine"
LABEL org.opencontainers.image.description="MSX Computer Magazine archive files (pdfs, disks, page viewer tiles)"

COPY archief /srv/files/archief
COPY wp-content /srv/files/wp-content
COPY mccm /srv/files/mccm
COPY mccw /srv/files/mccw
