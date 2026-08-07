@php
    $themes = [
        'teal' => ['Teal', 'linear-gradient(135deg, #0f766e, #06b6d4)'],
        'blue' => ['Blue', 'linear-gradient(135deg, #2563eb, #4f46e5)'],
        'lightblue' => ['Sky', 'linear-gradient(135deg, #0ea5e9, #22d3ee)'],
        'green' => ['Green', 'linear-gradient(135deg, #059669, #84cc16)'],
        'purple' => ['Purple', 'linear-gradient(135deg, #7c3aed, #ec4899)'],
        'orange' => ['Orange', 'linear-gradient(135deg, #ea580c, #f43f5e)'],
        'black' => ['Midnight', 'linear-gradient(135deg, #6366f1, #a855f7)'],
        'darkpro' => ['Dark Pro', 'linear-gradient(135deg, #5eead4, #22d3ee)'],
    ];
@endphp

<header class="bg-[var(--card)] border-b border-[var(--border)]">
    <div class="flex items-center justify-between px-4 sm:px-6 py-3">
        <div class="min-w-0">
            @isset($header)
                {{ $header }}
            @endisset
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Color Theme Picker -->
            <div x-data="{ open: false, active: localStorage.getItem('erp-theme') || 'teal' }"
                 @click.outside="open = false"
                 @erp-theme-changed.window="active = $event.detail"
                 class="relative">
                <button type="button" @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-[var(--border)] text-[var(--muted)] hover:bg-[var(--bg)]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h14a2 2 0 012 2v7a4 4 0 01-4 4H7z" />
                        <circle cx="8" cy="9" r="1" fill="currentColor" />
                        <circle cx="12" cy="9" r="1" fill="currentColor" />
                        <circle cx="16" cy="9" r="1" fill="currentColor" />
                    </svg>
                    <span class="text-xs font-medium hidden sm:inline">Theme</span>
                </button>

                <div x-show="open" x-cloak x-transition
                     class="absolute right-0 mt-2 w-60 bg-[var(--card)] rounded-lg shadow-lg ring-1 ring-[var(--border)] p-3 z-50">
                    <p class="text-xs font-semibold text-[var(--muted)] uppercase tracking-wide mb-2">Color Theme</p>
                    <div class="grid grid-cols-4 gap-2.5">
                        @foreach ($themes as $key => [$label, $grad])
                            <button type="button"
                                    @click="window.setErpTheme('{{ $key }}'); open = false"
                                    class="h-9 w-9 rounded-full border-2 flex items-center justify-center transition"
                                    :class="active === '{{ $key }}' ? 'border-[var(--text)] scale-110' : 'border-transparent'"
                                    style="background-image: {{ $grad }}"
                                    title="{{ $label }}">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-[var(--muted)] bg-[var(--card)] hover:text-[var(--text)] focus:outline-none transition ease-in-out duration-150">
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
