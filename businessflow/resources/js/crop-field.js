import Cropper from 'cropperjs';

/**
 * Wires up every `<input type="file" data-crop>` on the page so picking
 * a file (or files) opens a crop step before it's actually attached to
 * the input — the exact same input, form, and server-side handling as
 * before (logo upload, profile photo, unit photo) needed zero changes,
 * since this only ever substitutes what's already in input.files.
 *
 * data-crop-aspect: a ratio like "1" (square) or "4/3" - omit for a free
 * aspect ratio (used for unit photos, where forcing a shape would trim
 * real estate photos in ways nobody asked for).
 * data-crop-round: "1" to preview the crop box as a circle (avatars).
 *
 * Multiple files (data-crop-multi, e.g. the unit photo uploader's
 * `files[]" multiple` input) are cropped one at a time in a queue - a
 * non-image file (a PDF slipped into a mixed-type picker) passes through
 * untouched rather than blocking the rest of the batch.
 */
/**
 * Reads a data-crop-aspect value that's either a plain number ("1") or
 * a "w/h" fraction ("4/3") - a small parser rather than eval() for a
 * value that, while only ever developer-written in a data attribute,
 * has no reason to run arbitrary JS to produce a ratio.
 */
function parseAspectRatio(raw) {
    if (!raw) {
        return null;
    }

    const parts = raw.split('/').map(Number);

    if (parts.length === 2 && parts.every((n) => Number.isFinite(n) && n > 0)) {
        return parts[0] / parts[1];
    }

    return Number.isFinite(parts[0]) && parts[0] > 0 ? parts[0] : null;
}

export function initCropFields() {
    const modal = document.getElementById('crop-field-modal');
    if (!modal) {
        return;
    }

    const imageEl = document.getElementById('crop-field-image');
    const useBtn = document.getElementById('crop-field-use');
    const skipBtn = document.getElementById('crop-field-skip');
    const cancelBtn = document.getElementById('crop-field-cancel');
    const rotateBtn = document.getElementById('crop-field-rotate');
    const counterEl = document.getElementById('crop-field-counter');

    let cropper = null;
    let currentUrl = null;

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        if (currentUrl) {
            URL.revokeObjectURL(currentUrl);
            currentUrl = null;
        }
    }

    function openCropper(file, { aspectRatio, round }) {
        return new Promise((resolve) => {
            currentUrl = URL.createObjectURL(file);
            imageEl.src = currentUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            imageEl.classList.toggle('crop-field-round', !!round);

            // Cropper reads the image's natural size on load, so it has
            // to actually be in the DOM and loaded before constructing -
            // building it inside the <img>'s own load handler (rather
            // than right after setting .src) avoids a zero-size crop box
            // on a slow decode.
            imageEl.onload = () => {
                cropper = new Cropper(imageEl, {
                    aspectRatio: aspectRatio || NaN,
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                    responsive: true,
                });
            };

            useBtn.onclick = () => {
                if (!cropper) {
                    resolve(file);
                    closeModal();
                    return;
                }

                cropper.getCroppedCanvas({ maxWidth: 1600, maxHeight: 1600, imageSmoothingQuality: 'high' }).toBlob((blob) => {
                    closeModal();
                    if (!blob) {
                        resolve(file);
                        return;
                    }
                    const name = file.name.replace(/\.\w+$/, '') + '.jpg';
                    resolve(new File([blob], name, { type: 'image/jpeg' }));
                }, 'image/jpeg', 0.9);
            };

            skipBtn.onclick = () => {
                closeModal();
                resolve(file);
            };

            cancelBtn.onclick = () => {
                closeModal();
                resolve(null);
            };

            rotateBtn.onclick = () => {
                cropper?.rotate(90);
            };
        });
    }

    document.querySelectorAll('input[type="file"][data-crop]').forEach((input) => {
        input.addEventListener('change', function handleChange() {
            // The synthetic change dispatched below (so any other
            // listener on this same input, e.g. an Alpine preview
            // binding, sees the final cropped file) would otherwise
            // re-enter this same handler and try to crop its own output.
            if (input.dataset.cropSkip === '1') {
                delete input.dataset.cropSkip;
                return;
            }

            if (!input.files || !input.files.length) {
                return;
            }

            const aspectRatio = parseAspectRatio(input.dataset.cropAspect);
            const round = input.dataset.cropRound === '1';
            const originalFiles = Array.from(input.files);
            const results = [];
            let index = 0;

            const processNext = () => {
                if (index >= originalFiles.length) {
                    finish();
                    return;
                }

                const file = originalFiles[index];
                index += 1;

                if (!file.type.startsWith('image/')) {
                    results.push(file);
                    processNext();
                    return;
                }

                if (counterEl) {
                    counterEl.textContent = originalFiles.length > 1
                        ? `(${index} / ${originalFiles.length})`
                        : '';
                }

                openCropper(file, { aspectRatio, round }).then((cropped) => {
                    if (cropped) {
                        results.push(cropped);
                    }
                    processNext();
                });
            };

            const finish = () => {
                if (!results.length) {
                    input.value = '';
                    input.dataset.cropSkip = '1';
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                const dataTransfer = new DataTransfer();
                results.forEach((f) => dataTransfer.items.add(f));
                input.dataset.cropSkip = '1';
                input.files = dataTransfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            };

            processNext();
        });
    });
}
