<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Customers') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-600">Total {{ $customers->total() }} customers.</p>
                        <a href="{{ route('customers.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            + Naya Customer
                        </a>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">GST</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Contact</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Payment Terms</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Opening Balance</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Products</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($customers as $customer)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $customer->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $customer->gst_number ?? '—' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $customer->contact_person }} {{ $customer->mobile ? '· '.$customer->mobile : '' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $customer->payment_terms ?? '—' }}</td>
                                        <td class="px-4 py-2 text-right">₹{{ number_format($customer->opening_balance, 2) }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $customer->products_count }}</td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:underline">View</a>
                                            &middot;
                                            <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:underline">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Koi customer nahi hai abhi.</td></tr>
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
