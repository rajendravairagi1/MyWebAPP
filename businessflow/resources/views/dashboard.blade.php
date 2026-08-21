<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Portfolio P&L across all projects --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ __('Portfolio — all projects') }}</div>
                    <a href="{{ route('projects.index') }}" class="text-xs text-indigo-600 hover:underline">{{ __('View projects →') }}</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Projects') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $projectCount }}</div>
                        <div class="text-xs text-gray-400">{{ $ongoingProjectCount }} {{ __('ongoing') }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total cost') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($portfolioCost, 0) }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Received') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($portfolioRevenue, 0) }}</div>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Profit / Loss') }}</div>
                        <div class="mt-1 text-xl font-semibold {{ $portfolioProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($portfolioProfit, 0) }}</div>
                    </div>
                </div>

                @if ($projects->isNotEmpty())
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-slate-700">
                        <x-project-chart :projects="$projects" />
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Customers') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $customerCount }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Sales this month') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($salesThisMonth, 2) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Unpaid invoices') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $unpaidCount }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($unpaidTotal, 2) }} {{ __('outstanding') }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Overdue') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $overdueCount > 0 ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">{{ $overdueCount }}</div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900">{{ __('+ Add Project') }}</a>
                <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('+ Add Customer') }}</a>
                <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('+ Create Quotation') }}</a>
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('+ Create Invoice') }}</a>
                <a href="{{ route('followups.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('+ Schedule Follow-up') }}</a>
            </div>

            @if ($dueFollowups->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Follow-ups due') }}</span>
                        <a href="{{ route('followups.index') }}" class="text-xs text-indigo-600 hover:underline">{{ __('View all →') }}</a>
                    </div>
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($dueFollowups as $followup)
                            <li class="px-5 py-3 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('customers.show', $followup->customer) }}" class="text-indigo-600 hover:underline">{{ $followup->customer->name }}</a>
                                    <span class="text-gray-500 dark:text-gray-400">— {{ $followup->note }}</span>
                                </div>
                                @if ($url = $followup->whatsappUrl())
                                    <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">{{ __('WhatsApp') }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Recent invoices') }}</div>
                @if ($recentInvoices->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No invoices yet — create your first one above.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($recentInvoices as $invoice)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">{{ $invoice->number }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $invoice->customer->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ number_format($invoice->total, 2) }}</td>
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
