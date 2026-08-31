<x-guest-layout>
    <div class="text-center space-y-4">
        <div class="mx-auto h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        </div>

        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Subscription expired') }}</h2>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            @if ($business)
                {{ __('Access to') }} <span class="font-medium text-gray-800 dark:text-gray-100">{{ $business->name }}</span>
                @if ($expiredOn)
                    {{ __('was valid through') }} {{ $expiredOn->format('d M Y') }}{{ __(' and has now paused.') }}
                @else
                    {{ __('has been paused.') }}
                @endif
            @else
                {{ __('This account is currently paused.') }}
            @endif
        </p>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('All your data is safe and untouched — it comes right back the moment your renewal is confirmed. Please contact us to renew.') }}
        </p>

        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button class="text-sm text-accent-600 hover:underline">{{ __('Log out') }}</button>
        </form>
    </div>
</x-guest-layout>
