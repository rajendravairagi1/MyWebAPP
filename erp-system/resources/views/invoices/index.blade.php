<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Invoices') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex gap-2 mb-4">
                        <a href="{{ route('invoices.index') }}"
                           class="px-3 py-1.5 rounded text-xs font-semibold {{ ! $status ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600' }}">All</a>
                        @foreach (['unpaid' => 'Unpaid', 'partial' => 'Partial', 'paid' => 'Paid'] as $key => $label)
                            <a href="{{ route('invoices.index', ['status' => $key]) }}"
                               class="px-3 py-1.5 rounded text-xs font-semibold {{ $status === $key ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $label }}</a>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Invoice #</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Customer</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Due</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Total</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($invoices as $invoice)
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('invoices.show', $invoice) }}'">
                                        <td class="px-4 py-2 font-mono text-xs">{{ $invoice->invoice_number }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $invoice->customer->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</td>
                                        <td class="px-4 py-2 {{ $invoice->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                            {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                                            @if ($invoice->isOverdue()) (Overdue) @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">₹{{ number_format($invoice->total, 2) }}</td>
                                        <td class="px-4 py-2">
                                            @php
                                                $pill = match($invoice->status) {
                                                    'unpaid' => 'bg-red-100 text-red-700',
                                                    'partial' => 'bg-amber-100 text-amber-700',
                                                    'paid' => 'bg-green-100 text-green-700',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }}">{{ ucfirst($invoice->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Koi invoice nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $invoices->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
