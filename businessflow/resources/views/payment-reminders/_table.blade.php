@if ($invoices->isEmpty())
    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ $emptyText }}</div>
@else
    <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
            <tr>
                <th class="px-5 py-3 text-left">{{ __('Customer') }}</th>
                <th class="px-5 py-3 text-left">{{ __('Invoice') }}</th>
                <th class="px-5 py-3 text-right">{{ __('Balance due') }}</th>
                <th class="px-5 py-3 text-left">{{ __('Due') }}</th>
                <th class="px-5 py-3 text-right">{{ __('Reminder') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
            @foreach ($invoices as $invoice)
                <tr>
                    <td class="px-5 py-3">
                        <a href="{{ route('customers.show', $invoice->customer) }}" class="text-accent-600 hover:underline">{{ $invoice->customer->name }}</a>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-accent-600 hover:underline">{{ $invoice->number }}</a>
                    </td>
                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ \App\Support\Tenant::currencySymbol() }}{{ $invoice->balanceDue() }}</td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">
                        @if ($invoice->due_date)
                            {{ $invoice->due_date->format('d M Y') }}
                            @if ($invoice->due_date->isPast())
                                <span class="text-red-500 text-xs">({{ __(':days days overdue', ['days' => (int) $invoice->due_date->diffInDays(now())]) }})</span>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if ($url = $invoice->whatsappReminderUrl())
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">{{ __('WhatsApp') }}</a>
                        @else
                            <span class="text-xs text-gray-400">{{ __('No phone number') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
@endif
