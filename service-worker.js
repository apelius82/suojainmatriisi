const VERSION = '1.0.0';
const CACHE = `suojainmatriisi-${VERSION}`;
const CORE = [
  '/offline.html',
  '/assets/css/global.css',
  '/assets/css/nav.css',
  '/assets/css/layout.css',
  '/assets/js/modules/search.js',
  '/assets/img/icons/pwa-192.svg'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches
      .open(CACHE)
      .then((c) => c.addAll(CORE))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((k) => k.startsWith('suojainmatriisi-') && k !== CACHE)
          .map((k) => caches.delete(k))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  event.respondWith(
    fetch(event.request).catch(() =>
      caches.match(event.request).then((r) =>
        r || (event.request.mode === 'navigate' ? caches.match('/offline.html') : null)
      )
    )
  );
});
