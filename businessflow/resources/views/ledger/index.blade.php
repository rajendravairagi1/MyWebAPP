<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Ledger') }}</h2>
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

            {{-- Overview --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Sales') }}</div>
                    <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($totalSaleValue, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Collected') }}</div>
                    <div class="mt-1 text-xl font-semibold text-green-600">₹{{ number_format($totalCollected, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}</div>
                    <div class="mt-1 text-xl font-semibold {{ $totalOutstanding > 0 ? 'text-red-600' : 'text-gray-400' }}">₹{{ number_format($totalOutstanding, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Purchases / Costs') }}</div>
                    <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($totalPurchases, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Net Profit') }}</div>
                    <div class="mt-1 text-xl font-semibold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($netProfit, 0) }}</div>
                </div>
            </div>
            <p class="text-xs text-gray-400 -mt-3">{{ __('Sales counts booked/sold properties at their full price; Profit = Total Sales + manual income − Purchases. Outstanding is money still to be collected.') }}</p>

            {{-- Per-project breakdown --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('By Project') }}</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500">
                                <th class="px-5 py-2">{{ __('Project') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Units Sold') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Sale Value') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Collected') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Outstanding') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Purchases') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Profit') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse ($projects as $row)
                                <tr>
                                    <td class="px-5 py-2"><a href="{{ route('projects.show', $row->project) }}" class="text-accent-600 hover:underline">{{ $row->project->name }}</a></td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ $row->unitCount }}</td>
                                    <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">₹{{ number_format($row->saleValue, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-green-600">₹{{ number_format($row->collected, 0) }}</td>
                                    <td class="px-5 py-2 text-right {{ $row->outstanding > 0 ? 'text-red-600' : 'text-gray-400' }}">₹{{ number_format($row->outstanding, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">₹{{ number_format($row->purchases, 0) }}</td>
                                    <td class="px-5 py-2 text-right font-medium {{ $row->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($row->profit, 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No projects with sales or costs yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Per-customer breakdown --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('By Customer & Property') }}</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500">
                                <th class="px-5 py-2">{{ __('Customer') }}</th>
                                <th class="px-5 py-2">{{ __('Property') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Sale Value') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Collected') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Outstanding') }}</th>
                                <th class="px-5 py-2">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse ($customerRows as $row)
                                <tr>
                                    <td class="px-5 py-2">
                                        @if ($row->customer)
                                            <a href="{{ route('customers.show', $row->customer) }}" class="text-accent-600 hover:underline">{{ $row->customer->name }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $row->unit->project->name }} · {{ $row->unit->unit_number }}</td>
                                    <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">₹{{ number_format($row->unit->price, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-green-600">₹{{ number_format($row->unit->totalCollected(), 0) }}</td>
                                    <td class="px-5 py-2 text-right {{ $row->unit->totalOutstanding() > 0 ? 'text-red-600' : 'text-gray-400' }}">₹{{ number_format($row->unit->totalOutstanding(), 0) }}</td>
                                    <td class="px-5 py-2">
                                        @if ($row->unit->write_off_at)
                                            <span class="text-xs px-2 py-0.5 rounded font-medium bg-red-100 text-red-700">{{ __('Written off') }}</span>
                                        @else
                                            <x-status-badge :status="$row->unit->status" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No booked or sold properties yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Manual entries --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Manual Entries') }}
                    <span class="text-xs font-normal text-gray-400">— {{ __('for anything outside a normal sale, e.g. an extra expense or misc. income') }}</span>
                </div>

                @if ($entries->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500">
                                    <th class="px-5 py-2">{{ __('Date') }}</th>
                                    <th class="px-5 py-2">{{ __('Type') }}</th>
                                    <th class="px-5 py-2">{{ __('Category') }}</th>
                                    <th class="px-5 py-2">{{ __('Description') }}</th>
                                    <th class="px-5 py-2">{{ __('Linked to') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                                    <th class="px-5 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($entries as $entry)
                                    <tr>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $entry->entry_date->format('d M Y') }}</td>
                                        <td class="px-5 py-2">
                                            <span class="text-xs px-2 py-0.5 rounded font-medium {{ $entry->type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($entry->type) }}</span>
                                        </td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $entry->category ?? '—' }}</td>
                                        <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $entry->description }}</td>
                                        <td class="px-5 py-2 text-gray-500 dark:text-gray-400">
                                            @if ($entry->customer)<a href="{{ route('customers.show', $entry->customer) }}" class="text-accent-600 hover:underline">{{ $entry->customer->name }}</a>@endif
                                            @if ($entry->project)<a href="{{ route('projects.show', $entry->project) }}" class="text-accent-600 hover:underline">{{ $entry->project->name }}</a>@endif
                                            @if (!$entry->customer && !$entry->project) — @endif
                                        </td>
                                        <td class="px-5 py-2 text-right font-medium {{ $entry->type === 'income' ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($entry->amount, 0) }}</td>
                                        <td class="px-5 py-2 text-right">
                                            <form method="POST" action="{{ route('ledger.entries.destroy', $entry) }}" onsubmit="return confirm('{{ __('Remove this entry?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs text-red-600 hover:underline">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No manual entries yet.') }}</div>
                @endif

                <form method="POST" action="{{ route('ledger.entries.store') }}" class="p-5 border-t border-gray-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 items-end">
                    @csrf
                    <div class="col-span-1">
                        <x-input-label for="entry_type" :value="__('Type')" class="text-xs" />
                        <select id="entry_type" name="type" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="income">{{ __('Income') }}</option>
                            <option value="expense">{{ __('Expense') }}</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <x-input-label for="entry_category" :value="__('Category')" class="text-xs" />
                        <x-text-input id="entry_category" name="category" type="text" class="mt-1 block w-full text-sm" placeholder="{{ __('optional') }}" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="entry_description" :value="__('Description')" class="text-xs" />
                        <x-text-input id="entry_description" name="description" type="text" class="mt-1 block w-full text-sm" required />
                    </div>
                    <div class="col-span-1">
                        <x-input-label for="entry_amount" :value="__('Amount')" class="text-xs" />
                        <input id="entry_amount" name="amount" type="number" step="0.01" min="0.01" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div class="col-span-1">
                        <x-input-label for="entry_date" :value="__('Date')" class="text-xs" />
                        <input id="entry_date" name="entry_date" type="date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div class="col-span-1">
                        <x-input-label for="entry_customer" :value="__('Customer')" class="text-xs" />
                        <select id="entry_customer" name="customer_id" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="">{{ __('None') }}</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1">
                        <x-primary-button class="w-full justify-center">{{ __('+ Add') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
