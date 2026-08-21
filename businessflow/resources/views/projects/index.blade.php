<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Projects') }}</h2>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900">{{ __('+ New Project') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            @if ($projects->isEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No projects yet — add your first property/development.') }}</div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($projects as $project)
                        @php
                            $cost = $project->totalCost();
                            $revenue = $project->totalRevenue();
                            $profit = $revenue - $cost;
                        @endphp
                        <a href="{{ route('projects.show', $project) }}" class="block bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $project->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $project->type) }} · {{ $project->units_count }} {{ __('units') }}</div>
                                </div>
                                <x-status-badge :status="$project->status" />
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ __('Cost') }}</div>
                                    <div class="text-gray-900 dark:text-gray-100 font-medium">{{ number_format($cost, 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ __('Received') }}</div>
                                    <div class="text-gray-900 dark:text-gray-100 font-medium">{{ number_format($revenue, 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ __('Profit') }}</div>
                                    <div class="font-medium {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profit, 0) }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
