@php
    $types = ['sales' => 'Sales', 'collections' => 'Collections', 'customers' => 'Customers', 'projects' => 'Projects'];
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <x-input-label for="type" :value="__('Report')" />
                        <select id="type" name="type" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            @foreach ($types as $val => $label)
                                <option value="{{ $val }}" @selected($type === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($report['dated'])
                        <div>
                            <x-input-label for="from" :value="__('From')" />
                            <input id="from" name="from" type="date" value="{{ $from->format('Y-m-d') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                        <div>
                            <x-input-label for="to" :value="__('To')" />
                            <input id="to" name="to" type="date" value="{{ $to->format('Y-m-d') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Apply') }}</button>
                    @endif

                    <div class="ml-auto flex gap-2">
                        <a href="{{ route('reports.download', ['type' => $type, 'format' => 'pdf', 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            {{ __('PDF') }}
                        </a>
                        <a href="{{ route('reports.download', ['type' => $type, 'format' => 'csv', 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            {{ __('Excel (.csv)') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $report['title'] }}</span>
                    <div class="flex gap-4 text-xs text-gray-500 dark:text-gray-400">
                        @foreach ($report['totals'] as $label => $value)
                            <span>{{ $label }}: <strong class="text-gray-800 dark:text-gray-100">{{ $value }}</strong></span>
                        @endforeach
                    </div>
                </div>

                @if (empty($report['rows']))
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No data for this report.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-slate-700/60">
                                    @foreach ($report['columns'] as $col)
                                        <th class="px-5 py-2">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($report['rows'] as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td class="px-5 py-2 text-gray-700 dark:text-gray-300">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
