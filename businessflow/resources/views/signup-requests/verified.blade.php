<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Email Confirmed') }} — {{ config('app.name') }}</title>

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var dark = stored !== 'light';
                if (dark) document.documentElement.classList.add('dark');
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center py-10 px-4">
        <div class="w-full max-w-md space-y-5">
            <div class="flex items-center justify-center">
                <x-application-logo base-height="2.5rem" />
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 text-center space-y-2">
                <div class="text-2xl">✅</div>
                <h1 class="text-lg font-semibold">{{ __('Email confirmed!') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Your request has been sent for review. Our team will set up your account shortly.') }}</p>
            </div>

            <div class="text-center text-xs text-gray-400">
                {{ __('Powered by') }} {{ config('app.name') }}
            </div>
        </div>
    </body>
</html>
