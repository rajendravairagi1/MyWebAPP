@props(['unit', 'editable' => true])

@if ($unit->payments->isNotEmpty())
    <details class="mt-3">
        <summary class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-400 select-none">
            {{ __('Payment history') }} ({{ $unit->payments->count() }})
        </summary>
        <div class="mt-2 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400">
                        <th class="text-left font-medium py-1">{{ __('Date') }}</th>
                        <th class="text-left font-medium py-1">{{ __('Method') }}</th>
                        <th class="text-left font-medium py-1">{{ __('Reference') }}</th>
                        <th class="text-right font-medium py-1">{{ __('Amount') }}</th>
                        @if ($editable)
                            <th class="py-1"></th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                    @foreach ($unit->payments as $payment)
                        <tr>
                            <td class="py-1.5 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $payment->paid_at->format('d M Y') }}</td>
                            <td class="py-1.5 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $payment->method ? ucfirst(str_replace('_', ' ', $payment->method)) : '—' }}</td>
                            <td class="py-1.5 text-gray-500 dark:text-gray-400">{{ $payment->reference ?? '—' }}</td>
                            <td class="py-1.5 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">₹{{ number_format($payment->amount, 0) }}</td>
                            @if ($editable)
                                <td class="py-1.5 text-right whitespace-nowrap">
                                    <details class="inline-block relative">
                                        <summary class="cursor-pointer text-accent-600 hover:underline list-none inline">{{ __('Edit') }}</summary>
                                        <form method="POST" action="{{ route('unit-payments.update', [$unit, $payment]) }}" class="absolute right-0 z-10 mt-2 grid grid-cols-2 gap-1.5 text-left w-56 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 shadow-lg p-3 rounded-md">
                                            @csrf
                                            @method('PUT')
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
                                    <form method="POST" action="{{ route('unit-payments.destroy', [$unit, $payment]) }}" class="inline" onsubmit="return confirm('{{ __('Remove this payment?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ml-2 text-red-600 hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
@endif
