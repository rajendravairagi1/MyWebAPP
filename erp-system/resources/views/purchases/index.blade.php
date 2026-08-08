<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Purchases') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <div class="flex gap-2 text-sm">
                            <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 rounded-md {{ ! $status ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-[var(--muted)] hover:bg-[var(--bg)]' }}">All</a>
                            <a href="{{ route('purchases.index', ['status' => 'unpaid']) }}" class="px-3 py-1.5 rounded-md {{ $status === 'unpaid' ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-[var(--muted)] hover:bg-[var(--bg)]' }}">Unpaid</a>
                            <a href="{{ route('purchases.index', ['status' => 'partial']) }}" class="px-3 py-1.5 rounded-md {{ $status === 'partial' ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-[var(--muted)] hover:bg-[var(--bg)]' }}">Partial</a>
                            <a href="{{ route('purchases.index', ['status' => 'paid']) }}" class="px-3 py-1.5 rounded-md {{ $status === 'paid' ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-[var(--muted)] hover:bg-[var(--bg)]' }}">Paid</a>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('suppliers.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-[var(--border)] rounded-md font-semibold text-xs text-[var(--muted)] uppercase tracking-widest hover:bg-[var(--bg)]">
                                Suppliers
                            </a>
                            <a href="{{ route('purchases.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                + Nayi Purchase
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-[var(--border)] text-sm">
                            <thead class="bg-[var(--bg)]">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Date</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Supplier</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Material</th>
                                    <th class="px-4 py-2 text-right font-semibold text-[var(--muted)]">Qty</th>
                                    <th class="px-4 py-2 text-right font-semibold text-[var(--muted)]">Amount</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $purchase->purchase_date->format('d M Y') }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $purchase->supplier->name }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $purchase->rawMaterial->name }}</td>
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
                                    <tr><td colspan="7" class="px-4 py-6 text-center text-[var(--muted)]">Koi purchase entry nahi hai abhi.</td></tr>
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
