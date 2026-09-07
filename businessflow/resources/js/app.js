import Alpine from 'alpinejs';
import {
    Chart, BarController, BarElement, LineController, LineElement, PointElement,
    DoughnutController, ArcElement, CategoryScale, LinearScale, Tooltip, Legend, Filler,
} from 'chart.js';

Chart.register(
    BarController, BarElement, LineController, LineElement, PointElement,
    DoughnutController, ArcElement, CategoryScale, LinearScale, Tooltip, Legend, Filler,
);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();

// A slow connection or an impatient double-tap on "Record Payment" (or
// any other submit button) used to fire the same form twice, creating a
// duplicate entry — the server had no way to tell that apart from two
// separate payments. Disabling every submit button the instant its form
// actually submits closes that window app-wide, with no per-form wiring
// needed. Runs after any inline onsubmit (e.g. a delete confirm()), so
// a cancelled confirm — which calls preventDefault() — never disables
// the button for a submission that didn't happen.
document.addEventListener('submit', function (event) {
    if (event.defaultPrevented || !(event.target instanceof HTMLFormElement)) {
        return;
    }

    event.target.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]').forEach(function (button) {
        button.disabled = true;
    });
});

// navigator.share() only works during the short "user activation" window
// right after a tap — on a slow connection, the PDF (dompdf render + QR
// generation) can take longer than that window to fetch, so by the time
// the fetch finished the browser no longer treats it as a real user
// gesture and silently rejects the share, landing in the catch block
// below ("Could not prepare the PDF"). preloadPdf() kicks the same fetch
// off in the background as soon as the page with the button loads, so by
// the time the user actually taps "Send on WhatsApp" the PDF is usually
// already sitting in memory and the share happens immediately — well
// inside the activation window — instead of only starting the fetch on tap.
const pdfPreloadCache = new Map();

function fetchPdfBlob(url) {
    return fetch(url, { credentials: 'same-origin' }).then(function (response) {
        if (!response.ok) {
            throw new Error('Failed to fetch PDF');
        }
        return response.blob();
    });
}

window.preloadPdf = function (url) {
    if (!pdfPreloadCache.has(url)) {
        pdfPreloadCache.set(url, fetchPdfBlob(url));
    }
};

// Hands the actual PDF file to the phone's native share sheet (WhatsApp,
// email, etc. all appear there) instead of sending a link or plain text.
// Falls back to a normal download if the browser can't share files.
window.sharePdfFile = async function (url, filename, buttonEl) {
    const originalLabel = buttonEl ? buttonEl.textContent : null;

    try {
        if (buttonEl) {
            buttonEl.disabled = true;
            buttonEl.textContent = 'Preparing PDF…';
        }

        if (!pdfPreloadCache.has(url)) {
            window.preloadPdf(url);
        }

        const blob = await pdfPreloadCache.get(url);
        const file = new File([blob], filename, { type: 'application/pdf' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            await navigator.share({ files: [file] });
            return;
        }

        const objectUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = objectUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(objectUrl);

        window.alert('Your browser can\'t attach files directly. The PDF has been downloaded — open WhatsApp and attach it from your Downloads/Files.');
    } catch (err) {
        if (err && err.name === 'AbortError') {
            return; // user closed the share sheet
        }

        // The cached fetch (or its failure) is done with — let a retry
        // attempt a fresh one instead of replaying the same rejection.
        pdfPreloadCache.delete(url);

        window.alert('Could not prepare the PDF. Please try "Download PDF" instead.');
    } finally {
        if (buttonEl) {
            buttonEl.disabled = false;
            buttonEl.textContent = originalLabel;
        }
    }
};

// Hands the actual QR code image (already sitting on the page as a data:
// URI, so no network fetch/preload is needed) to the phone's native share
// sheet — WhatsApp, etc. — the same way PhonePe/BHIM share their QR as an
// image rather than a text link. Falls back to a plain download if the
// browser can't share files.
window.shareImageDataUri = async function (dataUri, filename, buttonEl) {
    const originalLabel = buttonEl ? buttonEl.textContent : null;

    try {
        if (buttonEl) {
            buttonEl.disabled = true;
            buttonEl.textContent = 'Preparing…';
        }

        const blob = await (await fetch(dataUri)).blob();
        const file = new File([blob], filename, { type: blob.type || 'image/png' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            await navigator.share({ files: [file] });
            return;
        }

        const link = document.createElement('a');
        link.href = dataUri;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();

        window.alert('Your browser can\'t attach images directly. The QR code has been downloaded — open WhatsApp and attach it from your Gallery/Downloads.');
    } catch (err) {
        if (err && err.name === 'AbortError') {
            return; // user closed the share sheet
        }

        window.alert('Could not share the QR code. Please try "Download QR" instead.');
    } finally {
        if (buttonEl) {
            buttonEl.disabled = false;
            buttonEl.textContent = originalLabel;
        }
    }
};
