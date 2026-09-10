<!DOCTYPE html>
{{--
    Always dark — the login/auth pages intentionally ignore the
    light/dark preference used elsewhere in the app (localStorage
    "theme") and always render in the same dark palette as the
    probuildercrm.com marketing site, so the branded feel carries
    through the Login button click without a flash of a different theme.
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @include('partials.pwa-head')
        @include('partials.brand-favicon-links')
        @include('partials.brand-logo-sync')

        <script>
            (function () {
                var accent = localStorage.getItem('accent');
                if (accent) document.documentElement.setAttribute('data-accent', accent);
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#f1f5f9] antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#0a0f1c]">
            <div>
                <a href="/">
                    <x-application-logo base-height="4rem" class="fill-current text-accent-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-[#131b2e] border border-white/10 shadow-md overflow-hidden sm:rounded-lg space-y-4">
                @include('partials.install-button')

                {{ $slot }}
            </div>

            <div class="w-full sm:max-w-md mt-4 px-6 text-center">
                @include('partials.footer')
            </div>
        </div>
    </body>
</html>
