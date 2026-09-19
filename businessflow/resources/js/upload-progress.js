/**
 * Shows a live upload percentage while a `<form data-upload-progress>`
 * with a file input submits, using XMLHttpRequest (which exposes
 * upload.progress events a plain form submit doesn't) instead of a
 * normal submit. Needs, inside the form:
 *   [data-upload-progress-wrap]  - hidden until upload starts
 *   [data-upload-progress-bar]   - width set to the current percentage
 *   [data-upload-progress-label] - text set to "Uploading… N%"
 *
 * When the upload finishes, the browser is sent to wherever the server
 * would normally have redirected — response/status flash messages stay
 * identical to a normal form submit, this only adds progress feedback
 * in between. A large photo/document upload with nothing here would
 * otherwise look frozen for however long it takes to actually transfer.
 */
export function initUploadProgress() {
    document.querySelectorAll('form[data-upload-progress]').forEach((form) => {
        if (form.dataset.uploadProgressBound === '1') {
            return;
        }
        form.dataset.uploadProgressBound = '1';

        const wrap = form.querySelector('[data-upload-progress-wrap]');
        const bar = form.querySelector('[data-upload-progress-bar]');
        const label = form.querySelector('[data-upload-progress-label]');
        if (!wrap || !bar || !label) {
            return;
        }

        form.addEventListener('submit', (event) => {
            const hasFiles = Array.from(form.querySelectorAll('input[type="file"]'))
                .some((input) => input.files && input.files.length);

            if (!hasFiles) {
                return;
            }

            event.preventDefault();

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }

            wrap.hidden = false;
            bar.style.width = '0%';
            label.textContent = 'Uploading… 0%';

            const xhr = new XMLHttpRequest();
            xhr.open(form.method || 'POST', form.action, true);

            xhr.upload.addEventListener('progress', (evt) => {
                if (!evt.lengthComputable) {
                    return;
                }
                const pct = Math.round((evt.loaded / evt.total) * 100);
                bar.style.width = pct + '%';
                label.textContent = `Uploading… ${pct}%`;
            });

            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 400) {
                    bar.style.width = '100%';
                    // XHR already followed the server's redirect, so
                    // xhr.responseText is the final page — including the
                    // one-time flash message. Render it directly instead
                    // of navigating again, since a second navigation
                    // would hit the page fresh and miss that flash
                    // (Laravel only keeps it for a single request).
                    if (xhr.responseURL) {
                        history.replaceState(null, '', xhr.responseURL);
                    }
                    document.open();
                    document.write(xhr.responseText);
                    document.close();
                } else {
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                    label.textContent = 'Something went wrong — please try again.';
                }
            };

            xhr.onerror = () => {
                if (submitButton) {
                    submitButton.disabled = false;
                }
                label.textContent = 'Upload failed — check your connection and try again.';
            };

            xhr.send(new FormData(form));
        });
    });
}
