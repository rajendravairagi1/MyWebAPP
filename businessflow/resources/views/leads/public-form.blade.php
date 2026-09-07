<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Contact') }} {{ $business->name }}</title>

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) document.documentElement.classList.add('dark');
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center py-10 px-4">
        <div class="w-full max-w-md space-y-5">
            <div class="flex items-center gap-3 justify-center text-center flex-col">
                @if ($business->logoDataUri())
                    <img src="{{ $business->logoDataUri() }}" alt="{{ $business->name }}" class="h-12">
                @endif
                <div class="font-semibold text-lg tracking-tight">{{ $business->name }}</div>
            </div>

            @if (session('leadSubmitted'))
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 text-center space-y-2">
                    <div class="text-2xl">✅</div>
                    <h1 class="text-lg font-semibold">{{ __('Thank you!') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Your details have been sent. Our team will contact you shortly.') }}</p>
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 space-y-4">
                    <div class="text-center">
                        <h1 class="text-lg font-semibold">{{ __('Share your details') }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __("We'll get in touch with you shortly.") }}</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ $formActionUrl }}" class="space-y-4">
                        @csrf

                        {{-- Honeypot — hidden with inline styles (never depends on the CSS build), off the tab
                             order, and named/id'd away from anything browser autofill recognises (e.g. "website"
                             or "url" can get silently filled in by saved-address autofill even off-screen). A
                             genuine visitor never sees or fills this. --}}
                        <div style="position:absolute; left:-9999px; top:-9999px; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                            <label for="hp_check_1">Leave blank</label>
                            <input type="text" id="hp_check_1" name="hp_check_1" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Your Name') }}</label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}" autofocus
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Phone Number') }}</label>
                            <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Anything specific you\'re looking for? (optional)') }}</label>
                            <textarea id="message" name="message" rows="2"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('message') }}</textarea>
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
