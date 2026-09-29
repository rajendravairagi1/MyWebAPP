/**
 * The billing page's "Renew Your Plan" screen (resources/views/billing/show.blade.php)
 * renders both a Google Play block and the existing UPI-QR block on every
 * load — this is the only place that decides, at runtime, which one a
 * given visitor actually sees. Play policy requires an in-app digital
 * subscription purchase to go through Play Billing, so the two must
 * never both show at once inside the Android app.
 *
 * This used to guess from document.referrer ("android-app://..." is
 * Chrome's documented signal that a page was opened by a Trusted Web
 * Activity). On a real device that signal never showed up at all — this
 * app's generated TWA shell apparently doesn't set it, so there was
 * nothing to detect. Guessing was the wrong approach anyway: what
 * actually matters is not "was this opened by a TWA" but "can this
 * browser actually process a Play Billing purchase right now" — and the
 * Payment Request API answers that directly via canMakePayment() for the
 * 'https://play.google.com/billing' method. A normal Chrome tab (or any
 * browser without Play Billing wired up) resolves that false; the
 * installed Android app resolves it true. No referrer, no persisted
 * flag, no cold-launch-vs-warm-resume edge cases — it just asks.
 */
async function canUsePlayBilling(sampleSku) {
    if (typeof PaymentRequest === 'undefined') return false;

    try {
        const request = new PaymentRequest(
            [{ supportedMethods: 'https://play.google.com/billing', data: { sku: sampleSku } }],
            { total: { label: 'Total', amount: { currency: 'INR', value: '0' } } },
        );

        return await request.canMakePayment();
    } catch (e) {
        return false;
    }
}

// TEMPORARY — remove once the "still showing UPI on a real Play Store
// install" report is confirmed fixed. Prints what this check saw at the
// top of the billing page itself, in plain text, so a screenshot from
// the real device tells us the result without needing dev tools.
function showDebugInfo(canUse) {
    const box = document.createElement('div');
    box.style.cssText = 'background:#000;color:#0f0;font:11px monospace;padding:8px;white-space:pre-wrap;word-break:break-all;';
    box.textContent = [
        'play-billing.js DEBUG',
        'PaymentRequest available: ' + (typeof PaymentRequest !== 'undefined'),
        'canUsePlayBilling(): ' + canUse,
        'UA: ' + navigator.userAgent,
    ].join('\n');
    document.body.prepend(box);
}

export async function initPlayBilling() {
    const root = document.getElementById('play-billing-root');
    if (! root) return;

    const firstButton = root.querySelector('[data-play-product]');
    const canUse = await canUsePlayBilling(firstButton?.dataset.playProduct);

    showDebugInfo(canUse);

    if (! canUse) return;

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
        // TEMPORARY — a visitor closing the Play Billing sheet themselves
        // also throws AbortError here, which is normal and not worth
        // alerting for on a finished build; alerting for it too, for now,
        // is the fastest way to see on-device exactly what's failing
        // when tapping a plan button does nothing visible. Revert to
        // "only log non-AbortError" once this is diagnosed.
        alert('DEBUG: ' + (error?.name || 'Error') + ' — ' + (error?.message || String(error)));
        console.error(error);
    } finally {
        button.disabled = false;
        button.textContent = originalLabel;
    }
}
