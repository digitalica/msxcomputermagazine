// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import { readFile } from 'node:fs/promises';
import path from 'node:path';

// The page viewer tiles live on the production server, which sends no CORS
// headers. Proxy them so the bladerkamer also works in `astro dev`/`preview`.
const proxy = {
  '/mcm_dzi/': { target: 'https://msxcomputermagazine.nl', changeOrigin: true },
};

// The search index is made by `pagefind` after `astro build`. Serve the one
// from the last build in `astro dev`, so the search page works there too.
/** @type {Record<string, string>} */
const TYPES = { '.js': 'text/javascript', '.css': 'text/css', '.json': 'application/json' };
/** @type {import('astro').ViteUserConfig['plugins']} */
const pagefindInDev = [
  {
    name: 'pagefind-in-dev',
    configureServer(server) {
      const root = path.resolve('dist/pagefind');
      server.middlewares.use('/pagefind/', async (req, res, next) => {
        const file = path.join(root, decodeURIComponent((req.url ?? '').split('?')[0]));
        if (!file.startsWith(root + path.sep)) return next();
        try {
          const data = await readFile(file);
          res.setHeader('Content-Type', TYPES[path.extname(file)] ?? 'application/octet-stream');
          res.end(data);
        } catch {
          next();
        }
      });
    },
  },
];

// https://astro.build/config
export default defineConfig({
  site: 'https://msxcomputermagazine.nl',
  // Same URLs as the old WordPress site: /archief/mcm-1/ etc.
  trailingSlash: 'always',
  integrations: [sitemap()],
  redirects: {
    '/contact-2/': '/contact/',
    '/archief/': '/',
  },
  vite: {
    plugins: pagefindInDev,
    server: { proxy },
    preview: { proxy },
  },
});
