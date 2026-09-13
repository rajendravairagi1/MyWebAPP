{{--
    5-minute inactivity auto-logout. A warning modal appears after 4
    minutes with no mouse/keyboard/scroll activity, giving a 60-second
    countdown to click "Stay Logged In" before the session is ended -
    matching how a banking app behaves, so nobody's session stays open
    unattended on a shared/site-office computer.

    The countdown you see here is just the on-screen display; the actual
    logout is driven independently by the plain window-level timers
    below (scheduled together, reset together on any activity) so it
    still fires at 5 minutes even if this modal never opened correctly.
--}}
<x-modal name="idle-warning" maxWidth="sm">
    <div class="p-6 text-center" x-data="{ seconds: 60 }" x-init="
        let iv = setInterval(() => {
            seconds = Math.max(0, seconds - 1);
            if (seconds === 0) clearInterval(iv);
        }, 1000);
        $watch('show', value => {
            if (value) { seconds = 60; }
            else { clearInterval(iv); }
        });
    ">
        <svg class="mx-auto h-10 w-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-3">{{ __('Still there?') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            {{ __("You've been inactive for a while. For your security, you'll be logged out in") }}
            <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="seconds"></span>
            {{ __('seconds.') }}
        </p>
        <div class="mt-5">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'idle-warning'); window.dispatchEvent(new Event('idle-stay-logged-in'))"
                class="w-full inline-flex items-center justify-center px-4 py-2 bg-accent-600 text-white text-sm font-semibold rounded-md hover:bg-accent-700"
            >
                {{ __('Stay Logged In') }}
            </button>
        </div>
    </div>
</x-modal>

<form id="idle-logout-form" method="POST" action="{{ route('idle-logout') }}" class="hidden" aria-hidden="true">
    @csrf
</form>

<script>
    (function () {
        var WARN_AFTER_MS = 4 * 60 * 1000;
        var LOGOUT_AFTER_MS = 5 * 60 * 1000;
        var warnTimer = null;
        var logoutTimer = null;
        var lastReset = 0;

        function scheduleTimers() {
            clearTimeout(warnTimer);
            clearTimeout(logoutTimer);
            warnTimer = setTimeout(function () {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'idle-warning' }));
            }, WARN_AFTER_MS);
            logoutTimer = setTimeout(function () {
                var form = document.getElementById('idle-logout-form');
                if (form) form.submit();
            }, LOGOUT_AFTER_MS);
        }

        function onActivity() {
            // A stream of mousemove/scroll events firing every few
            // milliseconds shouldn't each cancel + reschedule both
            // timers - once a second is already far more often than
            // needed to detect "still active".
            var now = Date.now();
            if (now - lastReset < 1000) return;
            lastReset = now;
            scheduleTimers();
        }

        ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'].forEach(function (evt) {
            window.addEventListener(evt, onActivity, { passive: true });
        });

        window.addEventListener('idle-stay-logged-in', scheduleTimers);

        scheduleTimers();
    })();
</script>
