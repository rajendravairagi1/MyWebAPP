<x-guest-layout>
    {{--
        Mirrors the same toggle on the signup page (both pages are
        reachable directly — this one is also the Android app's
        start_url for anyone already logged out there) so either one is
        one click from the other, instead of only signup -> login
        working via the plain text link further down. Sign Up stays on
        the left and Login on the right on BOTH pages - only which one
        is highlighted changes - so clicking between them never makes
        the tabs swap places.
    --}}
    <div class="grid grid-cols-2 gap-1 p-1 bg-white/5 rounded-lg text-sm font-medium mb-4">
        <a href="{{ route('signup-requests.public.show') }}" class="rounded-md py-2 text-center text-gray-400 hover:text-gray-200">{{ __('Sign Up') }}</a>
        <span class="rounded-md py-2 text-center bg-white/10 text-white">{{ __('Login') }}</span>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-accent-600 shadow-sm focus:ring-accent-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        {{ __("New here?") }}
        <a href="{{ route('signup-requests.public.show') }}" class="font-medium text-accent-600 hover:underline">{{ __('Request your account') }}</a>
    </p>
</x-guest-layout>
