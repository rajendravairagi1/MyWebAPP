<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quotations') }}</h2>
            <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900">{{ __('+ New Quotation') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if ($quotations->isEmpty())
                    <div class="p-6 text-sm text-gray-500">{{ __('No quotations yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Number') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Customer') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Total') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($quotations as $quotation)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('quotations.show', $quotation) }}" class="text-indigo-600 hover:underline font-medium">{{ $quotation->number }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $quotation->customer->name }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600">{{ number_format($quotation->total, 2) }}</td>
                                    <td class="px-5 py-3"><x-status-badge :status="$quotation->status" /></td>
                                    <td class="px-5 py-3 text-gray-500">{{ $quotation->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{ $quotations->links() }}
        </div>
    </div>
</x-app-layout>
