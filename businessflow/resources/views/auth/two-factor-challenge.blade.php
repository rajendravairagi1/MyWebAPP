<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Two-Factor Verification') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" x-data="{ recovery: false }">
            <span x-show="!recovery">{{ __('Enter the 6-digit code from your authenticator app.') }}</span>
            <span x-show="recovery" x-cloak>{{ __('Enter one of your recovery codes.') }}</span>
        </p>
    </div>

    <form method="POST" action="{{ route('two-factor.challenge') }}" x-data="{ recovery: false }">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" autocomplete="one-time-code" name="code" required autofocus x-bind:placeholder="recovery ? '{{ __('XXXX-XXXX') }}' : '123456'" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-3">
            <button type="button" x-on:click="recovery = !recovery" class="text-sm text-accent-600 hover:underline">
                <span x-show="!recovery">{{ __("Can't access your authenticator app? Use a recovery code") }}</span>
                <span x-show="recovery" x-cloak>{{ __('Use your authenticator app instead') }}</span>
            </button>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>{{ __('Verify') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
