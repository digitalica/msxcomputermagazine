# msxcomputermagazine.nl

Static website of the MSX Computer Magazine archive (1985 – 1997), built with
[Astro](https://astro.build). It replaces the old WordPress site; the content
was migrated with `scripts/migrate/`.

## Getting started

Requires Node (any recent version) with corepack. pnpm downloads the Node
version pinned in `.npmrc` (Astro 7 needs Node ≥ 22.23.4).

```sh
corepack pnpm install
corepack pnpm dev        # http://localhost:4321
corepack pnpm build      # static site in dist/, plus the Pagefind search index
corepack pnpm preview    # serve dist/ (search only works after a build)
corepack pnpm check      # type-check
```

## Where things are

| Path | Contents |
|---|---|
| `src/content/issues/*.md` | one file per issue: frontmatter with pdf, disks and listings; optional Markdown text |
| `src/content/pages/*.md` | text pages (achtergrond, contact, intros) |
| `src/data/series.json` | the series (jaargangen) and their order |
| `src/data/index-1-50.json` | article index of MCM 1 – 50 |
| `src/data/art-gallery.json`, `verzameldisks.json` | Art Gallery disks and the collection disks |
| `src/assets/` | covers and images (optimized at build time) |
| `src/content.config.ts` | schema of all content: a build fails on invalid data |
| `src/lib/links.ts` | how pdf, bladerkamer and WebMSX links are built |
| `src/pages/` | page templates |
| `scripts/migrate/` | one-off migration from the WordPress site |
| `docs/` | migration report and deploy notes |

### An issue file

```yaml
---
title: nr. 1 – feb 1985
series: 1_mcm-1985-1988      # id from src/data/series.json
order: 1                     # position in the archive
cover: ../../assets/covers/mcm_cover001.jpg
pdf: /archief/bladen/msx_computer_magazine_01.pdf
reader: 1                    # issue number in the bladerkamer
disks:
  - id: MCM-D1
    webmsx: { machine: MSX1E, disk: /archief/disks/mcm/mcmd01.di1 }
sections:
  - title: Listings in dit nummer
    items:
      - title: Lampjes
        page: 40             # pdf and bladerkamer links are made from this
        webmsx: { machine: MSX1E, files: /archief/diskzips/mcmd01.zip, run: lampjes }
---
```

Paths to pdfs and disk images are paths on the web server: those files are not
in this repository (see `docs/deploy.md`).

## Migration

`corepack pnpm migrate` scrapes the old site again (cached in `.cache/`) and
**overwrites** `src/content`, `src/data` and the downloaded images. Only run it
before content is edited by hand. Findings are in `docs/migration-report.md`.
