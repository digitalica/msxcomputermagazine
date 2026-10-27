// Cached HTTP fetching for the migration. Raw HTML of the old site is kept in
// .cache/ so the parser can be re-run without hitting the server again.
import { mkdir, readFile, writeFile, stat } from 'node:fs/promises';
import path from 'node:path';

export const OLD_SITE = 'https://msxcomputermagazine.nl';
const CACHE_DIR = path.resolve('.cache/html');

function cacheFile(url) {
  const u = new URL(url);
  const rel = u.pathname.replace(/^\/|\/$/g, '').replace(/\//g, '__') || 'home';
  const query = u.search ? `__${u.search.slice(1).replace(/[^\w=-]+/g, '_')}` : '';
  return path.join(CACHE_DIR, `${rel}${query}.html`);
}

async function exists(file) {
  try {
    await stat(file);
    return true;
  } catch {
    return false;
  }
}

export async function fetchText(url, { refresh = false } = {}) {
  const file = cacheFile(url);
  if (!refresh && (await exists(file))) return readFile(file, 'utf8');
  const res = await fetch(url, { redirect: 'follow' });
  if (!res.ok) throw new Error(`GET ${url} -> ${res.status}`);
  const text = await res.text();
  await mkdir(CACHE_DIR, { recursive: true });
  await writeFile(file, text);
  return text;
}

/** Download a binary file to `dest` unless it is already there. */
export async function download(url, dest) {
  if (await exists(dest)) return false;
  const res = await fetch(url, { redirect: 'follow' });
  if (!res.ok) throw new Error(`GET ${url} -> ${res.status}`);
  await mkdir(path.dirname(dest), { recursive: true });
  await writeFile(dest, Buffer.from(await res.arrayBuffer()));
  return true;
}

export async function sitemapUrls(name, opts) {
  const xml = await fetchText(`${OLD_SITE}/wp-sitemap-${name}-1.xml`, opts);
  return [...xml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);
}
