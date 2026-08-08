<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Products') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-[var(--muted)]">Total {{ $products->total() }} products.</p>
                        <a href="{{ route('products.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            + Naya Product
                        </a>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-[var(--border)] text-sm">
                            <thead class="bg-[var(--bg)]">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Code</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Company</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Unit</th>
                                    <th class="px-4 py-2 text-right font-semibold text-[var(--muted)]">Rate</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="px-4 py-2 font-mono text-xs">{{ $product->product_code }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $product->name }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $product->customer->name }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $product->unit }}</td>
                                        <td class="px-4 py-2 text-right">₹{{ number_format($product->rate, 2) }}</td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('products.edit', $product) }}" class="text-[var(--brand-600)] hover:underline">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-[var(--muted)]">Koi product nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
