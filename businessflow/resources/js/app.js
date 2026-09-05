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

        const response = await fetch(url, { credentials: 'same-origin' });
        if (!response.ok) {
            throw new Error('Failed to fetch PDF');
        }

        const blob = await response.blob();
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

        window.alert('Could not prepare the PDF. Please try "Download PDF" instead.');
    } finally {
        if (buttonEl) {
            buttonEl.disabled = false;
            buttonEl.textContent = originalLabel;
        }
    }
};
