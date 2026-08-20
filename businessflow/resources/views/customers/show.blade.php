<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }}</h2>
            <a href="{{ route('customers.edit', $customer) }}" class="text-sm text-indigo-600 hover:underline">{{ __('Edit') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">{{ __('Company') }}</div>
                    <div class="text-gray-900">{{ $customer->company ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">{{ __('Phone') }}</div>
                    <div class="text-gray-900">{{ $customer->phone ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">{{ __('Email') }}</div>
                    <div class="text-gray-900">{{ $customer->email ?: '—' }}</div>
                </div>
                <div class="sm:col-span-3">
                    <div class="text-gray-500">{{ __('Address') }}</div>
                    <div class="text-gray-900">{{ $customer->address ?: '—' }}</div>
                </div>
                @if ($customer->notes)
                    <div class="sm:col-span-3">
                        <div class="text-gray-500">{{ __('Notes') }}</div>
                        <div class="text-gray-900">{{ $customer->notes }}</div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                        <span class="font-medium text-gray-800">{{ __('Quotations') }}</span>
                        <a href="{{ route('quotations.create') }}" class="text-xs text-indigo-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($customer->quotations->isEmpty())
                        <div class="p-5 text-sm text-gray-500">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($customer->quotations as $quotation)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-indigo-600 hover:underline">{{ $quotation->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600">{{ number_format($quotation->total, 2) }}</span>
                                        <x-status-badge :status="$quotation->status" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                        <span class="font-medium text-gray-800">{{ __('Invoices') }}</span>
                        <a href="{{ route('invoices.create') }}" class="text-xs text-indigo-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($customer->invoices->isEmpty())
                        <div class="p-5 text-sm text-gray-500">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($customer->invoices as $invoice)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">{{ $invoice->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600">{{ number_format($invoice->total, 2) }}</span>
                                        <x-status-badge :status="$invoice->status" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
