<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('brokers.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $broker->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400 space-y-0.5">
                    @if ($broker->phone)<div>{{ __('Mobile') }}: {{ $broker->phone }}</div>@endif
                    @if ($broker->email)<div>{{ $broker->email }}</div>@endif
                    @if ($broker->notes)<div class="mt-1 text-gray-400 dark:text-gray-500">{{ $broker->notes }}</div>@endif
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-broker')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        {{ __('Edit') }}
                    </button>
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'record-commission')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                        {{ __('+ Add Commission') }}
                    </button>
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'record-payment')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        {{ __('+ Add Payment Paid') }}
                    </button>
                    <form method="POST" action="{{ route('brokers.destroy', $broker) }}" onsubmit="return confirm('{{ __('Delete this broker and all their transactions? This cannot be undone.') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-red-200 dark:border-red-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                            {{ __('Delete Broker') }}
                        </button>
                    </form>
                </div>

                <div class="mt-5 grid grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Commission Earned') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($broker->totalCommissionAccrued(), 2) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Paid Out') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($broker->totalPaid(), 2) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Balance Owed') }}</div>
                        <div class="text-lg font-semibold {{ $broker->balance() > 0 ? 'text-amber-600' : 'text-gray-900 dark:text-gray-100' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($broker->balance(), 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Ledger') }}
                </div>

                @if ($broker->transactions->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No transactions recorded yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Date') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Type') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Property') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Amount') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Note') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($broker->transactions as $transaction)
                                <tr>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        <span @class([
                                            'text-xs px-2 py-0.5 rounded font-medium',
                                            'bg-green-100 text-green-700' => $transaction->type === 'commission_accrued',
                                            'bg-rose-100 text-rose-700' => $transaction->type === 'payment_paid',
                                        ])>{{ $transaction->typeLabel() }}{{ $transaction->commission_percent ? ' ('.rtrim(rtrim(number_format($transaction->commission_percent, 2), '0'), '.').'%)' : '' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                        @if ($transaction->unit)
                                            {{ $transaction->unit->project->name }} · {{ $transaction->unit->unit_number }}
                                        @elseif ($transaction->deal)
                                            {{ $transaction->deal->property_title }} <span class="text-xs text-gray-400">({{ __('deal') }})</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($transaction->amount, 2) }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $transaction->detailsSummary() ?: '—' }}</td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-transaction-{{ $transaction->id }}')" class="text-accent-600 hover:underline text-xs">{{ __('Edit') }}</button>
                                        <form method="POST" action="{{ route('broker-transactions.destroy', [$broker, $transaction]) }}" onsubmit="return confirm('{{ __('Delete this transaction?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs ml-2">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="record-commission" max-width="md">
        <form method="POST" action="{{ route('broker-transactions.store', $broker) }}" x-data="{ type: 'commission_accrued', mode: 'fixed', unitId: '', dealId: '', percent: '', amount: '', unitPrices: {{ \Illuminate\Support\Js::from($units->pluck('price', 'id')) }}, dealPrices: {{ \Illuminate\Support\Js::from($deals->pluck('sale_price', 'id')) }}, get computed() { const base = this.unitId ? (this.unitPrices[this.unitId] || 0) : (this.dealId ? (this.dealPrices[this.dealId] || 0) : 0); return base && this.percent ? (parseFloat(base) * parseFloat(this.percent || 0) / 100) : 0 } }" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="type" value="commission_accrued">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Commission') }}</h2>
            <p class="text-xs text-gray-400">{{ __('Commission earned by :name for a sale.', ['name' => $broker->name]) }}</p>
            @include('brokers._transaction-fields', ['transaction' => null])
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="record-payment" max-width="md">
        <form method="POST" action="{{ route('broker-transactions.store', $broker) }}" x-data="{ type: 'payment_paid', mode: 'fixed', unitId: '', dealId: '', percent: '', amount: '', unitPrices: {}, dealPrices: {}, computed: 0 }" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="type" value="payment_paid">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Payment Paid') }}</h2>
            <p class="text-xs text-gray-400">{{ __('Brokerage actually paid out to :name.', ['name' => $broker->name]) }}</p>
            @include('brokers._transaction-fields', ['transaction' => null])
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    @foreach ($broker->transactions as $transaction)
        <x-modal name="edit-transaction-{{ $transaction->id }}" max-width="md">
            <form method="POST" action="{{ route('broker-transactions.update', [$broker, $transaction]) }}" x-data="{ type: '{{ $transaction->type }}', mode: '{{ $transaction->commission_percent ? 'percent' : 'fixed' }}', unitId: '{{ $transaction->project_unit_id }}', dealId: '{{ $transaction->property_deal_id }}', percent: '{{ $transaction->commission_percent }}', amount: '{{ $transaction->amount }}', unitPrices: {{ \Illuminate\Support\Js::from($units->pluck('price', 'id')) }}, dealPrices: {{ \Illuminate\Support\Js::from($deals->pluck('sale_price', 'id')) }}, get computed() { const base = this.unitId ? (this.unitPrices[this.unitId] || 0) : (this.dealId ? (this.dealPrices[this.dealId] || 0) : 0); return base && this.percent ? (parseFloat(base) * parseFloat(this.percent || 0) / 100) : 0 } }" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Transaction') }}</h2>
                @include('brokers._transaction-fields', ['transaction' => $transaction])
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endforeach

    <x-modal name="edit-broker" max-width="md">
        <form method="POST" action="{{ route('brokers.update', $broker) }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Broker') }}</h2>
            <div>
                <x-input-label for="edit_name" :value="__('Name')" />
                <input type="text" id="edit_name" name="name" value="{{ $broker->name }}" required
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_phone" :value="__('Phone')" />
                <input type="text" id="edit_phone" name="phone" value="{{ $broker->phone }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_email" :value="__('Email')" />
                <input type="email" id="edit_email" name="email" value="{{ $broker->email }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_notes" :value="__('Notes')" />
                <textarea id="edit_notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $broker->notes }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
