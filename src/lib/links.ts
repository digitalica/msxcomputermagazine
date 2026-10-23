// Links to the archive files (pdfs, disk images, page viewer tiles) and WebMSX.
//
// The archive files are not part of this repository. They live on the web
// server under /archief/, /mcm_dzi/ and /wp-content/uploads/. FILES_ORIGIN
// says where; WebMSX needs absolute URLs, and it lets a preview deployment
// elsewhere still use the real files.
export const FILES_ORIGIN: string =
  import.meta.env.PUBLIC_FILES_ORIGIN ?? 'https://www.msxcomputermagazine.nl';

export interface WebMSX {
  machine: string;
  disk?: string;
  files?: string;
  run?: string;
  brun?: string;
  keys?: string;
}

export interface PageRef {
  page?: number;
  pdf?: string;
  reader?: number;
}

export function fileUrl(path: string): string {
  return /^https?:\/\//.test(path) ? path : FILES_ORIGIN + encodeURI(path);
}

export function pdfUrl(pdf: string, page?: number): string {
  return fileUrl(pdf) + (page ? `#page=${page}` : '');
}

export function readerUrl(nr: number, page = 1): string {
  return `/mcm-bladerkamer/?nr=${nr}&pag=${page}`;
}

const WEBMSX_PARAMS: [keyof WebMSX, string][] = [
  ['machine', 'MACHINE'],
  ['disk', 'DISKA_URL'],
  ['files', 'DISKA_FILES_URL'],
  ['run', 'BASIC_RUN'],
  ['brun', 'BASIC_BRUN'],
  ['keys', 'BOOT_KEYS'],
];

export function webmsxUrl(w: WebMSX): string {
  const query = WEBMSX_PARAMS.filter(([key]) => w[key])
    .map(([key, param]) => {
      const value = key === 'disk' || key === 'files' ? fileUrl(w[key]!) : w[key]!;
      // Keep URLs inside the query readable, as WebMSX links usually are.
      return `${param}=${encodeURIComponent(value).replace(/%3A/g, ':').replace(/%2F/g, '/')}`;
    })
    .join('&');
  return `https://webmsx.org/?${query}`;
}

/** Resolve a page reference against the issue it appears in. */
export function pageLinks(issue: { pdf?: string; reader?: number }, ref: PageRef) {
  if (!ref.page) return {};
  const pdf = ref.pdf ?? issue.pdf;
  const reader = ref.reader ?? (ref.pdf ? undefined : issue.reader);
  return {
    pdf: pdf ? pdfUrl(pdf, ref.page) : undefined,
    reader: reader ? readerUrl(reader, ref.page) : undefined,
  };
}
