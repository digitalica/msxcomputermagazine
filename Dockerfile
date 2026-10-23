# syntax=docker/dockerfile:1
# Build the static site, then serve it with nginx on top of the archive
# files image (docker/files.Dockerfile), which holds pdfs, disks and tiles.

ARG FILES_IMAGE=ghcr.io/digitalica/msxcomputermagazine-files:latest

FROM docker.io/library/node:24.21.0-alpine AS build
ENV COREPACK_ENABLE_DOWNLOAD_PROMPT=0
RUN corepack enable
WORKDIR /app
# .npmrc is not copied (.dockerignore): it only pins the Node version for
# local use, and this image already has it.
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile
COPY . .
RUN pnpm build

FROM ${FILES_IMAGE}
# --link: these layers do not depend on the (large) base layers, so the build
# does not need to download them, and only these layers change per release.
COPY --link docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --link --from=build /app/dist /usr/share/nginx/html
