@php
    $themes = [
        'indigo' => '#4f46e5', 'blue' => '#2563eb', 'violet' => '#7c3aed',
        'pink' => '#db2777', 'rose' => '#e11d48', 'red' => '#dc2626',
        'orange' => '#ea580c', 'amber' => '#d97706', 'green' => '#16a34a',
        'teal' => '#0d9488',
    ];
@endphp

<header class="bg-white border-b border-gray-100">
    <div class="flex items-center justify-between px-4 sm:px-6 py-3">
        <div class="min-w-0">
            @isset($header)
                {{ $header }}
            @endisset
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Color Theme Picker -->
            <div x-data="{ open: false, active: localStorage.getItem('erp-theme') || 'indigo' }"
                 @click.outside="open = false"
                 @erp-theme-changed.window="active = $event.detail"
                 class="relative">
                <button type="button" @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h14a2 2 0 012 2v7a4 4 0 01-4 4H7z" />
                        <circle cx="8" cy="9" r="1" fill="currentColor" />
                        <circle cx="12" cy="9" r="1" fill="currentColor" />
                        <circle cx="16" cy="9" r="1" fill="currentColor" />
                    </svg>
                    <span class="text-xs font-medium hidden sm:inline">Theme</span>
                </button>

                <div x-show="open" x-cloak x-transition
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 p-3 z-50">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Color Theme</p>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach ($themes as $key => $hex)
                            <button type="button"
                                    @click="window.setErpTheme('{{ $key }}'); open = false"
                                    class="h-8 w-8 rounded-full border-2 flex items-center justify-center"
                                    :class="active === '{{ $key }}' ? 'border-gray-800' : 'border-transparent'"
                                    style="background-color: {{ $hex }}"
                                    title="{{ ucfirst($key) }}">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
