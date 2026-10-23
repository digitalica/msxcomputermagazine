import { defineCollection, reference } from 'astro:content';
import { file, glob } from 'astro/loaders';
import { z } from 'astro/zod';

/** Parameters of a WebMSX launch link, see src/lib/links.ts. */
const webmsx = z.object({
  machine: z.string(),
  disk: z.string().optional(),
  files: z.string().optional(),
  run: z.string().optional(),
  brun: z.string().optional(),
  keys: z.string().optional(),
});

/**
 * A page in the magazine. `pdf` and `reader` are only set when the page is in
 * another document than the issue itself.
 */
const pageRef = {
  page: z.number().int().positive().optional(),
  pdf: z.string().optional(),
  reader: z.number().int().positive().optional(),
};

const listing = z.object({
  title: z.string(),
  note: z.string().optional(),
  webmsx: webmsx.optional(),
  ...pageRef,
});

const disk = z.object({
  id: z.string(),
  webmsx,
  description: z.string().optional(),
  note: z.string().optional(),
  ...pageRef,
});

const series = defineCollection({
  loader: file('src/data/series.json'),
  schema: z.object({ title: z.string(), order: z.number() }),
});

const issues = defineCollection({
  loader: glob({ pattern: '*.md', base: 'src/content/issues' }),
  schema: ({ image }) =>
    z.object({
      title: z.string(),
      series: reference('series'),
      order: z.number(),
      cover: image(),
      pdf: z.string().optional(),
      /** Issue number in the page viewer (mcm-bladerkamer ?nr=). */
      reader: z.number().int().positive().optional(),
      /** Page with the disk subscription overview. */
      subscriptionPage: z.number().int().positive().optional(),
      disks: z.array(disk).default([]),
      sections: z
        .array(z.object({ title: z.string(), disk: z.string().optional(), items: z.array(listing) }))
        .default([]),
    }),
});

const pages = defineCollection({
  loader: glob({ pattern: '*.md', base: 'src/content/pages' }),
  schema: z.object({ title: z.string(), oldSlug: z.string().optional() }),
});

export const collections = { series, issues, pages };
