// Minimal service worker - only exists so Chrome/Android considers the app
// "installable" (Add to Home Screen). Deliberately does NOT cache anything:
// this app is data-driven (ledger, invoices, balances), so every request
// must always go to the network - caching would risk showing stale figures.
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  event.respondWith(fetch(event.request));
});
