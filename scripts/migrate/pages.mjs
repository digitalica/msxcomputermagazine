// Converts regular WordPress pages to Markdown, and the Index 1-50 table to JSON.
import * as cheerio from 'cheerio';
import TurndownService from 'turndown';
import path from 'node:path';
import { localHref, parseWebmsx } from './issues.mjs';

const squash = (s) => s.replace(/\s+/g, ' ').trim();

/** Turndown configured for our content; tables and iframes stay HTML. */
export function makeTurndown() {
  const td = new TurndownService({ headingStyle: 'atx', bulletListMarker: '-', emDelimiter: '_' });
  td.keep(['table', 'iframe']);
  td.remove(['script', 'style']);
  // Intro paragraphs ("lead") stay HTML, so they keep their larger style.
  // Not with an image: Astro only resolves and optimizes Markdown images.
  td.addRule('lead', {
    filter: (node) =>
      node.nodeName === 'P' && node.getAttribute('class') === 'lead' && !node.querySelector('img'),
    replacement: (_content, node) => `\n\n${node.outerHTML}\n\n`,
  });
  return td;
}

/**
 * Rewrite links to the old host into site-relative paths, collect images from
 * wp-content/uploads (returned as { url, file }), and drop WordPress layout
 * wrappers and attributes.
 */
export function cleanHtml(html, { imageDir, imageRef }) {
  const $ = cheerio.load(html, null, false);
  const images = [];
  $('a[href]').each((_, a) => {
    const href = $(a).attr('href');
    if (/^(mailto|#)/.test(href)) return;
    $(a).attr('href', localHref(href));
  });
  $('img').each((_, img) => {
    const src = $(img).attr('src');
    if (!src?.includes('/wp-content/uploads/')) return;
    // Use the original upload, not a resized "-300x176" variant.
    const original = src.replace(/-\d+x\d+(\.\w+)$/, '$1');
    const file = path.basename(new URL(original).pathname);
    images.push({ url: original, file: path.join(imageDir, file) });
    $(img).replaceWith(`<img src="${imageRef}/${file}" alt="${$(img).attr('alt') ?? ''}">`);
  });
  $('.spacer-30, .clear').remove();
  $('[class]').not('p.lead').removeAttr('class');
  $('[style]').removeAttr('style');
  $('table [id]').removeAttr('id');
  // Unwrap bootstrap layout divs; their content is what matters.
  $('div').each((_, d) => $(d).replaceWith($(d).contents()));
  $('p').each((_, p) => {
    if (!squash($(p).text()) && !$(p).find('img, iframe').length) $(p).remove();
  });
  return { html: $.html(), images };
}

export function pageContent(html) {
  const $ = cheerio.load(html);
  const $content = $('article .entry-content').first();
  // "<page> – MSX Computer Magazine archief (1985 – 1997)"
  const title = squash($('title').text()).split(' – ')[0];
  // The content often repeats the title as its first heading.
  const $first = $content.find('h1, h2, h4').first();
  if (squash($first.text()).toLowerCase() === title.toLowerCase()) $first.remove();
  return { title, $, $content };
}

/** Art Gallery: one WebMSX link per row, "(*)" marks a partly corrupt disk. */
export function parseArtGallery($, $content) {
  const rows = $content
    .find('table tbody tr')
    .map((_, tr) => {
      const $a = $(tr).find('a').first();
      const u = new URL($a.attr('href'));
      const entry = {
        name: squash($a.text()).replace(/\.dsk$/i, ''),
        webmsx: parseWebmsx(u.href, (w) => {
          throw new Error(w);
        }),
      };
      if ($(tr).text().includes('(*)')) entry.corrupt = true;
      return entry;
    })
    .get();
  $content.find('table').remove();
  return rows;
}

/** Verzameldisks: thumbnail (linking to a large photo), description, WebMSX link. */
export function parseVerzameldisks($, $content) {
  const rows = $content
    .find('table tbody tr')
    .map((_, tr) => {
      const [thumbCell, descCell, diskCell] = $(tr).children('td').toArray();
      const $a = $(diskCell).find('a').first();
      $(descCell)
        .find('a')
        .each((_, a) => {
          $(a).attr('href', localHref($(a).attr('href'))).removeAttr('rel').removeAttr('target');
        });
      return {
        name: squash($a.text()).replace(/\.dsk$/i, ''),
        thumb: new URL($(thumbCell).find('img').attr('src'), 'https://msxcomputermagazine.nl/').href,
        photo: new URL($(thumbCell).find('a').attr('href'), 'https://msxcomputermagazine.nl/').href,
        description: $(descCell).html().trim(),
        webmsx: parseWebmsx($a.attr('href'), (w) => {
          throw new Error(w);
        }),
      };
    })
    .get();
  $content.find('table').remove();
  return rows;
}

export function parseIndexTable(html) {
  const $ = cheerio.load(html);
  const $table = $('table.igsv-table').first();
  const cols = ['nr', 'pag', 'paginas', 'titel', 'inhoud', 'soort', 'trefwoorden', 'oeps'];
  const header = $table
    .find('thead th')
    .map((_, th) => squash($(th).text()))
    .get();
  const expected = ['nr', 'pag', '#pag', 'titel', 'inhoud', 'soort', 'trefw', 'oeps'];
  if (header.join() !== expected.join()) throw new Error(`index table header changed: ${header}`);
  return $table
    .find('tbody tr')
    .map((_, tr) => {
      const cells = $(tr)
        .children('td')
        .map((_, td) => squash($(td).text()))
        .get();
      const row = {};
      cols.forEach((c, i) => {
        const v = cells[i] ?? '';
        if (v === '') return;
        row[c] = ['nr', 'pag', 'paginas'].includes(c) && /^\d+$/.test(v) ? Number(v) : v;
      });
      return row;
    })
    .get();
}
