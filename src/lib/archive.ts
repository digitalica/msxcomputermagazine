import { getCollection, type CollectionEntry } from 'astro:content';

export type Issue = CollectionEntry<'issues'>;
export type Series = CollectionEntry<'series'>;

export async function getIssues(): Promise<Issue[]> {
  return (await getCollection('issues')).sort((a, b) => a.data.order - b.data.order);
}

export async function getSeries(): Promise<Series[]> {
  return (await getCollection('series')).sort((a, b) => a.data.order - b.data.order);
}

/** Series in order, each with its issues in order. */
export async function getArchive() {
  const issues = await getIssues();
  return (await getSeries()).map((series) => ({
    series,
    issues: issues.filter((i) => i.data.series.id === series.id),
  }));
}

export const issueUrl = (issue: Issue) => `/archief/${issue.id}/`;
export const seriesUrl = (series: Series) => `/archief-categorie/${series.id}/`;
