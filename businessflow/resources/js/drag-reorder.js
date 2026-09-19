/**
 * Drag-and-drop reordering for a media grid (unit/property-deal photos,
 * layouts, documents) — every draggable item just needs to sit inside a
 * `[data-reorder-container]` with a `data-reorder-url` and
 * `data-reorder-type`, carrying its own database id as `data-id`.
 *
 * Reordering is optimistic: the DOM move happens immediately on drop (so
 * dragging always feels instant), then the new order is POSTed in the
 * background. A save failure is logged rather than surfaced — losing a
 * drag-reorder to a flaky connection isn't worth interrupting the
 * builder with an error for something this low-stakes, and the grid
 * still reflects what they just did locally either way.
 */
export function initDragReorder() {
    document.querySelectorAll('[data-reorder-container]').forEach((container) => {
        if (container.dataset.reorderBound === '1') {
            return;
        }
        container.dataset.reorderBound = '1';

        const url = container.dataset.reorderUrl;
        const type = container.dataset.reorderType;
        let dragEl = null;

        container.querySelectorAll('[data-reorder-item]').forEach((item) => bindItem(item));

        function bindItem(item) {
            item.setAttribute('draggable', 'true');
            item.classList.add('cursor-grab');

            item.addEventListener('dragstart', () => {
                dragEl = item;
                setTimeout(() => item.classList.add('opacity-40'), 0);
            });

            item.addEventListener('dragend', () => {
                item.classList.remove('opacity-40');
                dragEl = null;
                save();
            });

            item.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!dragEl || dragEl === item) {
                    return;
                }
                const rect = item.getBoundingClientRect();
                const isBefore = (event.clientX - rect.left) < rect.width / 2;
                item.parentNode.insertBefore(dragEl, isBefore ? item : item.nextSibling);
            });
        }

        function save() {
            const ids = Array.from(container.querySelectorAll('[data-reorder-item]')).map((el) => el.dataset.id);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ type, ids }),
            }).catch(() => {
                // Best-effort - see file docblock.
            });
        }
    });
}
