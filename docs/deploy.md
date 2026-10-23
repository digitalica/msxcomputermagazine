# Deploying

`pnpm build` produces a static site in `dist/`. Any web server can serve it,
but the site links to large archive files that are **not** in this repository.
They must stay available on the same host, at the same paths.

## Files that stay on the server

| Path | What |
|---|---|
| `/archief/bladen/` | magazine pdfs |
| `/archief/lb/` | listing book pdfs |
| `/archief/disks/` | disk images (WebMSX) |
| `/archief/diskzips/` | zipped disk contents (WebMSX `DISKA_FILES_URL`) |
| `/mcm_dzi/` | Deep Zoom tiles for the bladerkamer (must be same origin: no CORS headers) |
| `/mccm/`, `/mccw/` | separate static sites (MCCM product support, MCCW web magazine) |
| `/wp-content/uploads/2017/12/*.zip` | the six verzameldisk images, see `docs/migration-report.md` |

PDF and WebMSX links use `PUBLIC_FILES_ORIGIN` (default
`https://www.msxcomputermagazine.nl`, set in `src/lib/links.ts`), so a preview
deployment on another host still works. The bladerkamer tiles are always
loaded from the site itself.

The generated site uses `/archief/<slug>/` as well, next to these
directories. When uploading, **never use `rsync --delete` on the web root**
without excluding them:

```sh
rsync -av --delete \
  --exclude '/archief/bladen/' --exclude '/archief/lb/' \
  --exclude '/archief/disks/' --exclude '/archief/diskzips/' \
  --exclude '/mcm_dzi/' --exclude '/mccm/' --exclude '/mccw/' \
  --exclude '/wp-content/' \
  dist/ user@server:/path/to/webroot/
```

## nginx

The current server runs nginx with WordPress (PHP). After switching, no PHP is
needed. Suggested additions to the server block:

```nginx
# Generated pages are directories with index.html.
index index.html;
error_page 404 /404.html;

location / {
    try_files $uri $uri/ =404;
}

# Real redirects for old WordPress URLs (the build also writes meta-refresh
# pages for these, which work on any host).
location = /contact-2/  { return 301 /contact/; }
location = /archief/    { return 301 /; }
location = /feed/       { return 301 /feed.xml; }
location = /oeps/       { return 301 /; }

# Old WordPress endpoints.
location ~ ^/(wp-admin|wp-login\.php|xmlrpc\.php|wp-json) { return 410; }

# Hashed build assets can be cached forever.
location /_astro/ {
    add_header Cache-Control "public, max-age=31536000, immutable";
}
```

## Before switching DNS / web root

1. `pnpm build` and check `dist/` locally with `pnpm preview`.
2. Upload next to the existing site first (e.g. a `new.` subdomain) and click
   through: an issue page, a pdf, a WebMSX run link, the bladerkamer, search.
3. Keep a backup of the WordPress database and `wp-content/` until the new site
   has run for a while.
