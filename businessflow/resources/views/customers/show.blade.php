<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $customer->name }}</h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('customers.statement', $customer) }}" class="text-sm text-indigo-600 hover:underline">{{ __('Download Statement') }}</a>
                <a href="{{ route('customers.edit', $customer) }}" class="text-sm text-indigo-600 hover:underline">{{ __('Edit') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Company') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->company ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Phone') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->phone ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Email') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->email ?: '—' }}</div>
                </div>
                <div class="sm:col-span-3">
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Address') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->address ?: '—' }}</div>
                </div>
                @if ($customer->notes)
                    <div class="sm:col-span-3">
                        <div class="text-gray-500 dark:text-gray-400">{{ __('Notes') }}</div>
                        <div class="text-gray-900 dark:text-gray-100">{{ $customer->notes }}</div>
                    </div>
                @endif
            </div>

            @if ($customer->units->isNotEmpty())
                @php
                    $totalPaid = $customer->units->sum(fn ($u) => $u->totalPaid());
                    $totalDue = $customer->units->sum(fn ($u) => $u->balanceDue());
                @endphp
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Properties Purchased') }}</div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Project') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Unit') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Price') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Paid') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Balance') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($customer->units as $unit)
                                <tr>
                                    <td class="px-5 py-2">
                                        <a href="{{ route('projects.show', $unit->project) }}" class="text-indigo-600 hover:underline">{{ $unit->project->name }}</a>
                                    </td>
                                    <td class="px-5 py-2 text-gray-900 dark:text-gray-100 font-medium">{{ $unit->unit_number }}</td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($unit->price, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-green-600">{{ number_format($unit->totalPaid(), 0) }}</td>
                                    <td class="px-5 py-2 text-right {{ $unit->balanceDue() > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($unit->balanceDue(), 0) }}</td>
                                    <td class="px-5 py-2"><x-status-badge :status="$unit->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-gray-200 dark:border-slate-700 font-medium">
                            <tr>
                                <td class="px-5 py-2" colspan="3">{{ __('Total') }}</td>
                                <td class="px-5 py-2 text-right text-green-600">{{ number_format($totalPaid, 0) }}</td>
                                <td class="px-5 py-2 text-right {{ $totalDue > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($totalDue, 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Quotations') }}</span>
                        <a href="{{ route('quotations.create') }}" class="text-xs text-indigo-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($customer->quotations->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($customer->quotations as $quotation)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-indigo-600 hover:underline">{{ $quotation->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($quotation->total, 2) }}</span>
                                        <x-status-badge :status="$quotation->status" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Invoices') }}</span>
                        <a href="{{ route('invoices.create') }}" class="text-xs text-indigo-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($customer->invoices->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($customer->invoices as $invoice)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">{{ $invoice->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($invoice->total, 2) }}</span>
                                        <x-status-badge :status="$invoice->status" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
