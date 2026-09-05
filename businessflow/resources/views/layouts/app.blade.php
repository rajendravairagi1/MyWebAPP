<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @include('partials.pwa-head')

        {{-- Set theme + accent color before first paint to avoid a flash --}}
        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) document.documentElement.classList.add('dark');

                var accent = localStorage.getItem('accent');
                if (accent) document.documentElement.setAttribute('data-accent', accent);
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100">
        <div x-data="{
                mobileOpen: false,
                dark: document.documentElement.classList.contains('dark'),
                accent: document.documentElement.getAttribute('data-accent') || 'indigo',
                accents: [
                    { key: 'indigo', label: '{{ __('Indigo') }}', swatch: '#4f46e5' },
                    { key: 'blue', label: '{{ __('Blue') }}', swatch: '#2563eb' },
                    { key: 'sky', label: '{{ __('Sky') }}', swatch: '#027cbb' },
                    { key: 'teal', label: '{{ __('Teal') }}', swatch: '#0c8379' },
                    { key: 'cyan', label: '{{ __('Cyan') }}', swatch: '#077f9c' },
                    { key: 'emerald', label: '{{ __('Emerald') }}', swatch: '#04855d' },
                    { key: 'green', label: '{{ __('Green') }}', swatch: '#12873d' },
                    { key: 'amber', label: '{{ __('Amber') }}', swatch: '#b36205' },
                    { key: 'orange', label: '{{ __('Orange') }}', swatch: '#cc4d0a' },
                    { key: 'rose', label: '{{ __('Rose') }}', swatch: '#e11d48' },
                    { key: 'fuchsia', label: '{{ __('Fuchsia') }}', swatch: '#c026d3' },
                    { key: 'violet', label: '{{ __('Violet') }}', swatch: '#7c3aed' },
                ],
             }"
             x-init="
                $watch('dark', value => { document.documentElement.classList.toggle('dark', value); localStorage.setItem('theme', value ? 'dark' : 'light'); window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: value } })); });
                $watch('accent', value => { document.documentElement.setAttribute('data-accent', value); localStorage.setItem('accent', value); });
             "
             class="flex min-h-screen">

            {{-- Mobile overlay --}}
            <div x-show="mobileOpen" x-cloak @click="mobileOpen = false"
                 class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-transition.opacity></div>

            {{-- Sidebar --}}
            <aside
                :class="mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed lg:sticky lg:top-0 lg:self-start lg:h-screen inset-y-0 left-0 z-40 w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col transition-transform duration-200 ease-in-out">
                <div class="h-16 flex items-center gap-2 px-5 border-b border-slate-800">
                    <x-application-logo class="h-7 w-7 fill-current text-accent-500" />
                    <span class="font-semibold text-white tracking-tight">{{ config('app.name', 'BusinessFlow') }}</span>
                </div>

                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </x-slot>
                        {{ __('Dashboard') }}
                    </x-sidebar-link>

                    @if ($ownedCompany)
                        <x-sidebar-link :href="route('company.show')" :active="request()->routeIs('company.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </x-slot>
                            {{ __('Company') }}
                        </x-sidebar-link>
                    @elseif ($managedBranch)
                        <x-sidebar-link :href="route('branches.show', $managedBranch)" :active="request()->routeIs('branches.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </x-slot>
                            {{ __('My Branch') }}
                        </x-sidebar-link>
                    @elseif ($canCreateCompany)
                        <x-sidebar-link :href="route('company.create')" :active="request()->routeIs('company.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </x-slot>
                            {{ __('Set Up Company') }}
                        </x-sidebar-link>
                    @endif

                    @if ($isPlatformAdmin)
                        <x-sidebar-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.02-.397-1.11-.94l-.213-1.281c-.063-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </x-slot>
                            {{ __('Platform Admin') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('projects'))
                        <x-sidebar-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 12h.01M9 15h.01M9 18h.01" />
                            </x-slot>
                            {{ __('Projects') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('projects'))
                        <x-sidebar-link :href="route('loans.index')" :active="request()->routeIs('loans.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                            </x-slot>
                            {{ __('Loans') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('customers'))
                        <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2c1.66 0 3-1.34 3-3s-1.34-3-3-3" />
                            </x-slot>
                            {{ __('Customers') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('quotations'))
                        <x-sidebar-link :href="route('quotations.index')" :active="request()->routeIs('quotations.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </x-slot>
                            {{ __('Quotations') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('invoices'))
                        <x-sidebar-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m3 9l-3-2-3 2-3-2-3 2V5a2 2 0 012-2h8a2 2 0 012 2v16z" />
                            </x-slot>
                            {{ __('Invoices') }}
                        </x-sidebar-link>

                        @if ($activeBusiness->payment_reminders_enabled ?? true)
                            <x-sidebar-link :href="route('payment-reminders.index')" :active="request()->routeIs('payment-reminders.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01M9.172 12.172a4 4 0 015.656 0M6.343 8.343a8 8 0 0111.314 0M12 4v.01" />
                                </x-slot>
                                {{ __('Payment Reminders') }}
                            </x-sidebar-link>
                        @endif
                    @endif

                    @if (\App\Support\Tenant::can('followups'))
                        <x-sidebar-link :href="route('followups.index')" :active="request()->routeIs('followups.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </x-slot>
                            {{ __('Follow-ups') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('meetings'))
                        <x-sidebar-link :href="route('meetings.index')" :active="request()->routeIs('meetings.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008z" />
                            </x-slot>
                            {{ __('Meetings') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('available_properties'))
                        <x-sidebar-link :href="route('available-properties.index')" :active="request()->routeIs('available-properties.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </x-slot>
                            {{ __('Available Properties') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('ledger'))
                        <x-sidebar-link :href="route('ledger.index')" :active="request()->routeIs('ledger.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            </x-slot>
                            {{ __('Ledger') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('investors'))
                        <x-sidebar-link :href="route('investors.index')" :active="request()->routeIs('investors.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.306a11.95 11.95 0 015.814-5.518l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </x-slot>
                            {{ __('Investors') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('brokers'))
                        <x-sidebar-link :href="route('brokers.index')" :active="request()->routeIs('brokers.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </x-slot>
                            {{ __('Brokers') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('contractors'))
                        <x-sidebar-link :href="route('contractors.index')" :active="request()->routeIs('contractors.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486l-3.276 3.276a3.004 3.004 0 002.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852z" />
                            </x-slot>
                            {{ __('Contractors / Vendors') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('property_deals'))
                        <x-sidebar-link :href="route('property-deals.index')" :active="request()->routeIs('property-deals.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                            </x-slot>
                            {{ __('Property Deals') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::can('completed_projects'))
                        <x-sidebar-link :href="route('completed-projects.index')" :active="request()->routeIs('completed-projects.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </x-slot>
                            {{ __('Completed Projects') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::isOwner())
                        <x-sidebar-link :href="route('payment-accounts.index')" :active="request()->routeIs('payment-accounts.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </x-slot>
                            {{ __('Payment Accounts') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::isOwner())
                        <x-sidebar-link :href="route('material-credit.index')" :active="request()->routeIs('material-credit.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25M21 7.5v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </x-slot>
                            {{ __('Material Credit') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::isOwner() && \App\Support\Tenant::planAllows('team'))
                        <x-sidebar-link :href="route('team.index')" :active="request()->routeIs('team.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </x-slot>
                            {{ __('Team') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::isOwner())
                        <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </x-slot>
                            {{ __('Reports') }}
                        </x-sidebar-link>
                    @endif

                    @if (\App\Support\Tenant::isOwner())
                        <x-sidebar-link :href="route('backup.index')" :active="request()->routeIs('backup.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </x-slot>
                            {{ __('Backup') }}
                        </x-sidebar-link>
                    @endif
                </nav>
            </aside>

            {{-- Main column --}}
            <div class="flex-1 flex flex-col min-w-0">
                @if ($activeBusinessBranch)
                    <div class="shrink-0 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800 px-4 sm:px-6 py-2 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-sm text-amber-800 dark:text-amber-300 min-w-0">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                            <span class="truncate">
                                {{ __('Branch') }}: <strong>{{ $activeBusinessBranch->name }}</strong>
                                @if ($activeBusinessName) — {{ $activeBusinessName }} @endif
                            </span>
                        </div>
                        {{-- Always back to THIS branch's own dashboard (its
                             real, single-branch numbers) — never the
                             company-wide summary of every branch mixed
                             together, which isn't what "back" means here. --}}
                        <a href="{{ route('branches.show', $activeBusinessBranch) }}" class="shrink-0 inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold whitespace-nowrap bg-amber-600 text-white hover:bg-amber-700">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                            {{ __('Back to :branch Dashboard', ['branch' => $activeBusinessBranch->name]) }}
                        </a>
                    </div>
                @endif

                <header class="min-h-16 shrink-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 flex items-center gap-4 px-4 sm:px-6 py-2">
                    <button @click="mobileOpen = true" type="button" class="lg:hidden text-gray-500 dark:text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    @isset($header)
                        <div class="flex-1 min-w-0">{{ $header }}</div>
                    @endisset

                    <x-global-search />

                    @if ($ownedCompany && $ownedCompanyBranches->isNotEmpty())
                        <x-dropdown align="right" width="w-64">
                            <x-slot name="trigger">
                                <button type="button" class="flex items-center gap-1 text-sm text-gray-600 dark:text-slate-300 hover:text-gray-800 dark:hover:text-slate-100">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                                    <span class="hidden sm:inline">{{ __('Branches') }}</span>
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('company.show')">
                                    <span class="font-medium">{{ __('Company Overview') }}</span>
                                    <span class="block text-xs text-gray-400">{{ __('All branches combined') }}</span>
                                </x-dropdown-link>
                                <div class="border-t border-gray-100 dark:border-slate-700"></div>
                                @foreach ($ownedCompanyBranches as $branch)
                                    <x-dropdown-link :href="route('branches.show', $branch)">
                                        {{ $branch->name }}
                                    </x-dropdown-link>
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    @endif

                    <x-dropdown align="right" width="w-80">
                        <x-slot name="trigger">
                            <button type="button" class="relative text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                @php $bellCount = $dueFollowupsCount + $dueCommitmentsCount + $dueMeetingsCount + ($subscriptionDaysRemaining !== null ? 1 : 0) + $adminRenewalCount; @endphp
                                @if ($bellCount > 0)
                                    <span class="absolute -top-1 -right-1 h-4 min-w-[1rem] px-1 rounded-full {{ ($subscriptionDaysRemaining !== null || $adminRenewalCount > 0) ? 'bg-amber-500' : 'bg-red-600' }} text-white text-[10px] leading-4 text-center font-semibold">{{ $bellCount }}</span>
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @if ($adminRenewalCount > 0)
                                <div class="flex items-center justify-between px-4 py-2 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('Accounts needing renewal') }}</span>
                                    <a href="{{ route('admin.expiring') }}" class="text-xs text-accent-600 hover:underline">{{ __('View all') }}</a>
                                </div>
                                @foreach ($adminRenewalAlerts as $alert)
                                    <div class="flex items-center justify-between gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 border-b border-amber-100 dark:border-amber-900/30">
                                        <a href="{{ route('admin.expiring') }}" class="min-w-0 flex-1">
                                            <div class="text-sm text-gray-800 dark:text-gray-100 font-medium truncate">{{ $alert['name'] }}</div>
                                            <div class="text-xs {{ $alert['expired'] ? 'text-red-600' : 'text-amber-600' }}">
                                                {{ $alert['expired'] ? __('Expired') : __('Expires') }} {{ $alert['expires_at']->format('d M Y') }}
                                            </div>
                                        </a>
                                        <form method="POST" action="{{ route($alert['type'] === 'company' ? 'admin.companies.dismiss-renewal' : 'admin.businesses.dismiss-renewal', $alert['id']) }}" onclick="event.stopPropagation();">
                                            @csrf
                                            <button type="submit" class="shrink-0 text-xs px-2 py-1 rounded border border-gray-300 dark:border-slate-600 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-slate-600" title="{{ __('Mark done — hide until next renewal') }}">
                                                {{ __('Done') }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif

                            @if ($subscriptionDaysRemaining !== null)
                                <div class="px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800">
                                    <div class="text-sm font-semibold text-amber-800 dark:text-amber-400">
                                        {{ $subscriptionDaysRemaining === 0 ? __('Your plan expires today!') : __('Your plan expires in :days day(s)', ['days' => $subscriptionDaysRemaining]) }}
                                    </div>
                                    <div class="text-xs text-amber-700 dark:text-amber-500 mt-0.5">{{ __('Valid till') }} {{ $subscriptionExpiresOn->format('d M Y') }} — {{ __('please contact us to renew and avoid losing access.') }}</div>
                                </div>
                            @endif

                            <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-slate-700">
                                {{ __('Follow-ups due') }}
                            </div>
                            @forelse ($dueFollowupsForBell as $followup)
                                <a href="{{ route('customers.show', $followup->customer) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700">
                                    <div class="text-sm text-gray-800 dark:text-gray-100 font-medium">{{ $followup->customer->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $followup->note }}</div>
                                    <div class="text-xs {{ $followup->due_at->isPast() ? 'text-red-500' : 'text-gray-400' }}">{{ $followup->due_at->diffForHumans() }}</div>
                                </a>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing due right now.') }}</div>
                            @endforelse
                            <a href="{{ route('followups.index') }}" class="block px-4 py-2 text-xs text-center text-accent-600 border-t border-gray-100 dark:border-slate-700 hover:underline">{{ __('View all follow-ups') }}</a>

                            <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 border-t border-b border-gray-100 dark:border-slate-700">
                                {{ __('Possession commitments overdue') }}
                            </div>
                            @forelse ($dueCommitmentsForBell as $commitmentUnit)
                                <a href="{{ route('customers.show', $commitmentUnit->customer) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700">
                                    <div class="text-sm text-gray-800 dark:text-gray-100 font-medium">{{ $commitmentUnit->customer?->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $commitmentUnit->project->name }} · {{ $commitmentUnit->unit_number }}</div>
                                    <div class="text-xs text-red-500">{{ __('Promised') }} {{ $commitmentUnit->commitment_date->diffForHumans() }}</div>
                                </a>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing overdue right now.') }}</div>
                            @endforelse

                            <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 border-t border-b border-gray-100 dark:border-slate-700">
                                {{ __('Meetings today / soon') }}
                            </div>
                            @forelse ($dueMeetingsForBell as $meeting)
                                <a href="{{ route('meetings.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700">
                                    <div class="text-sm text-gray-800 dark:text-gray-100 font-medium">{{ $meeting->title }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $meeting->customer?->name }}{{ $meeting->location ? ' · '.$meeting->location : '' }}</div>
                                    <div class="text-xs {{ $meeting->scheduled_at->isPast() ? 'text-red-500' : 'text-gray-400' }}">{{ $meeting->scheduled_at->format('d M, h:i A') }} · {{ $meeting->scheduled_at->diffForHumans() }}</div>
                                </a>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ __('No meetings coming up.') }}</div>
                            @endforelse
                            @if (\App\Support\Tenant::can('meetings'))
                                <a href="{{ route('meetings.index') }}" class="block px-4 py-2 text-xs text-center text-accent-600 border-t border-gray-100 dark:border-slate-700 hover:underline">{{ __('View all meetings') }}</a>
                            @endif
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="right" width="w-72">
                        <x-slot name="trigger">
                            <button type="button" class="flex items-center gap-2 text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">
                                @if (Auth::user()->photo_path)
                                    <img src="{{ route('profile.photo') }}?v={{ Auth::user()->updated_at->timestamp }}" class="h-9 w-9 rounded-full object-cover shrink-0">
                                @else
                                    <x-avatar-graphic :style="Auth::user()->avatar" :initials="collect(explode(' ', Auth::user()->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')" class="h-9 w-9" />
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 dark:border-slate-700">
                                @if (Auth::user()->photo_path)
                                    <img src="{{ route('profile.photo') }}?v={{ Auth::user()->updated_at->timestamp }}" class="h-10 w-10 rounded-full object-cover shrink-0">
                                @else
                                    <x-avatar-graphic :style="Auth::user()->avatar" :initials="collect(explode(' ', Auth::user()->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')" class="h-10 w-10" />
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ __('Profile Settings') }}
                                </span>
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('business.edit')">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                                    {{ __('Business Settings') }}
                                </span>
                            </x-dropdown-link>

                            <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2">{{ __('Theme color') }}</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="option in accents" :key="option.key">
                                        <button type="button" @click="accent = option.key" :title="option.label"
                                            class="h-7 w-7 rounded-full flex items-center justify-center border-2 transition"
                                            :class="accent === option.key ? 'border-gray-800 dark:border-gray-100' : 'border-transparent'">
                                            <span class="h-5 w-5 rounded-full" :style="{ backgroundColor: option.swatch }"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <button @click="dark = !dark" type="button"
                                class="w-full flex items-center gap-2 px-4 py-2 text-start text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 border-t border-gray-100 dark:border-slate-700 transition">
                                <svg x-show="!dark" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                <svg x-show="dark" x-cloak class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                                <span x-text="dark ? '{{ __('Light mode') }}' : '{{ __('Dark mode') }}'"></span>
                            </button>

                            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 dark:border-slate-700">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3" /></svg>
                                        {{ __('Log Out') }}
                                    </span>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </header>

                <main>
                    {{ $slot }}
                    @include('partials.footer')
                </main>
            </div>
        </div>

        @if ($subscriptionDaysRemaining !== null)
            @include('partials.renewal-modal')
        @endif
    </body>
</html>
