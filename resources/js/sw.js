const cacheName = 'pwa-conf-v1';
const staticAssets = [
  '/js/app.js',
  '/css/app.css'
];

self.addEventListener('install', async event => {
  console.log('install event')
  const cache = await caches.open(cacheName);
  await cache.addAll(staticAssets);
});

self.addEventListener('fetch', async event => {
  console.log('fetch event')
});
