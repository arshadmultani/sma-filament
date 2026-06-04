importScripts('https://storage.googleapis.com/workbox-cdn/releases/5.1.2/workbox-sw.js');

// Bump this string on any strategy change to force every client onto the new SW.
const PRECACHE = 'offline-v2';
const OFFLINE_URL = '/offline'; // Blade route

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(PRECACHE)
      .then((cache) => cache.add(new Request(OFFLINE_URL, { cache: 'reload' })))
  );
  self.skipWaiting();
});

// Take over open tabs immediately and drop caches from older SW versions.
self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keep = ['offline-v2', 'vite-assets', 'filament-assets', 'static-resources'];
    const names = await caches.keys();
    await Promise.all(names.filter((n) => !keep.includes(n)).map((n) => caches.delete(n)));
    await self.clients.claim();
  })());
});

workbox.setConfig({ debug: false });

const { registerRoute } = workbox.routing;
const { CacheFirst, StaleWhileRevalidate } = workbox.strategies;
const { ExpirationPlugin } = workbox.expiration;

// 1) Hashed Vite build output (/build/assets/app-XXXX.js). Content-hashed, so the
//    filename itself changes on every deploy — safe to cache "forever". This is the
//    bulk of the bytes that made cold opens slow.
registerRoute(
  ({ url }) => url.pathname.startsWith('/build/'),
  new CacheFirst({
    cacheName: 'vite-assets',
    plugins: [new ExpirationPlugin({ maxEntries: 200, maxAgeSeconds: 60 * 60 * 24 * 30 })],
  })
);

// 2) Filament + Livewire static assets. They carry a ?v= version query, so revalidate
//    in the background: instant from cache, silently updated when the version bumps.
registerRoute(
  ({ url }) =>
    url.pathname.startsWith('/css/filament') ||
    url.pathname.startsWith('/js/filament') ||
    url.pathname.startsWith('/livewire/livewire.js'),
  new StaleWhileRevalidate({ cacheName: 'filament-assets' })
);

// 3) Other same-origin fonts / images / styles / scripts (icons, etc.).
registerRoute(
  ({ url, request }) =>
    url.origin === self.location.origin &&
    ['font', 'image', 'style', 'script'].includes(request.destination),
  new StaleWhileRevalidate({ cacheName: 'static-resources' })
);

// 4) Everything dynamic — page navigations (HTML) and Livewire/XHR data POSTs — is
//    intentionally NOT cached. Always hit the network so the dashboard data is fresh
//    (this is what avoids the old "must hard-refresh" staleness). Only when the
//    network genuinely fails do navigations fall back to the offline page.
if (workbox.navigationPreload.isSupported()) {
  workbox.navigationPreload.enable();
}

self.addEventListener('fetch', (event) => {
  if (event.request.mode !== 'navigate') {
    return; // assets handled by registerRoute above; data falls through to network
  }
  event.respondWith((async () => {
    try {
      const preloadResp = await event.preloadResponse;
      if (preloadResp) return preloadResp;
      return await fetch(event.request);
    } catch (error) {
      const cache = await caches.open(PRECACHE);
      return cache.match(OFFLINE_URL);
    }
  })());
});
