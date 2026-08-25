@php $transaction ??= null; @endphp

@if ($transaction)
    <div>
        <x-input-label :value="__('Type')" />
        <select name="type" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="investment" @selected($transaction->type === 'investment')>{{ __('Investment') }}</option>
            <option value="profit_credited" @selected($transaction->type === 'profit_credited')>{{ __('Profit Credited') }}</option>
            <option value="payment_paid" @selected($transaction->type === 'payment_paid')>{{ __('Payment Paid') }}</option>
        </select>
    </div>
@endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('Amount')" />
        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $transaction->amount ?? '') }}" required
            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
    <div>
        <x-input-label :value="__('Date')" />
        <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction?->transaction_date?->toDateString() ?? now()->toDateString()) }}" required
            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
</div>

@if ($projects->isNotEmpty())
    <div>
        <x-input-label :value="__('Project (optional)')" />
        <select name="project_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('— Not tied to a project —') }}</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected(old('project_id', $transaction->project_id ?? null) == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
@endif

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
