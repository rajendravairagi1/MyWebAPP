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

// Meeting/follow-up/payment reminder alerts (see App\Support\PushNotifier)
// land here even when no tab is open. This listener is a `push` handler,
// not a `fetch` handler — it doesn't touch bfcache eligibility the way a
// `fetch` listener would (see the note above).
self.addEventListener('push', (event) => {
    let data = {};

    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'Pro Builder CRM', body: event.data ? event.data.text() : '' };
    }

    const title = data.title || 'Pro Builder CRM';
    const options = {
        body: data.body || '',
        icon: '/pwa-icon/192',
        badge: '/pwa-icon/192',
        data: { url: data.url || '/dashboard' },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(new URL(url, self.location.origin).pathname) && 'focus' in client) {
                    return client.focus();
                }
            }

            if (self.clients.openWindow) {
                return self.clients.openWindow(url);
            }
        })
    );
});
