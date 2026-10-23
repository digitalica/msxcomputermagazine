# Base image with the archive files: nginx + files/ at /srv/files.
# The site image (../Dockerfile) builds on top of this one.
#
# Built by hand (scripts/build-files-image.sh), with files/ as build context,
# because the page viewer tiles (files/mcm_dzi/) are not in the repository.
FROM docker.io/library/nginx:stable-alpine

# Links the package to the repository, so GitHub Actions can pull it.
LABEL org.opencontainers.image.source="https://github.com/digitalica/msxcomputermagazine"
LABEL org.opencontainers.image.description="MSX Computer Magazine archive files (pdfs, disks, page viewer tiles)"

# Page viewer tiles: 9 GB, split into layers of at most ~2.5 GB (registries
# limit layer size). Most stable first, so updating the pdfs or disks does
# not re-upload the tiles. A directory COPY copies its contents, hence one
# line per zoom level.
ARG T=mcm_dzi/mcm_dzi_files
ARG D=/srv/files/mcm_dzi/mcm_dzi_files
COPY mcm_dzi/mcm_dzi.dzi /srv/files/mcm_dzi/
COPY $T/0 $D/0
COPY $T/1 $D/1
COPY $T/2 $D/2
COPY $T/3 $D/3
COPY $T/4 $D/4
COPY $T/5 $D/5
COPY $T/6 $D/6
COPY $T/7 $D/7
COPY $T/8 $D/8
COPY $T/9 $D/9
COPY $T/10 $D/10
COPY $T/11 $D/11
COPY $T/12 $D/12
COPY $T/13 $D/13
COPY $T/14 $D/14
COPY $T/15 $D/15
COPY $T/16 $D/16
COPY $T/17 $D/17
# Level 18 (full resolution, 6.3 GB) by tile column: 0-99, 100-199, 200+.
COPY $T/18/?_* $T/18/??_* $D/18/
COPY $T/18/1??_* $D/18/
COPY $T/18/2??_* $T/18/3??_* $D/18/

COPY archief /srv/files/archief
COPY wp-content /srv/files/wp-content
COPY mccm /srv/files/mccm
COPY mccw /srv/files/mccw
