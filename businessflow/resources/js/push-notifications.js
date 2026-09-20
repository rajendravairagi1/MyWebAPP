// Lets a signed-in phone get real notifications (meeting, follow-up and
// payment reminders) even when the app isn't open, via the standard Web
// Push API — works the same way whether the site is opened in Chrome or
// installed/wrapped as the Android app. The "Enable Notifications" banner
// (see the bell dropdown in layouts/app.blade.php) only shows itself once
// we know the browser supports this AND the user hasn't already answered
// the permission prompt one way or the other.

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; i += 1) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

function sendSubscriptionToServer(subscription) {
    const json = subscription.toJSON();

    return fetch('/push/subscribe', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({
            endpoint: json.endpoint,
            public_key: json.keys.p256dh,
            auth_token: json.keys.auth,
        }),
    });
}

async function subscribeToPush(vapidPublicKey) {
    const registration = await navigator.serviceWorker.ready;
    let subscription = await registration.pushManager.getSubscription();

    if (! subscription) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });
    }

    await sendSubscriptionToServer(subscription);
}

export function initPushNotifications() {
    const banner = document.getElementById('push-enable-banner');
    const button = document.getElementById('push-enable-button');

    if (! banner || ! button) {
        return;
    }

    const supported = 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;

    if (! supported || Notification.permission !== 'default') {
        // Already granted (quietly re-subscribe in case the old
        // subscription expired) or denied (never nag again) — either
        // way the banner itself stays hidden.
        if (supported && Notification.permission === 'granted') {
            subscribeToPush(button.dataset.vapidPublicKey).catch(() => {});
        }

        return;
    }

    banner.classList.remove('hidden');

    button.addEventListener('click', async () => {
        button.disabled = true;
        button.textContent = 'Enabling…';

        try {
            const permission = await Notification.requestPermission();

            if (permission === 'granted') {
                await subscribeToPush(button.dataset.vapidPublicKey);
            }
        } catch (err) {
            // Permission dialog dismissed, or the push service is
            // unreachable — either way, nothing to show the user beyond
            // the banner simply not going away.
        } finally {
            banner.classList.add('hidden');
        }
    });
}
