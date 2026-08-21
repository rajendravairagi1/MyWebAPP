<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
                    { key: 'indigo', label: '{{ __('Indigo') }}', swatch: '#6366f1' },
                    { key: 'blue', label: '{{ __('Blue') }}', swatch: '#3b82f6' },
                    { key: 'emerald', label: '{{ __('Emerald') }}', swatch: '#10b981' },
                    { key: 'rose', label: '{{ __('Rose') }}', swatch: '#f43f5e' },
                    { key: 'amber', label: '{{ __('Amber') }}', swatch: '#f59e0b' },
                    { key: 'violet', label: '{{ __('Violet') }}', swatch: '#8b5cf6' },
                    { key: 'teal', label: '{{ __('Teal') }}', swatch: '#14b8a6' },
                ],
             }"
             x-init="
                $watch('dark', value => { document.documentElement.classList.toggle('dark', value); localStorage.setItem('theme', value ? 'dark' : 'light'); window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: value } })); });
                $watch('accent', value => { document.documentElement.setAttribute('data-accent', value); localStorage.setItem('accent', value); });
             "
             class="flex h-screen overflow-hidden">

            {{-- Mobile overlay --}}
            <div x-show="mobileOpen" x-cloak @click="mobileOpen = false"
                 class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-transition.opacity></div>

            {{-- Sidebar --}}
            <aside
                :class="mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed lg:static inset-y-0 left-0 z-40 w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col transition-transform duration-200 ease-in-out">
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

                    <x-sidebar-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 12h.01M9 15h.01M9 18h.01" />
                        </x-slot>
                        {{ __('Projects') }}
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2c1.66 0 3-1.34 3-3s-1.34-3-3-3" />
                        </x-slot>
                        {{ __('Customers') }}
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('quotations.index')" :active="request()->routeIs('quotations.*')">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </x-slot>
                        {{ __('Quotations') }}
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m3 9l-3-2-3 2-3-2-3 2V5a2 2 0 012-2h8a2 2 0 012 2v16z" />
                        </x-slot>
                        {{ __('Invoices') }}
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('followups.index')" :active="request()->routeIs('followups.*')">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </x-slot>
                        {{ __('Follow-ups') }}
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </x-slot>
                        {{ __('Products') }}
                    </x-sidebar-link>
                </nav>

                <div class="border-t border-slate-800 p-3 space-y-1">
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button type="button"
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h10a4 4 0 004-4V9a2 2 0 00-.586-1.414l-4-4A2 2 0 0015 3h-1M7 15h.01" /></svg>
                                <span>{{ __('Theme color') }}</span>
                                <span class="ml-auto h-4 w-4 rounded-full border border-white/20" :style="{ backgroundColor: accents.find(a => a.key === accent)?.swatch }"></span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="grid grid-cols-4 gap-2 p-3">
                                <template x-for="option in accents" :key="option.key">
                                    <button type="button" @click="accent = option.key" :title="option.label"
                                        class="h-8 w-8 rounded-full flex items-center justify-center border-2 transition"
                                        :class="accent === option.key ? 'border-gray-800 dark:border-gray-100' : 'border-transparent'">
                                        <span class="h-6 w-6 rounded-full" :style="{ backgroundColor: option.swatch }"></span>
                                    </button>
                                </template>
                            </div>
                        </x-slot>
                    </x-dropdown>

                    <button @click="dark = !dark" type="button"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition">
                        <svg x-show="!dark" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg x-show="dark" x-cloak class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <span x-text="dark ? '{{ __('Light mode') }}' : '{{ __('Dark mode') }}'"></span>
                    </button>

                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                <span class="h-7 w-7 rounded-full bg-accent-500 text-white text-xs font-semibold flex items-center justify-center shrink-0">
                                    {{ collect(explode(' ', Auth::user()->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                                </span>
                                <span class="truncate">{{ Auth::user()->name }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </aside>

            {{-- Main column --}}
            <div class="flex-1 flex flex-col min-w-0">
                <header class="h-16 shrink-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 flex items-center gap-4 px-4 sm:px-6">
                    <button @click="mobileOpen = true" type="button" class="lg:hidden text-gray-500 dark:text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    @isset($header)
                        <div class="flex-1 min-w-0">{{ $header }}</div>
                    @endisset

                    <x-dropdown align="right" width="80">
                        <x-slot name="trigger">
                            <button type="button" class="relative text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                @if ($dueFollowupsCount > 0)
                                    <span class="absolute -top-1 -right-1 h-4 min-w-[1rem] px-1 rounded-full bg-red-600 text-white text-[10px] leading-4 text-center font-semibold">{{ $dueFollowupsCount }}</span>
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
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
                        </x-slot>
                    </x-dropdown>
                </header>

                <main class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
