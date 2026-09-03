<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} — {{ config('app.name', 'Laravel') }}</title>

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) document.documentElement.classList.add('dark');
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @php
            $manifest = is_file(base_path('build/manifest.json')) ? base_path('build/manifest.json') : public_path('build/manifest.json');
        @endphp
        @if (is_file($manifest))
            @vite(['resources/css/app.css'])
        @endif
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-100 dark:bg-slate-900">
        <div class="min-h-screen flex flex-col justify-center items-center px-6 text-center">
            <div class="text-5xl font-bold text-accent-600 dark:text-accent-400 mb-2">{{ $code }}</div>
            <h1 class="text-xl font-semibold mb-2">{{ $title }}</h1>
            <p class="text-gray-500 dark:text-slate-400 max-w-sm mb-6">{{ $message }}</p>
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-accent-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition">
                {{ __('Go to Dashboard') }}
            </a>
        </div>
    </body>
</html>
