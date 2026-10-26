// Page viewer for all scanned magazine pages.
//
// The pages form one Deep Zoom image on the server (/mcm_dzi/): one row per
// issue (reader nr), one column per page, every page PAGE_W x PAGE_H pixels.
// ?nr=<issue>&pag=<page> zooms in on that page.
import OpenSeadragon from 'openseadragon';

const PAGE_W = 1760;
const PAGE_H = 2440;

// OpenSeadragon's button images, bundled by Vite: "zoomin_rest.png" -> url.
const images = import.meta.glob<string>('/node_modules/openseadragon/build/openseadragon/images/*.png', {
  eager: true,
  query: '?url',
  import: 'default',
});
const image = (name: string) =>
  Object.entries(images).find(([path]) => path.endsWith(`/${name}.png`))?.[1] ?? '';
const button = (name: string) => ({
  REST: image(`${name}_rest`),
  GROUP: image(`${name}_grouphover`),
  HOVER: image(`${name}_hover`),
  DOWN: image(`${name}_pressed`),
});

const container = document.getElementById('osd-container')!;
const viewer = OpenSeadragon({
  element: container,
  tileSources: container.dataset.tiles!,
  // navImages are full URLs; OpenSeadragon would otherwise prefix them with "/images/".
  prefixUrl: '',
  navImages: {
    zoomIn: button('zoomin'),
    zoomOut: button('zoomout'),
    home: button('home'),
    fullpage: button('fullpage'),
    rotateleft: button('rotateleft'),
    rotateright: button('rotateright'),
    flip: button('flip'),
    previous: button('previous'),
    next: button('next'),
  },
  showNavigator: true,
});

function gotoPage(nr: number, pag: number) {
  const rect = viewer.viewport.imageToViewportRectangle(PAGE_W * (pag - 1), PAGE_H * (nr - 1), PAGE_W, PAGE_H);
  viewer.viewport.fitBounds(rect, true);
}

const params = new URLSearchParams(location.search);
const nr = Number.parseInt(params.get('nr') ?? '', 10);
const pag = Number.parseInt(params.get('pag') ?? '1', 10) || 1;
if (nr > 0) viewer.addOnceHandler('open', () => gotoPage(nr, pag));
