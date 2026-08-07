<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Invoice Banao') }} — Job #{{ $job->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6 border rounded-lg p-4 bg-gray-50 text-sm">
                        <p><span class="text-gray-500">Customer:</span> <strong>{{ $job->customer->name }}</strong></p>
                        <table class="w-full mt-3 text-sm">
                            <thead>
                                <tr class="text-gray-500 text-xs uppercase">
                                    <th class="text-left">Product</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Rate</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $subtotal = 0; @endphp
                                @foreach ($job->items as $item)
                                    @php $amount = $item->quantity * $item->product->rate; $subtotal += $amount; @endphp
                                    <tr>
                                        <td class="py-1">{{ $item->product->name }}</td>
                                        <td class="text-right py-1">{{ $item->quantity }}</td>
                                        <td class="text-right py-1">₹{{ number_format($item->product->rate, 2) }}</td>
                                        <td class="text-right py-1">₹{{ number_format($amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="border-t mt-2 pt-2 text-right space-y-0.5">
                            <p>Subtotal: ₹{{ number_format($subtotal, 2) }}</p>
                            <p>CGST (9%): ₹{{ number_format($subtotal * 0.09, 2) }}</p>
                            <p>SGST (9%): ₹{{ number_format($subtotal * 0.09, 2) }}</p>
                            <p class="font-bold text-base">Total: ₹{{ number_format($subtotal * 1.18, 2) }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('invoices.store', $job) }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Challan Number</label>
                                <input type="text" name="challan_number" value="{{ old('challan_number', $job->challan_number) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date *</label>
                                <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <p class="text-xs text-gray-400 mt-1">Customer payment terms: {{ $job->customer->payment_terms ?? '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Number</label>
                                <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Driver Name</label>
                                <input type="text" name="driver_name" value="{{ old('driver_name') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Driver Mobile</label>
                                <input type="text" name="driver_mobile" value="{{ old('driver_mobile') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Generate Invoice
                        </button>
                        <a href="{{ route('jobs.show', $job) }}" class="ml-3 text-sm text-gray-500 hover:underline">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
