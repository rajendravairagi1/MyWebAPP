<x-guest-layout>
    <div class="text-center space-y-4">
        <div class="mx-auto h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
        </div>

        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Mobile access only') }}</h2>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Access to') }} <span class="font-medium text-gray-800 dark:text-gray-100">{{ $business?->name ?? __('this account') }}</span>
            {{ __('is set to mobile only — it can\'t be opened from a desktop or laptop browser.') }}
        </p>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Your data is safe either way — nothing is lost or hidden, this account is just set to open from a phone or tablet only.') }}
        </p>

        <p class="text-xs text-gray-500 dark:text-gray-500">
            {{ __('Open this on your Android or iPhone to continue — either the app or the mobile browser works.') }}
        </p>

        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button class="text-sm text-accent-600 hover:underline">{{ __('Log out') }}</button>
        </form>
    </div>
</x-guest-layout>
