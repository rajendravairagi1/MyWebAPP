<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase #{{ $purchase->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="px-4 py-2 rounded bg-red-50 text-red-700 text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Supplier</p>
                            <p class="font-semibold text-lg">{{ $purchase->supplier->name }}</p>
                            <p class="text-sm text-gray-500">GST: {{ $purchase->supplier->gst_number ?? '—' }}</p>
                        </div>
                        <div class="text-right text-sm">
                            <p><span class="text-gray-500">Date:</span> {{ $purchase->purchase_date->format('d M Y') }}</p>
                            <p><span class="text-gray-500">Bill #:</span> {{ $purchase->bill_number ?? '—' }}</p>
                            @php
                                $pill = match($purchase->status) {
                                    'unpaid' => 'bg-red-100 text-red-700',
                                    'partial' => 'bg-amber-100 text-amber-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                };
                            @endphp
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }}">{{ ucfirst($purchase->status) }}</span>
                        </div>
                    </div>

                    <table class="w-full text-sm border rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Material</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600">Qty</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600">Rate</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-3 py-2">{{ $purchase->rawMaterial->name }}</td>
                                <td class="px-3 py-2 text-right">{{ $purchase->quantity }} {{ $purchase->rawMaterial->unit }}</td>
                                <td class="px-3 py-2 text-right">₹{{ number_format($purchase->rate, 2) }}</td>
                                <td class="px-3 py-2 text-right">₹{{ number_format($purchase->amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex justify-end mt-4">
                        <div class="w-64 text-sm space-y-1">
                            <div class="flex justify-between font-bold text-base border-t pt-1"><span>Total</span><span>₹{{ number_format($purchase->amount, 2) }}</span></div>
                            <div class="flex justify-between text-green-700"><span>Paid</span><span>₹{{ number_format($purchase->totalPaid(), 2) }}</span></div>
                            <div class="flex justify-between font-semibold {{ $purchase->balanceDue() > 0 ? 'text-red-600' : 'text-gray-500' }}"><span>Balance Due</span><span>₹{{ number_format($purchase->balanceDue(), 2) }}</span></div>
                        </div>
                    </div>

                    @if ($purchase->notes)
                        <p class="text-xs text-gray-500 mt-4 border-t pt-3">{{ $purchase->notes }}</p>
                    @endif
                </div>
            </div>

            @if ($purchase->balanceDue() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-gray-800 mb-4">Record Payment</h3>
                        <form method="POST" action="{{ route('purchases.payments.store', $purchase) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Amount (₹)</label>
                                <input type="number" step="0.01" name="amount" max="{{ $purchase->balanceDue() }}" value="{{ $purchase->balanceDue() }}" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                                <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Method</label>
                                <select name="method" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
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

            @if ($purchase->payments->count())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-gray-800 mb-3">Payment History</h3>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($purchase->payments as $payment)
                                    <tr>
                                        <td class="py-1.5">{{ $payment->paid_at->format('d M Y') }}</td>
                                        <td class="py-1.5 text-gray-500">{{ str($payment->method)->replace('_', ' ')->title() }}</td>
                                        <td class="py-1.5 text-right font-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <a href="{{ route('purchases.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Back to Purchases</a>
        </div>
    </div>
</x-app-layout>
