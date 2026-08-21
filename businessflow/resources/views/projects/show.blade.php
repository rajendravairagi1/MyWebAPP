@php
    $cost = $project->totalCost();
    $revenue = $project->totalRevenue();
    $invoiced = $project->totalInvoiced();
    $profit = $revenue - $cost;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $project->name }}</h2>
                <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $project->type) }} @if($project->location) · {{ $project->location }} @endif</div>
            </div>
            <div class="flex items-center gap-3">
                <x-status-badge :status="$project->status" />
                <a href="{{ route('projects.edit', $project) }}" class="text-sm text-indigo-600 hover:underline">{{ __('Edit') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            {{-- P&L summary --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total cost') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($cost, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Booked value') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($invoiced, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Received') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($revenue, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Profit / Loss') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profit, 0) }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ __('received − cost') }}</div>
                </div>
            </div>

            @if ($costsByCategory->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">{{ __('Cost by category') }}</div>
                    <div class="flex flex-wrap gap-4 text-sm">
                        @foreach ($costsByCategory as $category => $amount)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-slate-700/50 rounded-md">
                                <span class="capitalize text-gray-600 dark:text-gray-400">{{ $category }}:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($amount, 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Units --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Units') }} ({{ $project->units->count() }})</div>
                @if ($project->units->isNotEmpty())
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Unit') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Type') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Area (sqft)') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Price') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Paid') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Balance') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Status') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Customer') }}</th>
                                <th class="px-5 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($project->units as $unit)
                                <tr>
                                    <td class="px-5 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $unit->unit_number }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $unit->type }}</td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ $unit->area_sqft ?? '—' }}</td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($unit->price, 0) }}</td>
                                    @if ($unit->customer)
                                        <td class="px-5 py-2 text-right text-green-600">{{ number_format($unit->totalPaid(), 0) }}</td>
                                        <td class="px-5 py-2 text-right {{ $unit->balanceDue() > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($unit->balanceDue(), 0) }}</td>
                                    @else
                                        <td class="px-5 py-2 text-right text-gray-300 dark:text-slate-600">—</td>
                                        <td class="px-5 py-2 text-right text-gray-300 dark:text-slate-600">—</td>
                                    @endif
                                    <td class="px-5 py-2"><x-status-badge :status="$unit->status" /></td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">
                                        @if ($unit->customer)
                                            <a href="{{ route('customers.show', $unit->customer) }}" class="text-indigo-600 hover:underline">{{ $unit->customer->name }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-right">
                                        <form method="POST" action="{{ route('project-units.destroy', [$project, $unit]) }}" onsubmit="return confirm('{{ __('Remove this unit?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <form method="POST" action="{{ route('project-units.store', $project) }}" class="p-5 border-t border-gray-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-6 gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="unit_number" :value="__('Unit no.')" class="text-xs" />
                        <x-text-input id="unit_number" name="unit_number" type="text" class="mt-1 block w-full text-sm" required />
                    </div>
                    <div>
                        <x-input-label for="type" :value="__('Type')" class="text-xs" />
                        <x-text-input id="type" name="type" type="text" placeholder="2BHK" class="mt-1 block w-full text-sm" />
                    </div>
                    <div>
                        <x-input-label for="area_sqft" :value="__('Area (sqft)')" class="text-xs" />
                        <x-text-input id="area_sqft" name="area_sqft" type="number" step="0.01" class="mt-1 block w-full text-sm" />
                    </div>
                    <div>
                        <x-input-label for="price" :value="__('Price')" class="text-xs" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full text-sm" required />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Status')" class="text-xs" />
                        <select id="status" name="status" class="mt-1 block w-full text-sm border-gray-300 rounded-md">
                            <option value="available">{{ __('Available') }}</option>
                            <option value="booked">{{ __('Booked') }}</option>
                            <option value="sold">{{ __('Sold') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-primary-button class="w-full justify-center">{{ __('+ Add Unit') }}</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Costs --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Cost entries') }} ({{ $project->costs->count() }})</div>
                @if ($project->costs->isNotEmpty())
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Date') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Category') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Description') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Vendor') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                                <th class="px-5 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($project->costs as $entry)
                                <tr>
                                    <td class="px-5 py-2 text-gray-500 dark:text-gray-400">{{ $entry->spent_on->format('d M Y') }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400 capitalize">{{ $entry->category }}</td>
                                    <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $entry->description }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $entry->vendor }}</td>
                                    <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ number_format($entry->amount, 2) }}</td>
                                    <td class="px-5 py-2 text-right">
                                        <form method="POST" action="{{ route('project-costs.destroy', [$project, $entry]) }}" onsubmit="return confirm('{{ __('Remove this entry?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <form method="POST" action="{{ route('project-costs.store', $project) }}" class="p-5 border-t border-gray-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-6 gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="category" :value="__('Category')" class="text-xs" />
                        <select id="category" name="category" class="mt-1 block w-full text-sm border-gray-300 rounded-md">
                            @foreach (['land' => 'Land', 'construction' => 'Construction', 'material' => 'Material', 'labor' => 'Labor', 'approval' => 'Approvals', 'marketing' => 'Marketing', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="description" :value="__('Description')" class="text-xs" />
                        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full text-sm" required />
                    </div>
                    <div>
                        <x-input-label for="amount" :value="__('Amount')" class="text-xs" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full text-sm" required />
                    </div>
                    <div>
                        <x-input-label for="spent_on" :value="__('Date')" class="text-xs" />
                        <x-text-input id="spent_on" name="spent_on" type="date" value="{{ now()->toDateString() }}" class="mt-1 block w-full text-sm" required />
                    </div>
                    <div>
                        <x-input-label for="vendor" :value="__('Vendor (optional)')" class="text-xs" />
                        <x-text-input id="vendor" name="vendor" type="text" class="mt-1 block w-full text-sm" />
                    </div>
                    <div class="sm:col-span-6">
                        <x-primary-button>{{ __('+ Add Cost Entry') }}</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Quotations & Invoices --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Quotations') }}</span>
                        <a href="{{ route('quotations.create') }}" class="text-xs text-indigo-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($project->quotations->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($project->quotations as $quotation)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-indigo-600 hover:underline">{{ $quotation->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($quotation->total, 0) }}</span>
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
                    @if ($project->invoices->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($project->invoices as $invoice)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">{{ $invoice->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($invoice->total, 0) }}</span>
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
