# Build the static site, then serve it with nginx.
# The archive files (pdfs, disks, page viewer tiles) are not in the image:
# mount them at /srv/files (see docker-compose.yml).

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

FROM docker.io/library/nginx:stable-alpine
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html
