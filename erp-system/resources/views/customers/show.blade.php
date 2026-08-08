<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ $customer->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <div class="flex justify-between items-start mb-4">
                        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                            <div><span class="text-[var(--muted)]">GST:</span> {{ $customer->gst_number ?? '—' }}</div>
                            <div><span class="text-[var(--muted)]">Payment Terms:</span> {{ $customer->payment_terms ?? '—' }}</div>
                            <div><span class="text-[var(--muted)]">Contact:</span> {{ $customer->contact_person ?? '—' }}</div>
                            <div><span class="text-[var(--muted)]">Mobile:</span> {{ $customer->mobile ?? '—' }}</div>
                            <div><span class="text-[var(--muted)]">Email:</span> {{ $customer->email ?? '—' }}</div>
                            <div><span class="text-[var(--muted)]">Opening Balance:</span> ₹{{ number_format($customer->opening_balance, 2) }}</div>
                        </div>
                        <a href="{{ route('customers.edit', $customer) }}" class="text-sm text-[var(--brand-600)] hover:underline">Edit</a>
                    </div>
                    @if ($customer->address)
                        <p class="text-sm text-[var(--muted)] border-t pt-3">{{ $customer->address }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-[var(--text)]">Products ({{ $customer->products->count() }})</h3>
                        <a href="{{ route('products.create') }}" class="text-sm text-[var(--brand-600)] hover:underline">+ Add Product</a>
                    </div>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full text-sm divide-y divide-[var(--border)]">
                            <thead class="bg-[var(--bg)]">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Code</th>
                                    <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Name</th>
                                    <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Unit</th>
                                    <th class="px-3 py-2 text-right font-semibold text-[var(--muted)]">Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                @forelse ($customer->products as $product)
                                    <tr>
                                        <td class="px-3 py-2">{{ $product->product_code }}</td>
                                        <td class="px-3 py-2">{{ $product->name }}</td>
                                        <td class="px-3 py-2">{{ $product->unit }}</td>
                                        <td class="px-3 py-2 text-right">₹{{ number_format($product->rate, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-3 py-4 text-center text-[var(--muted)]">Koi product nahi hai.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <a href="{{ route('customers.index') }}" class="text-sm text-[var(--muted)] hover:underline">&larr; Back to Customers</a>
        </div>
    </div>
</x-app-layout>
