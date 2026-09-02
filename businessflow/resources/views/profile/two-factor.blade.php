<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Two-Factor Authentication') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            @if ($recoveryCodes)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 space-y-2">
                    <div class="text-sm font-semibold text-amber-800 dark:text-amber-400">{{ __('Save these recovery codes — each one works once, and this is the only time they\'re shown.') }}</div>
                    <div class="grid grid-cols-2 gap-2 font-mono text-sm text-gray-800 dark:text-gray-100">
                        @foreach ($recoveryCodes as $code)
                            <div class="bg-white dark:bg-slate-800 rounded px-2 py-1 border border-amber-100 dark:border-amber-900">{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 space-y-4">
                @if ($user->hasEnabledTwoFactor())
                    <div class="flex items-center gap-2 text-green-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="font-medium">{{ __('Two-factor authentication is enabled.') }}</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('A code from your authenticator app is required every time you log in, in addition to your password.') }}</p>

                    <div class="flex items-center gap-3 pt-2">
                        <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
                            @csrf
                            <x-secondary-button>{{ __('Generate New Recovery Codes') }}</x-secondary-button>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('two-factor.destroy') }}" class="pt-4 border-t border-gray-100 dark:border-slate-700 space-y-3">
                        @csrf
                        @method('DELETE')
                        <x-input-label for="password" :value="__('Enter your password to disable 2FA')" />
                        <x-text-input id="password" type="password" name="password" class="block w-full" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        <button class="text-sm text-red-600 hover:underline">{{ __('Disable Two-Factor Authentication') }}</button>
                    </form>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Scan this QR code with Google Authenticator (or any TOTP app), then enter the 6-digit code it shows to turn on two-factor authentication.') }}</p>

                    @if ($qrCodeUri)
                        <div class="flex justify-center">
                            <img src="{{ $qrCodeUri }}" alt="{{ __('Scan with Google Authenticator') }}" class="rounded-lg border border-gray-200 dark:border-slate-700">
                        </div>
                    @endif

                    <p class="text-xs text-gray-400 text-center break-all">{{ __('Or enter this key manually') }}: <span class="font-mono">{{ $secret }}</span></p>

                    <form method="POST" action="{{ route('two-factor.store') }}" class="space-y-3 pt-2">
                        @csrf
                        <x-input-label for="code" :value="__('6-digit code')" />
                        <x-text-input id="code" type="text" inputmode="numeric" name="code" class="block w-full" required autofocus placeholder="123456" />
                        <x-input-error :messages="$errors->get('code')" class="mt-1" />
                        <x-primary-button>{{ __('Enable Two-Factor Authentication') }}</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
