{{-- Auto-opens once per login session (sessionStorage, not localStorage —
     resets on every fresh login, matching "show it the first time they log
     in") when the active plan is within 7 days of its expiry. Doesn't
     re-open on every page navigation within the same session; the bell
     dropdown keeps the same info visible the whole time regardless. --}}
<div x-data x-init="
        $nextTick(() => {
            if (!sessionStorage.getItem('renewal-reminder-shown')) {
                sessionStorage.setItem('renewal-reminder-shown', '1');
                $dispatch('open-modal', 'renewal-reminder');
            }
        });
     "></div>

<x-modal name="renewal-reminder" max-width="sm">
    <div class="p-6 text-center space-y-4">
        <div class="mx-auto h-12 w-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
            <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        </div>

        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ $subscriptionDaysRemaining === 0 ? __('Your plan expires today') : __('Your plan expires in :days day(s)', ['days' => $subscriptionDaysRemaining]) }}
        </h2>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Valid till') }} <span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscriptionExpiresOn->format('d M Y') }}</span>.
            {{ __('Please contact us to renew — access pauses automatically the day after, though all your data stays safe and comes right back once renewed.') }}
        </p>

        <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">
            {{ __('Got it') }}
        </button>
    </div>
</x-modal>
