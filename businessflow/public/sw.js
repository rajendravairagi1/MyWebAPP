// Exists purely so the browser considers this site "installable" (Add to
// Home Screen) — a registered service worker is enough for that on
// modern Chrome/Edge; it doesn't need to actually intercept anything.
//
// There used to be a `fetch` handler here too (just forwarding every
// request straight to the network, functionally a no-op). It's
// deliberately gone now: on mobile, ANY active fetch handler disqualifies
// a page from the browser's back/forward cache (bfcache), which is what
// makes the phone's Back button restore the previous page instantly. A
// service worker with no fetch handler at all doesn't have that problem
// — Back goes back immediately instead of re-fetching the whole page
// over the network and showing a loading flash, which is exactly what
// was happening before.
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    self.clients.claim();
});
