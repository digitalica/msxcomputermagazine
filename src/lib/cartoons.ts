import { getCollection, type CollectionEntry } from 'astro:content';

export type Cartoon = CollectionEntry<'cartoons'>;

/** All cartoons, in order of appearance in the magazine. */
export async function getCartoons(): Promise<Cartoon[]> {
  return (await getCollection('cartoons')).sort(
    (a, b) => a.data.issue - b.data.issue || a.data.page - b.data.page || a.id.localeCompare(b.id),
  );
}

export const cartoonUrl = (cartoon: Cartoon) => `/cartoons/${cartoon.data.slug}/`;
