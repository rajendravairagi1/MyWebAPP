<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Color theme: applied before paint to avoid a flash of the default color -->
        <script>
            (function () {
                var saved = localStorage.getItem('erp-theme');
                if (saved) document.documentElement.setAttribute('data-theme', saved);
            })();
            window.setErpTheme = function (theme) {
                localStorage.setItem('erp-theme', theme);
                document.documentElement.setAttribute('data-theme', theme);
            };
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
            @include('layouts.sidebar')

            <div class="flex-1 min-w-0">
                @include('layouts.topbar')

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
