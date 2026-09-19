{{--
    Paired with resources/js/upload-progress.js — hidden until that JS
    shows it on submit start, then reflects the live upload percentage.
--}}
<div data-upload-progress-wrap hidden>
    <div class="h-1.5 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden mt-2">
        <div data-upload-progress-bar class="h-full bg-accent-600 transition-all" style="width: 0%;"></div>
    </div>
    <span data-upload-progress-label class="text-xs text-gray-400 mt-1 block">{{ __('Uploading… 0%') }}</span>
</div>
