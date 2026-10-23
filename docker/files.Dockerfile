# Base image with the archive files: nginx + files/ at /srv/files.
# The site image (../Dockerfile) builds on top of this one.
#
# Built by hand (scripts/build-files-image.sh), with files/ as build context,
# because the page viewer tiles (files/mcm_dzi/) are not in the repository.
FROM docker.io/library/nginx:stable-alpine

# Links the package to the repository, so GitHub Actions can pull it.
LABEL org.opencontainers.image.source="https://github.com/digitalica/msxcomputermagazine"
LABEL org.opencontainers.image.description="MSX Computer Magazine archive files (pdfs, disks, page viewer tiles)"

# One layer per directory, largest and most stable first: updating the pdfs
# or disks does not re-upload the tiles.
COPY mcm_dzi /srv/files/mcm_dzi
COPY archief /srv/files/archief
COPY wp-content /srv/files/wp-content
COPY mccm /srv/files/mccm
COPY mccw /srv/files/mccw
