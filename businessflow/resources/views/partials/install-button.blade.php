{{-- Explicit "Install App" button — the browser's own install prompt is
     tucked away in a menu most people never find, so this listens for
     `beforeinstallprompt`, holds onto the event, and fires it directly
     when clicked. Only becomes visible once the browser actually offers
     it (hidden on iOS Safari, which never fires this event, and hidden
     again once installed). --}}
<div
    x-data="{
        deferredPrompt: null,
        installable: false,
    }"
    x-init="
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installable = true;
        });
        window.addEventListener('appinstalled', () => { installable = false; deferredPrompt = null; });
        if (window.matchMedia('(display-mode: standalone)').matches) { installable = false; }
    "
    x-show="installable"
    x-cloak
    class="w-full"
>
    <button type="button"
        @click="deferredPrompt.prompt(); deferredPrompt.userChoice.then(() => { deferredPrompt = null; installable = false; })"
        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-700 dark:bg-slate-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-slate-600 transition">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
        </svg>
        {{ __('Install App (Home Screen Shortcut)') }}
    </button>
</div>
