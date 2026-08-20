<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Invoices') }}</h2>
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900">{{ __('+ New Invoice') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="flex gap-2 text-sm">
                @foreach (['' => 'All', 'sent' => 'Sent', 'partially_paid' => 'Partially paid', 'paid' => 'Paid', 'overdue' => 'Overdue'] as $value => $label)
                    <a href="{{ route('invoices.index', $value ? ['status' => $value] : []) }}"
                        class="px-3 py-1 rounded-full border {{ request('status', '') === $value ? 'bg-gray-800 text-white border-gray-800' : 'border-gray-300 text-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if ($invoices->isEmpty())
                    <div class="p-6 text-sm text-gray-500">{{ __('No invoices yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Number') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Customer') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Total') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Balance due') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Due') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline font-medium">{{ $invoice->number }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $invoice->customer->name }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600">{{ number_format($invoice->total, 2) }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600">{{ $invoice->balanceDue() }}</td>
                                    <td class="px-5 py-3"><x-status-badge :status="$invoice->status" /></td>
                                    <td class="px-5 py-3 text-gray-500">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>
