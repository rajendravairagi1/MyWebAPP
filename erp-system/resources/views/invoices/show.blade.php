<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">Invoice {{ $invoice->invoice_number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="px-4 py-2 rounded bg-red-50 text-red-700 text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Bill To</p>
                            <p class="font-semibold text-lg">{{ $invoice->customer->name }}</p>
                            <p class="text-sm text-[var(--muted)]">{{ $invoice->customer->address }}</p>
                            <p class="text-sm text-[var(--muted)]">GST: {{ $invoice->customer->gst_number ?? '—' }}</p>
                        </div>
                        <div class="text-right text-sm">
                            <p><span class="text-[var(--muted)]">Invoice #:</span> <span class="font-mono">{{ $invoice->invoice_number }}</span></p>
                            <p><span class="text-[var(--muted)]">Date:</span> {{ $invoice->invoice_date->format('d M Y') }}</p>
                            <p><span class="text-[var(--muted)]">Due:</span> {{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
                            <p><span class="text-[var(--muted)]">Challan:</span> {{ $invoice->challan_number ?? '—' }}</p>
                            @php
                                $pill = match($invoice->status) {
                                    'unpaid' => 'bg-red-100 text-red-700',
                                    'partial' => 'bg-amber-100 text-amber-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                };
                            @endphp
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }}">{{ ucfirst($invoice->status) }}</span>
                        </div>
                    </div>

                    <table class="w-full text-sm border rounded-lg overflow-hidden">
                        <thead class="bg-[var(--bg)]">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Product</th>
                                <th class="px-3 py-2 text-right font-semibold text-[var(--muted)]">Qty</th>
                                <th class="px-3 py-2 text-right font-semibold text-[var(--muted)]">Rate</th>
                                <th class="px-3 py-2 text-right font-semibold text-[var(--muted)]">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border)]">
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td class="px-3 py-2">{{ $item->product->name }}</td>
                                    <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                                    <td class="px-3 py-2 text-right">₹{{ number_format($item->rate, 2) }}</td>
                                    <td class="px-3 py-2 text-right">₹{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex justify-end mt-4">
                        <div class="w-64 text-sm space-y-1">
                            <div class="flex justify-between"><span class="text-[var(--muted)]">Subtotal</span><span>₹{{ number_format($invoice->subtotal, 2) }}</span></div>
                            <div class="flex justify-between"><span class="text-[var(--muted)]">CGST (9%)</span><span>₹{{ number_format($invoice->cgst_amount, 2) }}</span></div>
                            <div class="flex justify-between"><span class="text-[var(--muted)]">SGST (9%)</span><span>₹{{ number_format($invoice->sgst_amount, 2) }}</span></div>
                            <div class="flex justify-between font-bold text-base border-t pt-1"><span>Total</span><span>₹{{ number_format($invoice->total, 2) }}</span></div>
                            <div class="flex justify-between text-green-700"><span>Paid</span><span>₹{{ number_format($invoice->totalPaid(), 2) }}</span></div>
                            <div class="flex justify-between font-semibold {{ $invoice->balanceDue() > 0 ? 'text-red-600' : 'text-[var(--muted)]' }}"><span>Balance Due</span><span>₹{{ number_format($invoice->balanceDue(), 2) }}</span></div>
                        </div>
                    </div>

                    @if ($invoice->vehicle_number)
                        <p class="text-xs text-[var(--muted)] mt-4 border-t pt-3">
                            Vehicle: {{ $invoice->vehicle_number }} · Driver: {{ $invoice->driver_name }} ({{ $invoice->driver_mobile }})
                        </p>
                    @endif
                </div>
            </div>

            @if ($invoice->balanceDue() > 0)
                <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-[var(--text)]">
                        <h3 class="font-semibold text-[var(--text)] mb-4">Record Payment</h3>
                        <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-[var(--muted)] mb-1">Amount (₹)</label>
                                <input type="number" step="0.01" name="amount" max="{{ $invoice->balanceDue() }}" value="{{ $invoice->balanceDue() }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[var(--muted)] mb-1">Date</label>
                                <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[var(--muted)] mb-1">Method</label>
                                <select name="method" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="upi">UPI</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if ($invoice->payments->count())
                <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-[var(--text)]">
                        <h3 class="font-semibold text-[var(--text)] mb-3">Payment History</h3>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-[var(--border)]">
                                @foreach ($invoice->payments as $payment)
                                    <tr>
                                        <td class="py-1.5">{{ $payment->paid_at->format('d M Y') }}</td>
                                        <td class="py-1.5 text-[var(--muted)]">{{ str($payment->method)->replace('_', ' ')->title() }}</td>
                                        <td class="py-1.5 text-right font-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <a href="{{ route('jobs.show', $invoice->job) }}" class="text-sm text-[var(--muted)] hover:underline">&larr; Back to Job #{{ $invoice->job_order_id }}</a>
        </div>
    </div>
</x-app-layout>
