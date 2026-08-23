import Alpine from 'alpinejs';
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();

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
