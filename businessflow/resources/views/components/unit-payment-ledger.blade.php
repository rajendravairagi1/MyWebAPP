@props(['unit', 'editable' => true])

@php
    $purposeStyles = [
        'token' => ['border' => 'border-l-blue-500', 'badge' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'],
        'installment' => ['border' => 'border-l-accent-500', 'badge' => 'bg-accent-100 dark:bg-slate-700 text-accent-700 dark:text-accent-100'],
        'registry' => ['border' => 'border-l-amber-500', 'badge' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300'],
        'maintenance' => ['border' => 'border-l-teal-500', 'badge' => 'bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300'],
    ];
    $defaultStyle = ['border' => 'border-l-gray-400', 'badge' => 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300'];
@endphp

@if ($unit->payments->isNotEmpty())
    <div class="mt-3 space-y-4">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Payment history') }} ({{ $unit->payments->count() }})</div>

        @foreach ($unit->payments as $payment)
            @php $style = $purposeStyles[$payment->purpose] ?? $defaultStyle; @endphp
            <div class="rounded-lg border border-gray-200 dark:border-slate-600 {{ $style['border'] }} border-l-4 bg-gray-50 dark:bg-slate-900/60 shadow-sm p-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $style['badge'] }}">{{ $payment->purposeLabel() }}</span>
                            <span class="text-xs text-gray-400">{{ $payment->paid_at->format('d M Y') }}</span>
                        </div>
                        @if ($payment->description)
                            <div class="mt-1.5 text-sm text-gray-700 dark:text-gray-300">{{ $payment->description }}</div>
                        @endif
                        <div class="mt-1.5 text-xs text-gray-400">
                            {{ $payment->method ? ucfirst(str_replace('_', ' ', $payment->method)) : __('Method not set') }}
                            @if ($payment->reference) · {{ __('Ref') }}: {{ $payment->reference }} @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0 pr-1">
                        <div class="text-lg font-bold text-gray-900 dark:text-gray-100 whitespace-nowrap">₹{{ number_format($payment->amount, 0) }}</div>
                        @if ($editable)
                            <div class="mt-1.5 flex items-center justify-end gap-1.5 text-xs">
                                <details class="relative">
                                    <summary class="cursor-pointer px-2 py-1 rounded border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 list-none [&::-webkit-details-marker]:hidden">{{ __('Edit') }}</summary>
                                    @php $paymentPurposeIsCustom = ! array_key_exists($payment->purpose, \App\Models\UnitPayment::PURPOSES); @endphp
                                    <form method="POST" action="{{ route('unit-payments.update', [$unit, $payment]) }}" x-data="{ purpose: '{{ $paymentPurposeIsCustom ? 'other' : $payment->purpose }}' }" class="absolute right-0 z-10 mt-2 grid grid-cols-2 gap-1.5 text-left w-64 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 shadow-lg p-3 rounded-md">
                                        @csrf
                                        @method('PUT')
                                        <select name="purpose" x-model="purpose" class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            @foreach (\App\Models\UnitPayment::PURPOSES as $val => $label)
                                                <option value="{{ $val }}" @selected($paymentPurposeIsCustom ? $val === 'other' : $payment->purpose === $val)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <div x-show="purpose === 'other'" x-cloak class="col-span-2">
                                            <input type="text" name="purpose_other" value="{{ $paymentPurposeIsCustom ? $payment->purpose : '' }}" placeholder="{{ __('If Other, specify') }}" class="w-full text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                        </div>
                                        <textarea name="description" rows="2" placeholder="{{ __('Description') }}" class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">{{ $payment->description }}</textarea>
                                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ $payment->amount }}" required class="col-span-1 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                        <input type="date" name="paid_at" value="{{ $payment->paid_at->format('Y-m-d') }}" required class="col-span-1 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                        <select name="method" class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            @foreach (['cash' => 'Cash', 'upi' => 'UPI', 'bank_transfer' => 'Bank transfer', 'cheque' => 'Cheque', 'card' => 'Card', 'other' => 'Other'] as $val => $label)
                                                <option value="{{ $val }}" @selected($payment->method === $val)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="reference" value="{{ $payment->reference }}" placeholder="{{ __('Reference') }}" class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                        <button class="col-span-2 mt-1 px-2 py-1 bg-accent-600 text-white text-[11px] font-semibold rounded hover:bg-accent-700">{{ __('Save') }}</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('unit-payments.destroy', [$unit, $payment]) }}" onsubmit="return confirm('{{ __('Remove this payment?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 rounded border border-red-200 dark:border-red-900 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
