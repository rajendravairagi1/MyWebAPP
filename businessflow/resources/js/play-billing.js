/**
 * The billing page's "Renew Your Plan" screen (resources/views/billing/show.blade.php)
 * renders both a Google Play block and the existing UPI-QR block on every
 * load — this is the only place that decides, at runtime, which one a
 * given visitor actually sees. Play policy requires an in-app digital
 * subscription purchase to go through Play Billing, so the two must
 * never both show at once inside the Android app.
 *
 * `document.referrer` starting with "android-app://" is the standard,
 * Chrome-documented signal that the current page was opened by a
 * Trusted Web Activity rather than a normal browser tab — see
 * https://developer.chrome.com/docs/android/trusted-web-activity/ — so
 * everything below is skipped entirely for anyone in a regular mobile or
 * desktop browser, who keeps seeing exactly today's UPI flow.
 */
export function initPlayBilling() {
    const root = document.getElementById('play-billing-root');
    if (! root) return;

    const isTwa = document.referrer.startsWith('android-app://');
    if (! isTwa) return;

    root.style.display = '';
    document.getElementById('upi-payment-block')?.style.setProperty('display', 'none');
    document.getElementById('upi-payment-note')?.style.setProperty('display', 'none');

    root.querySelectorAll('[data-play-product]').forEach((button) => {
        button.addEventListener('click', () => startPlayPurchase(root, button));
    });
}

async function startPlayPurchase(root, button) {
    if (typeof PaymentRequest === 'undefined') {
        alert('Google Play Billing needs a newer version of the app — please update it from the Play Store.');

        return;
    }

    const originalLabel = button.textContent;
    button.disabled = true;
    button.textContent = 'Opening Google Play…';

    try {
        const request = new PaymentRequest(
            [{
                supportedMethods: 'https://play.google.com/billing',
                data: {
                    sku: button.dataset.playProduct,
                    // Lets the RTDN webhook (which has no logged-in
                    // session to work from) still attribute a purchase
                    // to this business — see
                    // GooglePlayBillingService::resolveBusinessFromObfuscatedId.
                    obfuscatedAccountId: String(root.dataset.businessId || ''),
                },
            }],
            { total: { label: 'Total', amount: { currency: 'INR', value: '0' } } },
        );

        if (! (await request.canMakePayment())) {
            alert('Google Play Billing is not available on this device right now.');

            return;
        }

        const paymentResponse = await request.show();
        const purchaseToken = paymentResponse.details?.purchaseToken || paymentResponse.details?.token;

        const response = await fetch(root.dataset.activateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ purchase_token: purchaseToken }),
        });

        await paymentResponse.complete(response.ok ? 'success' : 'fail');

        if (response.ok) {
            window.location.reload();
        } else {
            alert('Payment went through, but we could not confirm it here — please contact support with your Play Store order.');
        }
    } catch (error) {
        // A visitor closing the Play Billing sheet themselves throws
        // here too (AbortError) — not worth alerting for, just reset the
        // button so they can try again.
        if (error?.name !== 'AbortError') console.error(error);
    } finally {
        button.disabled = false;
        button.textContent = originalLabel;
    }
}
