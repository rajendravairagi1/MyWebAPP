<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('Customers') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $customerCount }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('Sales this month') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($salesThisMonth, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('Unpaid invoices') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $unpaidCount }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ number_format($unpaidTotal, 2) }} {{ __('outstanding') }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('Overdue') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $overdueCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $overdueCount }}</div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900">{{ __('+ Add Customer') }}</a>
                <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">{{ __('+ Create Quotation') }}</a>
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">{{ __('+ Create Invoice') }}</a>
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">{{ __('+ Add Product') }}</a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 font-medium text-gray-800">{{ __('Recent invoices') }}</div>
                @if ($recentInvoices->isEmpty())
                    <div class="p-5 text-sm text-gray-500">{{ __('No invoices yet — create your first one above.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentInvoices as $invoice)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">{{ $invoice->number }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $invoice->customer->name }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ number_format($invoice->total, 2) }}</td>
                                    <td class="px-5 py-3">
                                        <x-status-badge :status="$invoice->status" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
