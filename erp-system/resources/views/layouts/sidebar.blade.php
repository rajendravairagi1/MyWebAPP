@php
    $navItem = function (string $route, string $label, string $icon, string $activePattern) {
        $active = request()->routeIs($activePattern);
        return compact('route', 'label', 'icon', 'active');
    };
@endphp

<aside x-data="{ mobileOpen: false }" class="lg:w-64 shrink-0">
    <!-- Mobile toggle -->
    <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="h-7 w-auto fill-current text-gray-800" />
            <span class="font-semibold text-gray-800">ERP</span>
        </a>
        <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-md text-gray-500 hover:bg-gray-100">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <nav :class="{ 'block': mobileOpen, 'hidden': !mobileOpen }"
         class="hidden lg:block lg:sticky lg:top-0 lg:h-screen w-full lg:w-64 bg-white border-r border-gray-100 overflow-y-auto">
        <div class="hidden lg:flex items-center gap-2 px-6 py-5">
            <x-application-logo class="h-8 w-auto fill-current text-gray-800" />
            <span class="font-bold text-lg text-gray-800">Job Work ERP</span>
        </div>

        <div class="px-3 pb-6 space-y-6">
            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Menu</p>
                <div class="space-y-1">
                    @php $active = request()->routeIs('dashboard'); @endphp
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    @can('module.crm-customers')
                        @php $active = request()->routeIs('customers.*'); @endphp
                        <a href="{{ route('customers.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4" />
                            </svg>
                            Customers
                        </a>

                        @php $active = request()->routeIs('products.*'); @endphp
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Products
                        </a>
                    @endcan

                    @canany(['module.job-work', 'module.quality', 'module.security-gate', 'module.invoice-accounts'])
                        @php $active = request()->routeIs('jobs.*'); @endphp
                        <a href="{{ route('jobs.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17V7a2 2 0 012-2h6.5L21 8.5V17a2 2 0 01-2 2H11a2 2 0 01-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7v12a2 2 0 002 2h9" />
                            </svg>
                            Job Work
                        </a>
                    @endcanany

                    @can('module.invoice-accounts')
                        @php $active = request()->routeIs('invoices.*'); @endphp
                        <a href="{{ route('invoices.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l2 2 4-4m3 9H6a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                            </svg>
                            Invoices
                        </a>
                    @endcan

                    @can('module.hr-employees')
                        @php $active = request()->routeIs('employees.*'); @endphp
                        <a href="{{ route('employees.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Employees
                        </a>
                    @endcan

                    @can('module.store-material')
                        @php $active = request()->routeIs('raw-materials.*'); @endphp
                        <a href="{{ route('raw-materials.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7L12 3 4 7m16 0l-8 4m8-4v10l-8 4M4 7l8 4m-8-4v10l8 4m0-10v10" />
                            </svg>
                            Raw Material
                        </a>
                    @endcan
                </div>
            </div>

            @role('Owner')
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Settings</p>
                    <div class="space-y-1">
                        @php $active = request()->routeIs('roles.*'); @endphp
                        <a href="{{ route('roles.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ $active ? 'bg-[var(--brand-50)] text-[var(--brand-700)]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Roles &amp; Permissions
                        </a>
                    </div>
                </div>
            @endrole
        </div>
    </nav>
</aside>
