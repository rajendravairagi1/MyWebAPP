<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="history.back()" class="shrink-0 inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    {{ __('Back') }}
                </button>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ __('Invoice') }} {{ $invoice->number }}</h2>
            </div>
            <x-status-badge :status="$invoice->status" class="text-sm" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Download PDF') }}</a>
                <button type="button" onclick="sharePdfFile('{{ route('invoices.pdf', $invoice) }}', '{{ $invoice->number }}.pdf', this)" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 disabled:opacity-60">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12.001 2c-5.514 0-9.988 4.474-9.988 9.988 0 1.762.462 3.489 1.34 5.007L2 22l5.135-1.347a9.958 9.958 0 004.866 1.24h.004c5.514 0 9.987-4.474 9.987-9.988C22 6.474 17.53 2 12.001 2zm0 18.253h-.003a8.259 8.259 0 01-4.204-1.151l-.302-.179-3.045.799.813-2.968-.197-.305a8.257 8.257 0 01-1.267-4.41c0-4.565 3.715-8.28 8.28-8.28 4.564 0 8.28 3.715 8.28 8.28 0 4.565-3.716 8.214-8.355 8.214z"/></svg>
                    {{ __('Send PDF on WhatsApp') }}
                </button>
                @if ($invoice->payments->isEmpty())
                    <a href="{{ route('invoices.edit', $invoice) }}" class="px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Edit') }}</a>
                @endif
                @if ($invoice->status === 'draft')
                    <form method="POST" action="{{ route('invoices.mark-sent', $invoice) }}">
                        @csrf
                        <button class="px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Mark as Sent') }}</button>
                    </form>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Customer') }}</div>
                    <a href="{{ route('customers.show', $invoice->customer) }}" class="text-accent-600 hover:underline">{{ $invoice->customer->name }}</a>
                </div>
                @if ($invoice->project)
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">{{ __('Project') }}</div>
                        <a href="{{ route('projects.show', $invoice->project) }}" class="text-accent-600 hover:underline">{{ $invoice->project->name }}</a>
                        @if ($invoice->projectUnit)
                            <span class="text-gray-500 dark:text-gray-400">· {{ $invoice->projectUnit->unit_number }}</span>
                        @endif
                    </div>
                @endif
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Due date') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Total') }}</div>
                    <div class="text-gray-900 dark:text-gray-100 font-medium">{{ number_format($invoice->total, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Balance due') }}</div>
                    <div class="{{ $invoice->balanceDue() > 0 ? 'text-red-600 font-medium' : 'text-green-600 font-medium' }}">{{ $invoice->balanceDue() }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-3 text-left">{{ __('Item') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Qty') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Price') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Line total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="px-5 py-3 text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ rtrim(rtrim($item->quantity, '0'), '.') ?: '0' }}</td>
                                <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 flex justify-end">
                    <table class="text-sm w-64">
                        <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Subtotal') }}</td><td class="py-1 text-right">{{ number_format($invoice->subtotal, 2) }}</td></tr>
                        <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Discount') }}</td><td class="py-1 text-right">{{ number_format($invoice->discount_total, 2) }}</td></tr>
                        <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Tax') }}</td><td class="py-1 text-right">{{ number_format($invoice->tax_total, 2) }}</td></tr>
                        <tr class="border-t border-gray-200 font-semibold"><td class="py-1">{{ __('Total') }}</td><td class="py-1 text-right">{{ number_format($invoice->total, 2) }}</td></tr>
                        <tr class="text-green-700"><td class="py-1">{{ __('Paid') }}</td><td class="py-1 text-right">{{ number_format($invoice->amount_paid, 2) }}</td></tr>
                    </table>
                </div>

                @if ($invoice->projectUnit && $invoice->counts_toward_property_price)
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-slate-700 flex justify-end">
                        <table class="text-sm w-64">
                            <tr><td colspan="2" class="pb-1 text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ $invoice->projectUnit->unit_number }} {{ __('— property balance') }}</td></tr>
                            <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Property price') }}</td><td class="py-1 text-right">{{ number_format($invoice->projectUnit->price, 2) }}</td></tr>
                            <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Total received so far') }}</td><td class="py-1 text-right">{{ number_format($invoice->projectUnit->totalCollected(), 2) }}</td></tr>
                            <tr class="border-t border-gray-200 dark:border-slate-700 font-semibold"><td class="py-1">{{ __('Property balance remaining') }}</td><td class="py-1 text-right {{ $invoice->projectUnit->totalOutstanding() > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($invoice->projectUnit->totalOutstanding(), 2) }}</td></tr>
                        </table>
                    </div>
                @elseif ($invoice->projectUnit)
                    <div class="px-5 py-3 border-t border-gray-100 dark:border-slate-700 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('This is a separate charge for :unit — not counted toward the property price.', ['unit' => $invoice->projectUnit->unit_number]) }}
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Payments') }}</div>

                @if ($invoice->payments->isNotEmpty())
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($invoice->payments as $payment)
                            <li class="px-5 py-3 flex items-center justify-between">
                                <div>
                                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ number_format($payment->amount, 2) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">· {{ $payment->paid_at->format('d M Y') }}</span>
                                    @if ($payment->method)<span class="text-gray-500 dark:text-gray-400">· {{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span>@endif
                                    @if ($payment->reference)<span class="text-gray-400">· {{ $payment->reference }}</span>@endif
                                </div>
                                <form method="POST" action="{{ route('payments.destroy', [$invoice, $payment]) }}" onsubmit="return confirm('{{ __('Remove this payment?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($invoice->balanceDue() > 0)
                    <form method="POST" action="{{ route('payments.store', $invoice) }}" class="p-5 border-t border-gray-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-5 gap-3 items-end">
                        @csrf
                        <div class="col-span-2 sm:col-span-1">
                            <x-input-label for="amount" :value="__('Amount')" class="text-xs" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" max="{{ $invoice->balanceDue() }}" value="{{ $invoice->balanceDue() }}" class="mt-1 block w-full text-sm" required />
                        </div>
                        <div>
                            <x-input-label for="method" :value="__('Method')" class="text-xs" />
                            <select id="method" name="method" class="mt-1 block w-full text-sm border-gray-300 rounded-md">
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="bank_transfer">{{ __('Bank transfer') }}</option>
                                <option value="card">{{ __('Card') }}</option>
                                <option value="upi">{{ __('UPI') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="paid_at" :value="__('Date')" class="text-xs" />
                            <x-text-input id="paid_at" name="paid_at" type="date" value="{{ now()->toDateString() }}" class="mt-1 block w-full text-sm" required />
                        </div>
                        <div>
                            <x-input-label for="reference" :value="__('Reference')" class="text-xs" />
                            <x-text-input id="reference" name="reference" type="text" class="mt-1 block w-full text-sm" />
                        </div>
                        <div>
                            <x-primary-button class="w-full justify-center">{{ __('Record Payment') }}</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
