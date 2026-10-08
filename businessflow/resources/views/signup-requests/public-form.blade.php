<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Get Started') }} — {{ config('app.name') }}</title>

        {{--
            Deliberately no dark-mode script here (the main app's other
            pages read a `theme` value from localStorage and toggle a
            `dark` class) — this page is the very first thing a brand-new,
            not-yet-logged-in visitor sees, so there's no stored preference
            of theirs to read yet, and one could leak in from a previous
            visit to a different page on this same browser. That made this
            page's look depend on whatever the visitor's browser happened
            to have stored, drifting out of sync with the fixed, always-
            dark look of the probuildercrm.com marketing site (and of the
            brand logo below, which is a light-colored mark made for a dark
            background — it goes near-invisible on anything lighter). This
            page is hardcoded dark, in every case, for every visitor.
        --}}

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-900 text-gray-100 min-h-screen flex items-center justify-center py-10 px-4">
        <div class="w-full max-w-md space-y-5">
            <div class="flex items-center justify-center">
                <x-application-logo base-height="2.5rem" />
            </div>

            {{--
                A brand-new visitor and a returning one land on this exact
                same URL (it's the Android app's start_url — see
                PwaController::manifest()), so this has to work for both:
                Sign Up stays highlighted since this page IS the signup
                form, Login just goes to the login page directly — no
                in-page form-swapping needed since that page already has
                its own "New here? Request your account" link back here.
            --}}
            <div class="grid grid-cols-2 gap-1 p-1 bg-slate-800 rounded-lg text-sm font-medium">
                <span class="rounded-md py-2 text-center bg-slate-700 text-white">{{ __('Sign Up') }}</span>
                <a href="{{ route('login') }}" class="rounded-md py-2 text-center text-gray-400 hover:text-gray-200">{{ __('Login') }}</a>
            </div>

            @if (session('requestSubmitted'))
                <div class="bg-slate-800 shadow-sm rounded-xl p-6 text-center space-y-2">
                    <div class="text-2xl">📧</div>
                    <h1 class="text-lg font-semibold">{{ __('Check your email') }}</h1>
                    <p class="text-sm text-gray-400">{{ __("We've sent a confirmation link to your email address. Click it to confirm and get started.") }}</p>
                </div>
            @else
                <div class="bg-slate-800 shadow-sm rounded-xl p-6 space-y-4">
                    <div class="text-center">
                        <h1 class="text-lg font-semibold">{{ __('Get Your Account') }}</h1>
                        <p class="text-sm text-gray-400 mt-1">{{ __('Fill in your details below to get started.') }}</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-900/30 border border-red-800 text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('signup-requests.public.store') }}" class="space-y-4">
                        @csrf

                        {{-- Honeypot — hidden with inline styles (never depends on the CSS build), off the tab
                             order, and named/id'd away from anything browser autofill recognises. A genuine
                             visitor never sees or fills this. --}}
                        <div style="position:absolute; left:-9999px; top:-9999px; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                            <label for="hp_check_1">Leave blank</label>
                            <input type="text" id="hp_check_1" name="hp_check_1" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300">{{ __('Username') }}</label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}" autofocus
                                class="mt-1 block w-full border-slate-600 bg-slate-700 text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300">{{ __('Phone Number') }}</label>
                            <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                                class="mt-1 block w-full border-slate-600 bg-slate-700 text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300">{{ __('Email') }}</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                class="mt-1 block w-full border-slate-600 bg-slate-700 text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" required minlength="8"
                                class="mt-1 block w-full border-slate-600 bg-slate-700 text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <p class="text-xs text-gray-400 mt-1">{{ __('At least 8 characters — this is what you\'ll log in with.') }}</p>
                        </div>

                        <div>
                            <label for="plan" class="block text-sm font-medium text-gray-300">{{ __('Plan') }}</label>
                            <select id="plan" name="plan" required
                                class="mt-1 block w-full border-slate-600 bg-slate-700 text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                @foreach (\App\Models\SignupRequest::PLAN_LABELS as $value => $label)
                                    <option value="{{ $value }}" @selected(old('plan', 'team') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-300">{{ __('Address') }}</label>
                            <textarea id="address" name="address" rows="2"
                                class="mt-1 block w-full border-slate-600 bg-slate-700 text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('address') }}</textarea>
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-accent-600 text-white text-sm font-semibold rounded-md hover:bg-accent-700">
                            {{ __('Submit') }}
                        </button>
                    </form>
                </div>
            @endif

            <div class="text-center text-xs text-gray-400">
                {{ __('Powered by') }} {{ config('app.name') }}
            </div>
        </div>
    </body>
</html>
