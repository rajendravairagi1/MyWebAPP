<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Purchases') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <div class="flex gap-2 text-sm">
                            <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 rounded-md {{ ! $status ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-gray-500 hover:bg-gray-50' }}">All</a>
                            <a href="{{ route('purchases.index', ['status' => 'unpaid']) }}" class="px-3 py-1.5 rounded-md {{ $status === 'unpaid' ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-gray-500 hover:bg-gray-50' }}">Unpaid</a>
                            <a href="{{ route('purchases.index', ['status' => 'partial']) }}" class="px-3 py-1.5 rounded-md {{ $status === 'partial' ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-gray-500 hover:bg-gray-50' }}">Partial</a>
                            <a href="{{ route('purchases.index', ['status' => 'paid']) }}" class="px-3 py-1.5 rounded-md {{ $status === 'paid' ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-gray-500 hover:bg-gray-50' }}">Paid</a>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('suppliers.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-md font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-50">
                                Suppliers
                            </a>
                            <a href="{{ route('purchases.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                + Nayi Purchase
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Supplier</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Material</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Qty</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Amount</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-500">{{ $purchase->purchase_date->format('d M Y') }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $purchase->supplier->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $purchase->rawMaterial->name }}</td>
                                        <td class="px-4 py-2 text-right">{{ $purchase->quantity }} {{ $purchase->rawMaterial->unit }}</td>
                                        <td class="px-4 py-2 text-right">₹{{ number_format($purchase->amount, 2) }}</td>
                                        <td class="px-4 py-2">
                                            @php
                                                $pill = match($purchase->status) {
                                                    'unpaid' => 'bg-red-100 text-red-700',
                                                    'partial' => 'bg-amber-100 text-amber-700',
                                                    'paid' => 'bg-green-100 text-green-700',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }}">{{ ucfirst($purchase->status) }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('purchases.show', $purchase) }}" class="text-[var(--brand-600)] hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Koi purchase entry nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $purchases->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
