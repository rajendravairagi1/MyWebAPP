<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Quotation') }} {{ $quotation->number }}</h2>
            <x-status-badge :status="$quotation->status" class="text-sm" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('quotations.pdf', $quotation) }}" class="px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Download PDF') }}</a>

                @if ($quotation->status === 'draft')
                    <form method="POST" action="{{ route('quotations.mark-sent', $quotation) }}">
                        @csrf
                        <button class="px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Mark as Sent') }}</button>
                    </form>
                @endif

                @if ($quotation->invoices->isEmpty())
                    <form method="POST" action="{{ route('quotations.convert', $quotation) }}">
                        @csrf
                        <button class="px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">{{ __('Convert to Invoice') }}</button>
                    </form>
                @else
                    <a href="{{ route('invoices.show', $quotation->invoices->first()) }}" class="px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">
                        {{ __('View Invoice') }} {{ $quotation->invoices->first()->number }}
                    </a>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Customer') }}</div>
                    <a href="{{ route('customers.show', $quotation->customer) }}" class="text-accent-600 hover:underline">{{ $quotation->customer->name }}</a>
                </div>
                @if ($quotation->project)
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">{{ __('Project') }}</div>
                        <a href="{{ route('projects.show', $quotation->project) }}" class="text-accent-600 hover:underline">{{ $quotation->project->name }}</a>
                        @if ($quotation->projectUnit)
                            <span class="text-gray-500 dark:text-gray-400">· {{ $quotation->projectUnit->unit_number }}</span>
                        @endif
                    </div>
                @endif
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Valid until') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $quotation->valid_until?->format('d M Y') ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Created') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $quotation->created_at->format('d M Y') }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-3 text-left">{{ __('Item') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Qty') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Unit price') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Line total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($quotation->items as $item)
                            <tr>
                                <td class="px-5 py-3 text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ rtrim(rtrim($item->quantity, '0'), '.') }}</td>
                                <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 flex justify-end">
                    <table class="text-sm w-64">
                        <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Subtotal') }}</td><td class="py-1 text-right">{{ number_format($quotation->subtotal, 2) }}</td></tr>
                        <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Discount') }}</td><td class="py-1 text-right">{{ number_format($quotation->discount_total, 2) }}</td></tr>
                        <tr><td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Tax') }}</td><td class="py-1 text-right">{{ number_format($quotation->tax_total, 2) }}</td></tr>
                        <tr class="border-t border-gray-200 font-semibold"><td class="py-1">{{ __('Total') }}</td><td class="py-1 text-right">{{ number_format($quotation->total, 2) }}</td></tr>
                    </table>
                </div>
            </div>

            @if ($quotation->notes || $quotation->terms)
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 text-sm space-y-3">
                    @if ($quotation->notes)
                        <div><div class="text-gray-500 dark:text-gray-400">{{ __('Notes') }}</div><div class="text-gray-900 dark:text-gray-100">{{ $quotation->notes }}</div></div>
                    @endif
                    @if ($quotation->terms)
                        <div><div class="text-gray-500 dark:text-gray-400">{{ __('Terms') }}</div><div class="text-gray-900 dark:text-gray-100">{{ $quotation->terms }}</div></div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
