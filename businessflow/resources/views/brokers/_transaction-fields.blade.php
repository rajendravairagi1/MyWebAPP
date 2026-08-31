@php $transaction ??= null; @endphp

@if ($transaction)
    <div>
        <x-input-label :value="__('Type')" />
        <select name="type" x-model="type" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="commission_accrued" @selected($transaction->type === 'commission_accrued')>{{ __('Commission Earned') }}</option>
            <option value="payment_paid" @selected($transaction->type === 'payment_paid')>{{ __('Payment Paid') }}</option>
        </select>
    </div>
@endif

<div x-show="type === 'commission_accrued'" x-cloak>
    <x-input-label :value="__('Property (optional)')" />
    <select name="project_unit_id" x-model="unitId" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
        <option value="">{{ __('— Not tied to a property —') }}</option>
        @foreach ($units as $unit)
            <option value="{{ $unit->id }}">{{ $unit->project->name }} · {{ $unit->unit_number }} ({{ \App\Support\Tenant::currencySymbol() }}{{ number_format($unit->price, 0) }})</option>
        @endforeach
    </select>
</div>

<div x-show="type === 'commission_accrued'" x-cloak class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
    <label class="inline-flex items-center gap-1.5"><input type="radio" name="commission_mode" value="fixed" x-model="mode"> {{ __('Fixed amount') }}</label>
    <label class="inline-flex items-center gap-1.5"><input type="radio" name="commission_mode" value="percent" x-model="mode"> {{ __('% of property price') }}</label>
</div>

<div x-show="type === 'commission_accrued' && mode === 'percent'" x-cloak class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('Commission %')" />
        <input type="number" step="0.01" min="0.01" max="100" name="commission_percent" x-model.number="percent" :required="type === 'commission_accrued' && mode === 'percent'" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
    <div>
        <x-input-label :value="__('= Amount')" />
        <div class="mt-1 flex items-center h-[42px] px-3 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-slate-700/40 rounded-md" x-text="'{{ \App\Support\Tenant::currencySymbol() }}' + (Math.round(computed * 100) / 100).toLocaleString()"></div>
    </div>
</div>

<div x-show="!(type === 'commission_accrued' && mode === 'percent')">
    <x-input-label :value="__('Amount')" />
    <input type="number" step="0.01" min="0.01" name="amount" x-model="amount" :required="!(type === 'commission_accrued' && mode === 'percent')" value="{{ old('amount', $transaction->amount ?? '') }}"
        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
</div>

<div>
    <x-input-label :value="__('Date')" />
    <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction?->transaction_date?->toDateString() ?? now()->toDateString()) }}" required
        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('Method (optional)')" />
        <select name="method" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            @php $selectedMethod = old('method', $transaction->method ?? ''); @endphp
            <option value="" @selected($selectedMethod === '')>{{ __('—') }}</option>
            <option value="cash" @selected($selectedMethod === 'cash')>{{ __('Cash') }}</option>
            <option value="bank_transfer" @selected($selectedMethod === 'bank_transfer')>{{ __('Bank Transfer') }}</option>
            <option value="upi" @selected($selectedMethod === 'upi')>{{ __('UPI') }}</option>
            <option value="cheque" @selected($selectedMethod === 'cheque')>{{ __('Cheque') }}</option>
        </select>
    </div>
    <div>
        <x-input-label :value="__('Reference (optional)')" />
        <input type="text" name="reference" value="{{ old('reference', $transaction->reference ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
</div>

<div>
    <x-input-label :value="__('Note (optional)')" />
    <input type="text" name="description" value="{{ old('description', $transaction->description ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
</div>
