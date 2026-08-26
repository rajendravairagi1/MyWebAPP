<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BusinessFlow') }} — Real Estate Builder CRM</title>

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) document.documentElement.classList.add('dark');
                var accent = localStorage.getItem('accent');
                if (accent) document.documentElement.setAttribute('data-accent', accent);
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100">

        <header class="border-b border-gray-100 dark:border-slate-800">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-application-logo class="h-7 w-7 fill-current text-accent-600" />
                    <span class="font-semibold tracking-tight">{{ config('app.name', 'BusinessFlow') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-accent-600 hover:underline">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">{{ __('Login') }}</a>
                        <a href="{{ route('demo.login') }}" class="inline-flex items-center h-9 px-4 rounded-lg text-sm font-semibold bg-accent-600 text-white hover:bg-accent-700">{{ __('See Demo') }}</a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Hero --}}
        <section class="max-w-4xl mx-auto px-6 py-20 text-center">
            <h1 class="text-3xl sm:text-5xl font-bold tracking-tight">{{ __('Run your builder business from one place') }}</h1>
            <p class="mt-5 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                {{ __('Projects, units, customers, payments, quotations, invoices and ledger — built for real estate builders in India. Add your team with exactly the access they need.') }}
            </p>
            <div class="mt-8 flex items-center justify-center gap-3">
                <a href="{{ route('demo.login') }}" class="inline-flex items-center h-11 px-6 rounded-lg text-sm font-semibold bg-accent-600 text-white hover:bg-accent-700">{{ __('See Live Demo') }}</a>
                <a href="#pricing" class="inline-flex items-center h-11 px-6 rounded-lg text-sm font-semibold border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800">{{ __('See Pricing') }}</a>
            </div>
        </section>

        {{-- Features strip --}}
        <section class="max-w-5xl mx-auto px-6 pb-16">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Projects') }}</div>
                    <div class="mt-1">{{ __('Units, status, material log') }}</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Customers') }}</div>
                    <div class="mt-1">{{ __('Payments, follow-ups, statements') }}</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Quotations & Invoices') }}</div>
                    <div class="mt-1">{{ __('Branded PDFs, one click away') }}</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Ledger & Investors') }}</div>
                    <div class="mt-1">{{ __('Know your profit, always') }}</div>
                </div>
            </div>
        </section>

        {{-- Pricing --}}
        <section id="pricing" class="bg-gray-50 dark:bg-slate-800/40 py-20">
            <div class="max-w-5xl mx-auto px-6">
                <h2 class="text-2xl sm:text-3xl font-bold text-center">{{ __('Simple, per-builder pricing') }}</h2>
                <p class="mt-3 text-center text-gray-600 dark:text-gray-400">{{ __('Start where you are, upgrade when you grow.') }}</p>

                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Solo --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-8 flex flex-col">
                        <div class="font-semibold text-lg">{{ __('Single Builder') }}</div>
                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-4xl font-bold">₹1,000</span>
                            <span class="text-gray-400 text-sm">/{{ __('month') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('For a single builder running solo.') }}</p>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400 flex-1">
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Projects, units & material log') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Customers, payments & follow-ups') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Quotations, invoices, ledger') }}</li>
                            <li class="flex gap-2 text-gray-300 dark:text-slate-600"><span>—</span> {{ __('Team members / roles') }}</li>
                            <li class="flex gap-2 text-gray-300 dark:text-slate-600"><span>—</span> {{ __('Multiple branches') }}</li>
                        </ul>
                        <a href="{{ route('demo.login') }}" class="mt-8 inline-flex items-center justify-center h-10 rounded-lg text-sm font-semibold border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Try the Demo') }}</a>
                    </div>

                    {{-- Team --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border-2 border-accent-500 p-8 flex flex-col relative">
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-xs font-semibold px-3 py-1 rounded-full bg-accent-600 text-white">{{ __('Most Popular') }}</span>
                        <div class="font-semibold text-lg">{{ __('Builder + Team') }}</div>
                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-4xl font-bold">₹1,500</span>
                            <span class="text-gray-400 text-sm">/{{ __('month') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Add your staff — agents, accountant, site supervisor — each with exactly the access they need.') }}</p>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400 flex-1">
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Everything in Single Builder') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Unlimited team members') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Role presets: Agent, Accountant, Supervisor...') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Hide money details per role') }}</li>
                            <li class="flex gap-2 text-gray-300 dark:text-slate-600"><span>—</span> {{ __('Multiple branches') }}</li>
                        </ul>
                        <a href="{{ route('demo.login') }}" class="mt-8 inline-flex items-center justify-center h-10 rounded-lg text-sm font-semibold bg-accent-600 text-white hover:bg-accent-700">{{ __('Try the Demo') }}</a>
                    </div>

                    {{-- Company --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-8 flex flex-col">
                        <div class="font-semibold text-lg">{{ __('Company (Multi-Branch)') }}</div>
                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-4xl font-bold">₹3,000</span>
                            <span class="text-gray-400 text-sm">/{{ __('month') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('For a company running multiple branches, each with its own builders and teams.') }}</p>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400 flex-1">
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Everything in Builder + Team') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Unlimited branches') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Unlimited builders per branch') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Branch Managers with full local control') }}</li>
                            <li class="flex gap-2"><span class="text-green-600">✓</span> {{ __('Combined company-wide reporting') }}</li>
                        </ul>
                        <a href="{{ route('demo.login') }}" class="mt-8 inline-flex items-center justify-center h-10 rounded-lg text-sm font-semibold border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Try the Demo') }}</a>
                    </div>
                </div>

                <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Payment by UPI / bank transfer — contact us to get started, we\'ll set up your login.') }}</p>
            </div>
        </section>

        <footer class="py-10 text-center text-xs text-gray-400">
            {{ __('© :year :name — Built for real estate builders in India.', ['year' => date('Y'), 'name' => config('app.name', 'BusinessFlow')]) }}
        </footer>
    </body>
</html>
