# Deploying

The site runs as a Docker container behind Traefik, like watbenjedan:

- GitHub Actions builds the image on every push to `main` and pushes it to
  `ghcr.io/digitalica/msxcomputermagazine:latest` (`.github/workflows/docker.yml`).
- The image builds the Astro site and serves it with nginx (`Dockerfile`,
  `docker/nginx.conf`).
- On the server, `docker-compose.yml` runs it on the shared `proxy` network.
  Traefik handles TLS (Let's Encrypt) and redirects `www.` to the apex domain.

## Archive files

The pdfs, disk images and page viewer tiles are several GB and are **not** in
the repository or the image. They live in a directory on the server that is
mounted read-only into the container at `/srv/files`. Its layout follows the
URL paths:

```
files/
  archief/bladen/      magazine pdfs
  archief/lb/          listing book pdfs
  archief/disks/       disk images (WebMSX)
  archief/diskzips/    zipped disk contents (WebMSX DISKA_FILES_URL)
  mcm_dzi/             Deep Zoom tiles for the bladerkamer
  mccm/                MCCM support site (static)
  mccw/                MCCW web magazine (static)
  wp-content/uploads/  only the six verzameldisk zips in 2017/12/ are still linked
```

Copy these directories from the old web root once, e.g.:

```sh
rsync -av old-server:/path/to/webroot/{archief,mcm_dzi,mccm,mccw} files/
rsync -av --relative old-server:/path/to/webroot/./wp-content/uploads/2017/12/ files/
```

The issue pages (`/archief/mcm-1/` etc.) come from the image; only the
subdirectories above come from `files/`. nginx serves the archive paths with `Access-Control-Allow-Origin: *`, so WebMSX can
load disks and a preview deployment can use the tiles.

`FILES_DIR` sets where the directory is (default `./files` next to
`docker-compose.yml`).

## First-time server setup

Log in once with a GitHub PAT scoped to `read:packages` (needed while the
package is private):

```sh
echo YOUR_GITHUB_PAT | docker login ghcr.io -u YOUR_GITHUB_USERNAME --password-stdin
```

Copy `docker-compose.yml` to the server, put the archive files next to it in
`files/` (or set `FILES_DIR`), and start:

```sh
docker compose pull
docker compose up -d
```

The `proxy` network must already exist (shared with Traefik).

## Updating

Push to `main`, wait for the GitHub Action, then on the server:

```sh
docker compose pull
docker compose up -d
```

## Testing the image locally

```sh
docker build -t msxcomputermagazine .
docker run --rm -p 8080:80 -v "$PWD/files:/srv/files:ro" msxcomputermagazine
```

Without a `files/` directory the site works, but pdf, disk and tile requests
to localhost return 404. The pdf and WebMSX links themselves point to
`https://msxcomputermagazine.nl` (`PUBLIC_FILES_ORIGIN` in `src/lib/links.ts`).

## Switching from WordPress

1. Put the archive files in place and start the container.
2. Check it before moving DNS, e.g. with a temporary extra Traefik host rule
   (`new.msxcomputermagazine.nl`) or `curl --resolve`: an issue page, a pdf, a
   WebMSX run link, the bladerkamer, search.
3. Point `msxcomputermagazine.nl` and `www.` to the Traefik server.
4. Keep a backup of the WordPress database and `wp-content/` for a while.

Old WordPress URLs are handled in `docker/nginx.conf`: `/contact-2/`,
`/archief/`, `/feed/` and `/oeps/` redirect; `wp-admin`, `wp-login.php`,
`xmlrpc.php` and `wp-json` return 410.
