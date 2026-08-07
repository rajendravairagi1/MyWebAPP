<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Suppliers') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-600">Total {{ $suppliers->total() }} suppliers.</p>
                        <div class="flex gap-2">
                            <a href="{{ route('purchases.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-md font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-50">
                                Purchases &rarr;
                            </a>
                            <a href="{{ route('suppliers.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                + Naya Supplier
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">GST</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Phone</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Purchases</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($suppliers as $supplier)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $supplier->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $supplier->gst_number ?? '—' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $supplier->phone ?? '—' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $supplier->purchases_count }}</td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-[var(--brand-600)] hover:underline">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Koi supplier nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $suppliers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
