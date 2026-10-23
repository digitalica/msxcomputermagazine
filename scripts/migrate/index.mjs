// One-off migration of the old WordPress site to Astro content files.
//
//   pnpm migrate            use cached HTML from .cache/ where available
//   pnpm migrate --refresh  download everything again
//
// Writes src/content/{issues,pages}, src/data, src/assets/{covers,pages} and
// docs/migration-report.md. Re-running overwrites the generated files, so do
// manual edits only after the migration is final.
import { mkdir, writeFile, rm, readdir, readFile } from 'node:fs/promises';
import path from 'node:path';
import * as cheerio from 'cheerio';
import { stringify } from 'yaml';
import { OLD_SITE, fetchText, download, sitemapUrls } from './fetch.mjs';
import { parseIssue } from './issues.mjs';
import {
  makeTurndown,
  cleanHtml,
  pageContent,
  parseIndexTable,
  parseArtGallery,
  parseVerzameldisks,
} from './pages.mjs';

const refresh = process.argv.includes('--refresh');
const OUT = {
  issues: 'src/content/issues',
  pages: 'src/content/pages',
  data: 'src/data',
  covers: 'src/assets/covers',
  pageImages: 'src/assets/pages',
  verzameldisks: 'src/assets/verzameldisks',
  report: 'docs/migration-report.md',
};

// Old page slug -> new page slug. Other pages (home, search, bladerkamer,
// index1-50) are templates in src/pages.
const PAGES = {
  achtergrond: 'achtergrond',
  'contact-2': 'contact',
  'art-gallery': 'art-gallery',
  'mcm-verzameldisks': 'mcm-verzameldisks',
  oeps: 'oeps',
};

const report = [];
const td = makeTurndown();

function markdownFile(data, body = '') {
  return `---\n${stringify(data, { lineWidth: 0 })}---\n${body ? `\n${body.trim()}\n` : ''}`;
}

/** Issue order and series labels, as shown on the old home page grid. */
function parseHome(html) {
  const $ = cheerio.load(html);
  const series = $('a.filter[data-filter^="."]')
    .map((i, a) => ({
      id: $(a).attr('data-filter').slice(1),
      title: $(a).text().replace(/\s+/g, ' ').trim(),
      order: i + 1,
    }))
    .get();
  const order = $('article.grid a.portfolio-info-link')
    .map((_, a) => new URL($(a).attr('href')).pathname.replace(/^\/archief\/|\/$/g, ''))
    .get();
  return { series, order };
}

async function migrateIssues(order) {
  const urls = await sitemapUrls('posts-portfolio', { refresh });
  const slugs = urls.map((u) => new URL(u).pathname.replace(/^\/archief\/|\/$/g, ''));
  const missing = slugs.filter((s) => !order.includes(s));
  if (missing.length) report.push(`- Issues not on the home page (sorted last): ${missing.join(', ')}`);

  await rm(OUT.issues, { recursive: true, force: true });
  await mkdir(OUT.issues, { recursive: true });
  for (const [i, url] of urls.entries()) {
    const slug = slugs[i];
    const warnings = [];
    const { data, bodyHtml, coverUrl } = parseIssue(await fetchText(url, { refresh }), (w) =>
      warnings.push(w),
    );
    const pos = order.indexOf(slug);
    data.order = pos === -1 ? order.length + i + 1 : pos + 1;
    if (coverUrl) {
      const file = path.basename(new URL(coverUrl).pathname);
      await download(coverUrl, path.join(OUT.covers, file));
      data.cover = `../../assets/covers/${file}`;
    }
    const { html, images } = cleanHtml(bodyHtml, {
      imageDir: OUT.pageImages,
      imageRef: '../../assets/pages',
    });
    for (const img of images) await download(img.url, img.file);
    await writeFile(path.join(OUT.issues, `${slug}.md`), markdownFile(orderKeys(data), td.turndown(html)));
    for (const w of warnings) report.push(`- \`${slug}\`: ${w}`);
  }
  return urls.length;
}

// Stable, readable key order in the frontmatter.
function orderKeys(d) {
  const keys = ['title', 'series', 'order', 'cover', 'pdf', 'reader', 'subscriptionPage', 'disks', 'sections'];
  return Object.fromEntries(keys.filter((k) => d[k] !== undefined).map((k) => [k, d[k]]));
}

async function migratePages() {
  await rm(OUT.pages, { recursive: true, force: true });
  await mkdir(OUT.pages, { recursive: true });
  for (const [oldSlug, slug] of Object.entries(PAGES)) {
    const { title, $, $content } = pageContent(await fetchText(`${OLD_SITE}/${oldSlug}/`, { refresh }));
    if (slug === 'art-gallery') await writeJson('art-gallery.json', parseArtGallery($, $content));
    if (slug === 'mcm-verzameldisks') {
      const disks = parseVerzameldisks($, $content);
      for (const d of disks) {
        for (const key of ['thumb', 'photo']) {
          const file = path.basename(new URL(d[key]).pathname);
          await download(d[key], path.join(OUT.verzameldisks, file));
          d[key] = file;
        }
      }
      await writeJson('verzameldisks.json', disks);
    }
    const { html: clean, images } = cleanHtml($content.html() ?? '', {
      imageDir: OUT.pageImages,
      imageRef: '../../assets/pages',
    });
    for (const img of images) await download(img.url, img.file);
    const data = { title };
    if (oldSlug !== slug) data.oldSlug = oldSlug;
    await writeFile(path.join(OUT.pages, `${slug}.md`), markdownFile(data, td.turndown(clean)));
  }
}

async function migrateIndex() {
  const rows = parseIndexTable(await fetchText(`${OLD_SITE}/index1-50/`, { refresh }));
  await writeJson('index-1-50.json', rows, 1);
  return rows.length;
}

async function writeJson(file, data, indent = 2) {
  await writeFile(path.join(OUT.data, file), JSON.stringify(data, null, indent) + '\n');
}

/** Files under /wp-content/ that the new site still links to (must stay on the server). */
async function wpContentReferences() {
  const refs = new Set();
  const files = await readdir('src', { recursive: true });
  for (const f of files.filter((f) => /\.(md|json)$/.test(f))) {
    const text = await readFile(path.join('src', f), 'utf8');
    for (const m of text.matchAll(/(?:https?:\/\/(?:www\.)?msxcomputermagazine\.nl)?(\/wp-content\/[^\s"')\]]+)/g)) {
      refs.add(m[1]);
    }
  }
  return [...refs].sort();
}

const home = parseHome(await fetchText(`${OLD_SITE}/`, { refresh }));
// Site logo (Wammes Witkop) and icons.
await download(`${OLD_SITE}/wp-content/uploads/2017/01/wammes.jpg`, 'src/assets/wammes.jpg');
await download(`${OLD_SITE}/wp-content/uploads/2017/01/cropped-wammes-32x32.jpg`, 'public/favicon.jpg');
await download(`${OLD_SITE}/wp-content/uploads/2017/01/cropped-wammes-180x180.jpg`, 'public/apple-touch-icon.jpg');
await mkdir(OUT.data, { recursive: true });
await writeJson('series.json', home.series);
const issueCount = await migrateIssues(home.order);
await migratePages();
const indexRows = await migrateIndex();
const wpRefs = await wpContentReferences();
if (wpRefs.length) {
  report.push(
    `\n## Files still served from /wp-content/\n\nThese must keep working on the server (or move and be relinked):\n\n` +
      wpRefs.map((r) => `- \`${r}\``).join('\n'),
  );
}

await mkdir(path.dirname(OUT.report), { recursive: true });
await writeFile(
  OUT.report,
  `# Migration report\n\nGenerated by \`pnpm migrate\` from ${OLD_SITE}.\n\n` +
    `- ${home.series.length} series, ${issueCount} issues, ${Object.keys(PAGES).length} pages, ${indexRows} index rows\n\n` +
    `## Things to review\n\n${report.join('\n') || 'None.'}\n`,
);
console.log(`${issueCount} issues, ${indexRows} index rows, ${report.length} report lines -> ${OUT.report}`);
