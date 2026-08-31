<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $company->name }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-branch')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Add Branch') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Each branch can have its own manager and any number of builders — each builder runs fully independently, with its own projects, customers and team.') }}
            </p>

            {{-- Company-wide P&L across every branch --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-4">{{ __('Company-wide — all branches') }}</div>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Projects') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $companyTotals['projects'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Collected') }}</div>
                        <div class="mt-1 text-xl font-semibold text-green-600">{{ $companyCurrencySymbol }}{{ number_format($companyTotals['collected'], 0) }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}</div>
                        <div class="mt-1 text-xl font-semibold {{ $companyTotals['outstanding'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $companyCurrencySymbol }}{{ number_format($companyTotals['outstanding'], 0) }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Cost') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $companyCurrencySymbol }}{{ number_format($companyTotals['cost'], 0) }}</div>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Profit / Loss') }}</div>
                        <div class="mt-1 text-xl font-semibold {{ $companyTotals['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $companyCurrencySymbol }}{{ number_format($companyTotals['profit'], 0) }}</div>
                    </div>
                </div>

                @if ($branches->isNotEmpty())
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-slate-700">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Profit / loss by branch') }}</div>
                        <x-profit-chart :labels="$branches->pluck('name')" :values="$branches->map(fn ($b) => round($branchStats[$b->id]['profit'], 2))" />
                    </div>
                @endif
            </div>

            @if ($branches->isEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No branches yet — add your first one to start adding builders under it.') }}
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($branches as $branch)
                        @php $stats = $branchStats[$branch->id]; @endphp
                        <a href="{{ route('branches.show', $branch) }}" class="block bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 hover:ring-2 hover:ring-accent-500 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $branch->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $branch->manager ? __('Manager').': '.$branch->manager->name : __('No manager assigned') }}
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 shrink-0">{{ $branch->businesses->count() }} {{ __('builders') }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-4 gap-2 text-xs">
                                <div>
                                    <div class="text-gray-400">{{ __('Projects') }}</div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['projects'] }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-400">{{ __('Collected') }}</div>
                                    <div class="font-semibold text-green-600">{{ $companyCurrencySymbol }}{{ number_format($stats['collected'], 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-400">{{ __('Outstanding') }}</div>
                                    <div class="font-semibold {{ $stats['outstanding'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $companyCurrencySymbol }}{{ number_format($stats['outstanding'], 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-400">{{ __('Profit') }}</div>
                                    <div class="font-semibold {{ $stats['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $companyCurrencySymbol }}{{ number_format($stats['profit'], 0) }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <x-modal name="add-branch" max-width="md" :show="$errors->any()">
        <form method="POST" action="{{ route('branches.store') }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Branch') }}</h2>
            <div>
                <x-input-label for="name" :value="__('Branch name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. Delhi Branch') }}" required autofocus />
            </div>

            <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Branch Manager (optional)') }}</p>
                <p class="text-xs text-gray-400 mb-3">{{ __('They\'ll be able to log in and manage every builder in this branch. You can also add/change this later.') }}</p>
                <div class="space-y-3">
                    <x-text-input name="manager_name" type="text" class="block w-full" placeholder="{{ __('Manager name') }}" />
                    <x-text-input name="manager_email" type="email" class="block w-full" placeholder="{{ __('Manager email') }}" />
                    <x-text-input name="manager_password" type="text" class="block w-full" placeholder="{{ __('Password (if new login)') }}" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-primary-button>{{ __('+ Add Branch') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
