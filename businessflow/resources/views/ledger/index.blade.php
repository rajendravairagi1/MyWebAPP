<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Ledger') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-manual-entry')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Manual Entry') }}
            </button>
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

            {{-- Overview --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Sales') }}</div>
                    <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalSaleValue, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Collected') }}</div>
                    <div class="mt-1 text-xl font-semibold text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalCollected, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}</div>
                    <div class="mt-1 text-xl font-semibold {{ $totalOutstanding > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalOutstanding, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Purchases / Costs') }}</div>
                    <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalPurchases, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Net Profit') }}</div>
                    <div class="mt-1 text-xl font-semibold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($netProfit, 0) }}</div>
                </div>
            </div>
            <p class="text-xs text-gray-400 -mt-3">
                {{ __('Total Sales is the full booked value (for reference only). Profit = Collected + manual income + property deal profit − Purchases − broker commission paid — only money actually received/paid counts; Outstanding isn\'t profit until it\'s collected.') }}
                @if ($dealsProfit != 0)
                    {{ __('Includes :amount profit from Property Deals.', ['amount' => \App\Support\Tenant::currencySymbol().number_format($dealsProfit, 0)]) }}
                @endif
                @if ($brokerCommissionPaid != 0)
                    {{ __(':amount paid out in broker commission is deducted.', ['amount' => \App\Support\Tenant::currencySymbol().number_format($brokerCommissionPaid, 0)]) }}
                @endif
            </p>

            {{-- Per-project breakdown --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('By Project') }}</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
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
                                    <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->saleValue, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->collected, 0) }}</td>
                                    <td class="px-5 py-2 text-right {{ $row->outstanding > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->outstanding, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->purchases, 0) }}</td>
                                    <td class="px-5 py-2 text-right font-medium {{ $row->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->profit, 0) }}</td>
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
                    <table class="min-w-full text-sm">
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
                                            @if ($row->customer->trashed())
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-400">{{ __('deleted') }}</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $row->unit->project->name }} · {{ $row->unit->unit_number }}</td>
                                    <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->unit->price, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->unit->totalCollected(), 0) }}</td>
                                    <td class="px-5 py-2 text-right {{ $row->unit->totalOutstanding() > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($row->unit->totalOutstanding(), 0) }}</td>
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

            {{-- Property deals — resale/trading, separate from own projects --}}
            @if ($deals->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100 flex items-center justify-between">
                        <span>{{ __('Property Deals') }} <span class="text-xs font-normal text-gray-400">— {{ __('buy from one party, sell to another') }}</span></span>
                        <a href="{{ route('property-deals.index') }}" class="text-xs text-accent-600 hover:underline font-normal">{{ __('Manage →') }}</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500">
                                    <th class="px-5 py-2">{{ __('Property') }}</th>
                                    <th class="px-5 py-2">{{ __('Seller') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Purchase') }}</th>
                                    <th class="px-5 py-2">{{ __('Buyer') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Sale') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Profit') }}</th>
                                    <th class="px-5 py-2">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($deals as $deal)
                                    @php $profit = $deal->profit(); @endphp
                                    <tr>
                                        <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $deal->property_title }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $deal->seller_name ?? '—' }}</td>
                                        <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($deal->purchase_price, 0) }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $deal->buyer_name ?? '—' }}</td>
                                        <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ $deal->sale_price !== null ? \App\Support\Tenant::currencySymbol().number_format($deal->sale_price, 0) : '—' }}</td>
                                        <td class="px-5 py-2 text-right font-medium {{ $profit === null ? 'text-gray-400' : ($profit >= 0 ? 'text-green-600' : 'text-red-600') }}">
                                            {{ $profit !== null ? \App\Support\Tenant::currencySymbol().number_format($profit, 0) : '—' }}
                                        </td>
                                        <td class="px-5 py-2">
                                            <span @class([
                                                'text-xs px-2 py-0.5 rounded font-medium',
                                                'bg-amber-100 text-amber-700' => $deal->status === 'open',
                                                'bg-green-100 text-green-700' => $deal->status === 'sold',
                                                'bg-gray-100 text-gray-500' => $deal->status === 'cancelled',
                                            ])>{{ $deal->statusLabel() }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Manual entries --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Manual Entries') }}
                    <span class="text-xs font-normal text-gray-400">— {{ __('for anything outside a normal sale, e.g. an extra expense or misc. income') }}</span>
                </div>

                @if ($entries->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500">
                                    <th class="px-5 py-2">{{ __('Date') }}</th>
                                    <th class="px-5 py-2">{{ __('Type') }}</th>
                                    <th class="px-5 py-2">{{ __('Category') }}</th>
                                    <th class="px-5 py-2">{{ __('Description') }}</th>
                                    <th class="px-5 py-2">{{ __('Linked to') }}</th>
                                    <th class="px-5 py-2">{{ __('Account') }}</th>
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
                                            @if ($entry->customer)<a href="{{ route('customers.show', $entry->customer) }}" class="text-accent-600 hover:underline">{{ $entry->customer->name }}</a>@if ($entry->customer->trashed()) <span class="text-xs text-gray-400">({{ __('deleted') }})</span>@endif @endif
                                            @if ($entry->project)<a href="{{ route('projects.show', $entry->project) }}" class="text-accent-600 hover:underline">{{ $entry->project->name }}</a>@endif
                                            @if (!$entry->customer && !$entry->project) — @endif
                                        </td>
                                        <td class="px-5 py-2 text-gray-500 dark:text-gray-400">{{ $entry->account?->label() ?? '—' }}</td>
                                        <td class="px-5 py-2 text-right font-medium {{ $entry->type === 'income' ? 'text-green-600' : 'text-red-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($entry->amount, 0) }}</td>
                                        <td class="px-5 py-2 text-right whitespace-nowrap">
                                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-entry-{{ $entry->id }}')" class="text-xs text-accent-600 hover:underline mr-2">{{ __('Edit') }}</button>
                                            <form method="POST" action="{{ route('ledger.entries.destroy', $entry) }}" onsubmit="return confirm('{{ __('Remove this entry?') }}')" class="inline">
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
            </div>
        </div>
    </div>

    <x-modal name="add-manual-entry" max-width="lg" :show="$errors->hasAny(['type', 'category', 'description', 'amount', 'entry_date', 'customer_id'])">
        <form method="POST" action="{{ route('ledger.entries.store') }}" x-data="{ type: 'income' }" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Manual Entry') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('For anything outside a normal sale, e.g. an extra expense or misc. income.') }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="entry_type" :value="__('Type')" />
                    <select id="entry_type" name="type" x-model="type" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="income">{{ __('Income') }}</option>
                        <option value="expense">{{ __('Expense') }}</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="entry_category" :value="__('Category (optional)')" />
                    <x-text-input id="entry_category" name="category" type="text" class="mt-1 block w-full" />
                </div>
            </div>

            <div>
                <x-input-label for="entry_description" :value="__('Description')" />
                <x-text-input id="entry_description" name="description" type="text" class="mt-1 block w-full" required />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="entry_amount" :value="__('Amount')" />
                    <x-text-input id="entry_amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="entry_date" :value="__('Date')" />
                    <x-text-input id="entry_date" name="entry_date" type="date" value="{{ now()->format('Y-m-d') }}" class="mt-1 block w-full" required />
                </div>
            </div>

            <div>
                <x-input-label for="entry_customer" :value="__('Customer (optional)')" />
                <select id="entry_customer" name="customer_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            @if ($paymentAccounts->isNotEmpty())
                <div>
                    <x-input-label for="entry_payment_account_id" x-text="type === 'expense' ? '{{ __('Paid From (optional)') }}' : '{{ __('Received In (optional)') }}'" />
                    <select id="entry_payment_account_id" name="payment_account_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('— Not specified —') }}</option>
                        @foreach ($paymentAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->label() }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-primary-button>{{ __('+ Add') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    @foreach ($entries as $entry)
        <x-modal name="edit-entry-{{ $entry->id }}" max-width="lg">
            <form method="POST" action="{{ route('ledger.entries.update', $entry) }}" x-data="{ type: '{{ $entry->type }}' }" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Manual Entry') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Type')" />
                        <select name="type" x-model="type" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="income" @selected($entry->type === 'income')>{{ __('Income') }}</option>
                            <option value="expense" @selected($entry->type === 'expense')>{{ __('Expense') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Category (optional)')" />
                        <x-text-input name="category" type="text" class="mt-1 block w-full" value="{{ $entry->category }}" />
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('Description')" />
                    <x-text-input name="description" type="text" class="mt-1 block w-full" required value="{{ $entry->description }}" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Amount')" />
                        <x-text-input name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required value="{{ $entry->amount }}" />
                    </div>
                    <div>
                        <x-input-label :value="__('Date')" />
                        <x-text-input name="entry_date" type="date" value="{{ $entry->entry_date->format('Y-m-d') }}" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('Customer (optional)')" />
                    <select name="customer_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" @selected($entry->customer_id === $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($paymentAccounts->isNotEmpty())
                    <div>
                        <x-input-label x-text="type === 'expense' ? '{{ __('Paid From (optional)') }}' : '{{ __('Received In (optional)') }}'" />
                        <select name="payment_account_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="">{{ __('— Not specified —') }}</option>
                            @foreach ($paymentAccounts as $account)
                                <option value="{{ $account->id }}" @selected($entry->payment_account_id === $account->id)>{{ $account->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>
