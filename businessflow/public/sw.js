// Exists purely so the browser considers this site "installable" (Add to
// Home Screen) — every request just passes straight through to the
// network. Deliberately does NOT cache anything: this app's data changes
// constantly, and caching pages here would risk showing stale data the
// same way a host's own page cache already has once this session.
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});
