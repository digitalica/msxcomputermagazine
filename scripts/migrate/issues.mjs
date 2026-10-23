// Parses an issue ("portfolio") page of the old WordPress site into structured data.
//
// The issue bodies on the old site were generated from a dataset, using a few
// recognisable blocks:
//   .mcmpdf          link to the pdf
//   .mcmbladerkamer  link into the page viewer (?nr=)
//   .mcmdisk         "Start WebMSX met <disk>", a disk subscription list, or a
//                    "Disk DA61A: ..." block with the listings on that disk
//   .mcmlistings     "<p>heading</p><ul>listings</ul>" pairs
//   .mcminfo         free text
// Anything else is kept as free text (Markdown body).
import * as cheerio from 'cheerio';

const OWN_HOSTS = new Set(['msxcomputermagazine.nl', 'www.msxcomputermagazine.nl']);
const WEBMSX_PARAMS = {
  MACHINE: 'machine',
  DISKA_URL: 'disk',
  DISKA_FILES_URL: 'files',
  BASIC_RUN: 'run',
  BASIC_BRUN: 'brun',
  BOOT_KEYS: 'keys',
};

/** Site-relative, decoded path for links to our own host; absolute URL otherwise. */
export function localPath(href) {
  const u = new URL(href, 'https://msxcomputermagazine.nl/');
  if (!OWN_HOSTS.has(u.hostname)) return u.href;
  return decodeURI(u.pathname);
}

/** Like localPath, but keeps the query string and fragment. */
export function localHref(href) {
  const u = new URL(href, 'https://msxcomputermagazine.nl/');
  if (!OWN_HOSTS.has(u.hostname)) return u.href;
  return decodeURI(u.pathname) + u.search + u.hash;
}

/** WebMSX launch link -> { machine, disk|files, run|brun, keys }. */
export function parseWebmsx(href, warn) {
  const out = {};
  // Not URLSearchParams: it turns "+" into a space, and file names contain "+".
  const query = href.split('?')[1]?.split('#')[0] ?? '';
  for (const pair of query.split('&').filter(Boolean)) {
    const [key, raw = ''] = pair.split('=');
    const value = decodeURIComponent(raw);
    const name = WEBMSX_PARAMS[key];
    if (!name) {
      warn(`unknown WebMSX parameter ${key}=${value}`);
      continue;
    }
    out[name] = name === 'disk' || name === 'files' ? localPath(value) : value;
  }
  return out;
}

function pdfPage(href) {
  const m = /#page=(\d+)/.exec(href);
  return m ? Number(m[1]) : undefined;
}

function readerRef(href) {
  const u = new URL(href, 'https://msxcomputermagazine.nl/');
  return { nr: Number(u.searchParams.get('nr')), pag: Number(u.searchParams.get('pag')) };
}

const isPdf = (href) => /\.pdf(#|$)/i.test(href);
const isReader = (href) => href.includes('mcm-bladerkamer');
const isWebmsx = (href) => href.includes('webmsx.org');
const squash = (s) => s.replace(/\s+/g, ' ').trim();

/**
 * Collect the page reference from the pdf/reader links in `$el`.
 * Returns { page, pdf?, reader? }: `pdf`/`reader` are only set when they point
 * to another document than the issue itself.
 */
function pageRef($, $el, issue, warn) {
  const ref = {};
  let readerPag;
  $el.find('a').each((_, a) => {
    const href = $(a).attr('href') ?? '';
    if (isPdf(href)) {
      ref.page = pdfPage(href);
      const file = localPath(href.split('#')[0]);
      if (file !== issue.pdf) ref.pdf = file;
    } else if (isReader(href)) {
      const r = readerRef(href);
      readerPag = r.pag;
      if (r.nr !== issue.reader) ref.reader = r.nr;
    }
  });
  if (ref.page === undefined && readerPag !== undefined) ref.page = readerPag;
  if (ref.page !== undefined && readerPag !== undefined && readerPag !== ref.page) {
    warn(`page ${ref.page} in pdf link but ${readerPag} in reader link (kept pdf page)`);
  }
  return ref;
}

const PAGE_NOTE = /\(?\s*(zie p|pagina)\s*\d+\s*in\s*pdf(\s*of\s*bladerkamer)?\s*\)?/i;

function parseListing($, li, issue, warn) {
  const $li = $(li);
  const item = {};
  const runLink = $li.find('a').filter((_, a) => isWebmsx($(a).attr('href') ?? '')).first();
  if (runLink.length) {
    item.title = squash(runLink.text());
  } else {
    const m = /naar:\s*(.+)$/s.exec(squash($li.text()));
    if (m) item.title = m[1];
  }
  Object.assign(item, pageRef($, $li, issue, warn));
  if (runLink.length) item.webmsx = parseWebmsx(runLink.attr('href'), warn);

  // Without a run link or "naar:", the item is "run <file>" or "pdf, blader of
  // run <file>": a file on the disk that has no WebMSX link.
  const text = squash($li.text());
  if (!item.title) item.title = text.replace(/^(?:(?:pdf|blader|of|run)\b[,:]?\s*)+/, '');

  // Anything left after removing the known words is unexpected: keep it as a note.
  const rest = squash(text.replace(item.title, '').replace(/\b(pdf|blader|of|run|naar)\b|[,:]/g, ''));
  if (rest) {
    item.note = rest;
    warn(`listing "${item.title}": unparsed text "${rest}"`);
  }
  return item;
}

function parseDiskLink($a, warn) {
  const id = squash($a.text());
  return { id, webmsx: parseWebmsx($a.attr('href'), warn) };
}

function parseDiskBlock($, el, issue, out, warn) {
  const $el = $(el);
  const $ul = $el.children('ul');
  const intro = squash(
    $el
      .contents()
      .filter((_, n) => !(n.type === 'tag' && n.name === 'ul'))
      .text(),
  );

  if ($ul.length && /^Diskabonnement/i.test(intro)) {
    // Disk subscription: one disk per <li>, optionally with a page reference.
    const introRef = pageRef($, $el.clone().children('ul').remove().end(), issue, warn);
    if (introRef.page !== undefined) out.subscriptionPage = introRef.page;
    $ul.children('li').each((_, li) => {
      const $li = $(li);
      const $a = $li.find('a').filter((_, a) => isWebmsx($(a).attr('href') ?? '')).first();
      const disk = parseDiskLink($a, warn);
      const description = squash($li.text().replace(disk.id, '').replace(PAGE_NOTE, ''));
      if (description) disk.description = description;
      Object.assign(disk, pageRef($, $li, issue, warn));
      out.disks.push(disk);
    });
    return;
  }

  const diskSection = /^Disk (\S+):\s*(.*)$/.exec(intro);
  if ($ul.length && diskSection) {
    out.sections.push({
      title: `${diskSection[1]}: ${diskSection[2]}`,
      disk: diskSection[1],
      items: $ul
        .children('li')
        .map((_, li) => parseListing($, li, issue, warn))
        .get(),
    });
    return;
  }

  const $a = $el.find('a').first();
  if (/^Start WebMSX met/i.test(intro) && $a.length && isWebmsx($a.attr('href'))) {
    const disk = parseDiskLink($a, warn);
    const note = squash(intro.replace(/^Start WebMSX met/i, '').replace(disk.id, ''));
    if (note) disk.note = note;
    out.disks.push(disk);
    return;
  }

  warn(`unrecognised .mcmdisk block: ${intro.slice(0, 80)}`);
  out.extraHtml.push($.html(el));
}

function parseListingsBlock($, el, issue, out, warn) {
  let section;
  $(el)
    .children()
    .each((_, child) => {
      if (child.name === 'p') {
        section = { title: squash($(child).text()).replace(/:$/, ''), items: [] };
        out.sections.push(section);
      } else if (child.name === 'ul') {
        if (!section) {
          section = { title: 'Listings', items: [] };
          out.sections.push(section);
        }
        $(child)
          .children('li')
          .each((_, li) => section.items.push(parseListing($, li, issue, warn)));
      } else {
        warn(`unexpected <${child.name}> in .mcmlistings`);
      }
    });
}

/**
 * @returns {{ data: object, bodyHtml: string, coverUrl?: string }}
 */
export function parseIssue(html, warn) {
  const $ = cheerio.load(html);
  const $article = $('article.portfolio').first();
  const series = /portfolio-category-(\S+)/.exec($article.attr('class') ?? '')?.[1];
  const title = squash($article.find('h1.entry-title').first().text());
  const coverUrl = $article.find('figure.featured-image img').first().attr('src');
  const $body = $article.find('[itemprop="articleBody"]').first();

  const out = { disks: [], sections: [], extraHtml: [], info: [] };
  const issue = {};

  // The pdf/reader links define what "this issue" is, so read them first.
  $body.find('.mcmpdf a').each((_, a) => (issue.pdf = localPath($(a).attr('href'))));
  $body.find('.mcmbladerkamer a').each((_, a) => (issue.reader = readerRef($(a).attr('href')).nr));

  $body.contents().each((_, node) => {
    if (node.type === 'text') {
      if (squash($(node).text())) out.extraHtml.push($(node).text());
      return;
    }
    if (node.type !== 'tag') return;
    const $n = $(node);
    if ($n.is('.mcmpdf, .mcmbladerkamer')) return;
    if ($n.is('.mcmdisk')) return parseDiskBlock($, node, issue, out, warn);
    if ($n.is('.mcmlistings')) return parseListingsBlock($, node, issue, out, warn);
    if ($n.is('.mcminfo')) return out.info.push($n.html());
    if (!squash($n.text()) && !$n.find('img').length) return; // empty <p>
    out.extraHtml.push($.html(node));
  });

  const details = $article
    .find('.entry-details li')
    .map((_, li) => squash($(li).text()))
    .get()
    .filter(Boolean);
  if (details.length) warn(`entry details not migrated: ${details.join(' | ')}`);

  const data = { title, series };
  if (issue.pdf) data.pdf = issue.pdf;
  if (issue.reader) data.reader = issue.reader;
  if (out.subscriptionPage !== undefined) data.subscriptionPage = out.subscriptionPage;
  if (out.disks.length) data.disks = out.disks;
  if (out.sections.length) data.sections = out.sections;
  if (!series) warn('no series');
  if (!coverUrl) warn('no cover image');

  return { data, bodyHtml: [...out.info, ...out.extraHtml].join('\n\n'), coverUrl };
}
