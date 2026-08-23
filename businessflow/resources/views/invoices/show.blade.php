<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Invoice') }} {{ $invoice->number }}</h2>
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
                            <th class="px-5 py-3 text-right">{{ __('Unit price') }}</th>
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
