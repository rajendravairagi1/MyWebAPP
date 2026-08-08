<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Customers') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-[var(--muted)]">Total {{ $customers->total() }} customers.</p>
                        <a href="{{ route('customers.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            + Naya Customer
                        </a>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-[var(--border)] text-sm">
                            <thead class="bg-[var(--bg)]">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">GST</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Contact</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Payment Terms</th>
                                    <th class="px-4 py-2 text-right font-semibold text-[var(--muted)]">Opening Balance</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Products</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                @forelse ($customers as $customer)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $customer->name }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $customer->gst_number ?? '—' }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $customer->contact_person }} {{ $customer->mobile ? '· '.$customer->mobile : '' }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $customer->payment_terms ?? '—' }}</td>
                                        <td class="px-4 py-2 text-right">₹{{ number_format($customer->opening_balance, 2) }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $customer->products_count }}</td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('customers.show', $customer) }}" class="text-[var(--brand-600)] hover:underline">View</a>
                                            &middot;
                                            <a href="{{ route('customers.edit', $customer) }}" class="text-[var(--brand-600)] hover:underline">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-6 text-center text-[var(--muted)]">Koi customer nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $customers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
