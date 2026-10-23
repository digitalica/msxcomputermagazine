// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

// The page viewer tiles live on the production server, which sends no CORS
// headers. Proxy them so the bladerkamer also works in `astro dev`/`preview`.
const proxy = {
  '/mcm_dzi/': { target: 'https://msxcomputermagazine.nl', changeOrigin: true },
};

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
    server: { proxy },
    preview: { proxy },
  },
});
