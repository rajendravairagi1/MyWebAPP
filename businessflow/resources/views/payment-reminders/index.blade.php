<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Payment Reminders') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Every customer with money still due, in one place — one click opens WhatsApp with the reminder message already filled in for you to send.') }}</p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700">
                    <span class="font-medium text-red-600">{{ __('Overdue') }} ({{ $overdueInvoices->count() }})</span>
                </div>
                @include('payment-reminders._table', ['invoices' => $overdueInvoices, 'emptyText' => __('Nothing overdue right now.')])
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700">
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Due (not yet overdue)') }} ({{ $dueSoonInvoices->count() }})</span>
                </div>
                @include('payment-reminders._table', ['invoices' => $dueSoonInvoices, 'emptyText' => __('Nothing pending here.')])
            </div>
        </div>
    </div>
</x-app-layout>
