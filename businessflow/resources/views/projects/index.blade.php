@php
    $ongoingCount = $projects->where('status', 'ongoing')->count();
    $totalCost = $projects->sum(fn ($p) => $p->totalCost());
    $totalRevenue = $projects->sum(fn ($p) => $p->totalRevenue());
    $totalProfit = $totalRevenue - $totalCost;

    $statusStyles = [
        'planning' => 'border-l-gray-400',
        'ongoing' => 'border-l-blue-500',
        'on_hold' => 'border-l-amber-500',
        'completed' => 'border-l-green-500',
    ];
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Projects') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            {{-- Header card --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 shrink-0 rounded-lg bg-accent-100 dark:bg-slate-700 text-accent-700 flex items-center justify-center">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 12h.01M9 15h.01M9 18h.01" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Your Projects') }}</h3>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $projects->count() }} {{ __('total') }} · {{ $ongoingCount }} {{ __('ongoing') }}</div>
                    </div>
                </div>
                <a href="{{ route('projects.create') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    {{ __('New Project') }}
                </a>
            </div>

            {{-- Summary stat cards --}}
            @if ($projects->isNotEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Projects') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $projects->count() }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $ongoingCount }} {{ __('ongoing') }}</div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Cost') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalCost, 0) }}</div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Received') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalRevenue, 0) }}</div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Profit') }}</div>
                        <div class="mt-1 text-2xl font-semibold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalProfit, 0) }}</div>
                    </div>
                </div>
            @endif

            {{-- Project cards --}}
            @if ($projects->isEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No projects yet — add your first property/development.') }}</div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($projects as $project)
                        @php
                            $cost = $project->totalCost();
                            $revenue = $project->totalRevenue();
                            $profit = $revenue - $cost;
                            $borderStyle = $statusStyles[$project->status] ?? 'border-l-gray-400';
                        @endphp
                        <a href="{{ route('projects.show', $project) }}" class="block bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 {{ $borderStyle }} border-l-4 shadow-sm hover:shadow-md rounded-lg p-5 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $project->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 capitalize mt-0.5">{{ str_replace('_', ' ', $project->type) }} · {{ $project->units_count }} {{ __('units') }}</div>
                                </div>
                                <x-status-badge :status="$project->status" />
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-700 grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ __('Cost') }}</div>
                                    <div class="text-gray-900 dark:text-gray-100 font-semibold mt-0.5">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($cost, 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ __('Received') }}</div>
                                    <div class="text-green-600 font-semibold mt-0.5">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($revenue, 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ __('Profit') }}</div>
                                    <div class="font-semibold mt-0.5 {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($profit, 0) }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
