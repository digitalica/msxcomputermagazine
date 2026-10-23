# Deploying

The site runs as a Docker container behind Traefik, like watbenjedan. There
are two images:

| Image | Contents | Built by |
|---|---|---|
| `ghcr.io/digitalica/msxcomputermagazine-files` | nginx + the archive files (`files/`) | by hand, `scripts/build-files-image.sh --push` |
| `ghcr.io/digitalica/msxcomputermagazine` | the files image + the generated site | GitHub Actions, on every push to `main` |

The site image starts `FROM` the files image and adds its layers with
`COPY --link`, so a release only adds a few MB: the server downloads the
archive layers once.

## Archive files

The old server is going away, so `files/` holds a full copy, at the URL paths:

```
files/
  archief/bladen/      magazine pdfs                                  (git)
  archief/lb/          listing book pdfs                              (git)
  archief/disks/       disk images (WebMSX)                           (git)
  archief/diskzips/    zipped disk contents (WebMSX DISKA_FILES_URL)  (git)
  wp-content/uploads/  the six verzameldisk zips                      (git)
  mccm/                MCCM support site (static)                     (git)
  mccw/                MCCW web magazine (static)                     (git)
  mcm_dzi/             page viewer tiles, ~200k files                 (NOT in git)
```

The tiles are not in git (too many files, several GB). They exist in the
files image and on the machine that built it. They can be extracted from the
image again, or regenerated from the pdfs (one row per issue, one column per
page, 1760×2440 per page; see `src/scripts/bladerkamer.ts`).

`files/` was filled from the old site with:

```sh
node scripts/download-archive.mjs --dzi     # everything linked + all tiles
wget --mirror --no-parent --no-host-directories --page-requisites -e robots=off \
  --directory-prefix=files https://msxcomputermagazine.nl/mccm/ https://msxcomputermagazine.nl/mccw/
```

Only files that something links to could be downloaded (the server has no
directory listings).

## Updating the files image

When something in `files/` changes:

```sh
docker login ghcr.io -u YOUR_GITHUB_USERNAME   # token with write:packages
scripts/build-files-image.sh --push            # DOCKER=podman also works
```

Then rebuild the site image: GitHub → Actions → "Build and push Docker image"
→ Run workflow (or push a commit). Pushes that only change `files/` do not
start a build by themselves.

The files image has a label linking it to this repository, so the workflow's
`GITHUB_TOKEN` can pull it. If the first workflow run cannot pull it, check
the package settings on GitHub: the repository must have access.

## Server

Log in once with a GitHub token that has `read:packages`:

```sh
echo YOUR_GITHUB_PAT | docker login ghcr.io -u YOUR_GITHUB_USERNAME --password-stdin
```

Copy `docker-compose.yml` to the server and start:

```sh
docker compose pull
docker compose up -d
```

The `proxy` network must already exist (shared with Traefik). Traefik handles
TLS and redirects `www.` to the apex domain. Updating is the same two
commands after a new image is built.

## Testing locally

```sh
docker build -t msxcomputermagazine .
docker run --rm -p 8080:80 msxcomputermagazine
```

With a locally built files image: add
`--build-arg FILES_IMAGE=ghcr.io/digitalica/msxcomputermagazine-files:latest`
(the default) after `scripts/build-files-image.sh`, or point it at another tag.

## Switching from WordPress

1. Push the files image, run the workflow, start the container on the server.
2. Check it before moving DNS, e.g. with a temporary extra Traefik host rule
   (`new.msxcomputermagazine.nl`) or `curl --resolve`: an issue page, a pdf, a
   WebMSX run link, the bladerkamer, search, `/mccm/`.
3. Point `msxcomputermagazine.nl` and `www.` to the Traefik server.
4. Keep a backup of the WordPress database and `wp-content/` for a while.

Old WordPress URLs are handled in `docker/nginx.conf`: `/contact-2/`,
`/archief/`, `/feed/` and `/oeps/` redirect; `wp-admin`, `wp-login.php`,
`xmlrpc.php` and `wp-json` return 410.
