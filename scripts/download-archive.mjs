// Downloads the archive files from the old server into files/, at their URL paths.
//
//   node scripts/download-archive.mjs          pdfs, disks, zips linked from the content
//   node scripts/download-archive.mjs --dzi    also the page viewer tiles (~150k files)
//
// Files that already exist are skipped, so it can be re-run after an interruption.
// The /mccm/ and /mccw/ sites are mirrored separately with wget (see docs/deploy.md).
import { mkdir, readdir, readFile, rename, stat, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { parse } from 'yaml';

const ORIGIN = 'https://msxcomputermagazine.nl';
const OUT = 'files';
// The old server rate-limits (429) aggressive clients.
const CONCURRENCY = Number(process.env.CONCURRENCY ?? 4);
const ARCHIVE = /^\/(archief\/(bladen|lb|disks|diskzips)|wp-content\/uploads|mcm_dzi)\//;

/** All strings in a parsed document that are archive file paths. */
function collect(value, out) {
  if (typeof value === 'string') {
    const p = value.replace(/^https?:\/\/(www\.)?msxcomputermagazine\.nl/, '');
    if (ARCHIVE.test(p)) out.add(p.split('#')[0]);
  } else if (value && typeof value === 'object') {
    for (const v of Object.values(value)) collect(v, out);
  }
}

async function linkedFiles() {
  const out = new Set();
  for (const dir of ['src/content/issues', 'src/content/pages']) {
    for (const f of await readdir(dir)) {
      const text = await readFile(path.join(dir, f), 'utf8');
      collect(parse(text.split(/^---$/m)[1]), out);
    }
  }
  for (const f of await readdir('src/data')) {
    collect(JSON.parse(await readFile(path.join('src/data', f), 'utf8')), out);
  }
  return [...out].sort();
}

/** Every tile of the Deep Zoom image, computed from the .dzi descriptor. */
async function dziFiles() {
  const xml = await (await fetch(`${ORIGIN}/mcm_dzi/mcm_dzi.dzi`)).text();
  const attr = (name) => Number(new RegExp(`${name}="(\\d+)"`).exec(xml)[1]);
  const [width, height, tile] = [attr('Width'), attr('Height'), attr('TileSize')];
  const format = /Format="(\w+)"/.exec(xml)[1];
  const maxLevel = Math.ceil(Math.log2(Math.max(width, height)));
  const files = ['/mcm_dzi/mcm_dzi.dzi'];
  for (let level = 0; level <= maxLevel; level++) {
    const scale = 2 ** (maxLevel - level);
    const cols = Math.ceil(Math.ceil(width / scale) / tile);
    const rows = Math.ceil(Math.ceil(height / scale) / tile);
    for (let x = 0; x < cols; x++) {
      for (let y = 0; y < rows; y++) files.push(`/mcm_dzi/mcm_dzi_files/${level}/${x}_${y}.${format}`);
    }
  }
  return files;
}

async function exists(file) {
  try {
    await stat(file);
    return true;
  } catch {
    return false;
  }
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

/** GET a file, retrying with backoff when the server rate-limits (429) or hiccups. */
async function fetchFile(p) {
  for (let attempt = 0; ; attempt++) {
    // The old server redirects missing files to its "oeps" page: treat as missing.
    const res = await fetch(ORIGIN + encodeURI(p), { redirect: 'manual' });
    if (res.status === 200) return Buffer.from(await res.arrayBuffer());
    await res.body?.cancel();
    if (![429, 502, 503, 504].includes(res.status) || attempt === 8) throw new Error(String(res.status));
    const retryAfter = Number(res.headers.get('retry-after'));
    await sleep(retryAfter > 0 ? retryAfter * 1000 : Math.min(60_000, 1000 * 2 ** attempt));
  }
}

async function downloadAll(paths) {
  let done = 0;
  let fetched = 0;
  let bytes = 0;
  const failed = [];
  let next = 0;
  async function worker() {
    while (next < paths.length) {
      const p = paths[next++];
      const dest = path.join(OUT, p);
      if (!(await exists(dest))) {
        try {
          const data = await fetchFile(p);
          await mkdir(path.dirname(dest), { recursive: true });
          // Write to a temp name first, so an interrupted run leaves no partial files.
          await writeFile(`${dest}.part`, data);
          await rename(`${dest}.part`, dest);
          fetched++;
          bytes += data.length;
        } catch (e) {
          failed.push(`${p} (${e.message})`);
        }
      }
      if (++done % 1000 === 0) console.log(`${done}/${paths.length} (${(bytes / 2 ** 20).toFixed(0)} MB new)`);
    }
  }
  await Promise.all(Array.from({ length: CONCURRENCY }, worker));
  console.log(`${paths.length} files: ${fetched} downloaded (${(bytes / 2 ** 20).toFixed(1)} MB), ${failed.length} failed`);
  for (const f of failed) console.log(`  failed: ${f}`);
}

let paths = await linkedFiles();
if (process.argv.includes('--dzi')) paths = paths.concat(await dziFiles());
await downloadAll(paths);
