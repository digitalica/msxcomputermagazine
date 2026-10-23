import rss from '@astrojs/rss';
import type { APIContext } from 'astro';
import { getEntry } from 'astro:content';
import { getIssues, issueUrl } from '../lib/archive';

// All issues, newest addition to the archive first (highest order).
export async function GET(context: APIContext) {
  const issues = (await getIssues()).reverse();
  const items = await Promise.all(
    issues.map(async (issue) => {
      const series = await getEntry(issue.data.series);
      return {
        title: `${series.data.title} ${issue.data.title}`,
        link: issueUrl(issue),
        description: `${series.data.title}, ${issue.data.title}`,
      };
    }),
  );
  return rss({
    title: 'MSX Computer Magazine archief (1985 – 1997)',
    description: 'Alle nummers van MSX Computer Magazine, MS(X)DOS Computer Magazine en MSX Computer & Club Magazine.',
    site: context.site!,
    items,
    customData: '<language>nl</language>',
  });
}
