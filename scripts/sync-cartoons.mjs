// Copies the cartoons (data and cleaned images) from the mcm-cartoons repository (made by Hayo).
//
//   node scripts/sync-cartoons.mjs [path to mcm-cartoons]     default: ../mcm-cartoons
//
// Cartoons and their texts are edited in that repository; src/data/cartoons.json
// and src/assets/cartoons/ are overwritten here.
import { copyFile, mkdir, readFile, rm, writeFile } from 'node:fs/promises';
import path from 'node:path';

const website = path.resolve(process.argv[2] ?? '../mcm-cartoons', 'website');
const ASSETS = 'src/assets/cartoons';

const cartoons = JSON.parse(await readFile(path.join(website, 'src/data/cartoons.json'), 'utf8'));

await rm(ASSETS, { recursive: true, force: true });
await mkdir(ASSETS, { recursive: true });

for (const cartoon of cartoons) {
  // "/cartoons/cartoon_x.png": the base path of that site plus a file in public/.
  const file = path.basename(cartoon.image);
  await copyFile(path.join(website, 'public', file), path.join(ASSETS, file));
  cartoon.image = `../assets/cartoons/${file}`;
  // Repair an opening tag without its ">": "<strongRepWek</strong>".
  cartoon.context = cartoon.context.replace(/<strong(?!>)/g, '<strong>');
}

await writeFile('src/data/cartoons.json', JSON.stringify(cartoons, null, 2) + '\n');
console.log(`${cartoons.length} cartoons`);
