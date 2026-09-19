{{--
    One shared modal for every data-crop file input on the page (logo,
    profile photo, unit photos) — see resources/js/crop-field.js. Only
    needs including once; it's inert until a crop-enabled input's change
    event opens it.
--}}
<div id="crop-field-modal" class="hidden fixed inset-0 z-[60] items-center justify-center bg-black/70 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                {{ __('Crop image') }} <span id="crop-field-counter" class="text-gray-400 font-normal"></span>
            </h3>
            <button type="button" id="crop-field-cancel" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xl leading-none">&times;</button>
        </div>

        <div class="p-4">
            <div style="max-height: 55vh; overflow: hidden;">
                <img id="crop-field-image" style="max-width: 100%; display: block;">
            </div>
        </div>

        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700 flex items-center justify-between gap-2">
            <button type="button" id="crop-field-rotate" class="px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600 rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">
                {{ __('Rotate') }}
            </button>

            <div class="flex items-center gap-3">
                <button type="button" id="crop-field-skip" class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:underline">
                    {{ __('Use original') }}
                </button>
                <button type="button" id="crop-field-use" class="px-4 py-1.5 bg-accent-600 text-white text-sm font-semibold rounded-md hover:bg-accent-700">
                    {{ __('Crop & Use') }}
                </button>
            </div>
        </div>
    </div>
</div>
