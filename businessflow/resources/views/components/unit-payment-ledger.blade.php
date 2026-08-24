@props(['unit', 'editable' => true])

@if ($unit->payments->isNotEmpty())
    <div class="mt-3 space-y-2">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Payment history') }} ({{ $unit->payments->count() }})</div>

        @foreach ($unit->payments as $payment)
            <div class="rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/40 p-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded font-medium bg-accent-100 dark:bg-slate-700 text-accent-700 dark:text-accent-100">{{ $payment->purposeLabel() }}</span>
                            <span class="text-xs text-gray-400">{{ $payment->paid_at->format('d M Y') }}</span>
                        </div>
                        @if ($payment->description)
                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $payment->description }}</div>
                        @endif
                        <div class="mt-1 text-xs text-gray-400">
                            {{ $payment->method ? ucfirst(str_replace('_', ' ', $payment->method)) : __('Method not set') }}
                            @if ($payment->reference) · {{ __('Ref') }}: {{ $payment->reference }} @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">₹{{ number_format($payment->amount, 0) }}</div>
                        @if ($editable)
                            <div class="mt-1 flex items-center justify-end gap-2 text-xs">
                                <details class="relative">
                                    <summary class="cursor-pointer text-accent-600 hover:underline list-none">{{ __('Edit') }}</summary>
                                    <form method="POST" action="{{ route('unit-payments.update', [$unit, $payment]) }}" class="absolute right-0 z-10 mt-2 grid grid-cols-2 gap-1.5 text-left w-64 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 shadow-lg p-3 rounded-md">
                                        @csrf
                                        @method('PUT')
                                        <select name="purpose" class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            @foreach (\App\Models\UnitPayment::PURPOSES as $val => $label)
                                                <option value="{{ $val }}" @selected($payment->purpose === $val)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="purpose_other" value="{{ !array_key_exists($payment->purpose, \App\Models\UnitPayment::PURPOSES) ? $payment->purpose : '' }}" placeholder="{{ __('If Other, specify') }}" class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
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
                                    <button class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
