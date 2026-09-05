<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="history.back()" class="shrink-0 inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    {{ __('Back') }}
                </button>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ $account->name }}</h2>
                @if ($account->isCash())
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">{{ __('Cash-in-hand') }}</span>
                @else
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">{{ __('Bank') }}</span>
                @endif
            </div>
            <a href="{{ route('payment-accounts.statement', $account) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Download Statement') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($account->bank_name || $account->maskedAccountNumber() || $account->notes)
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if ($account->bank_name) {{ $account->bank_name }} @endif
                    @if ($account->maskedAccountNumber()) · {{ $account->maskedAccountNumber() }} @endif
                    @if ($account->notes) · {{ $account->notes }} @endif
                </p>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase text-gray-400">{{ __('Total In') }}</div>
                    <div class="mt-1 text-2xl font-bold text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalIn, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase text-gray-400">{{ __('Total Out') }}</div>
                    <div class="mt-1 text-2xl font-bold text-red-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalOut, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase text-gray-400">{{ $account->isCash() ? __('Cash currently held') : __('Net balance') }}</div>
                    <div class="mt-1 text-2xl font-bold {{ $balance >= 0 ? 'text-gray-900 dark:text-gray-100' : 'text-red-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($balance, 0) }}</div>
                </div>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Every payment recorded against this account/person — money that came in and money paid out from it.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($rows->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No transactions recorded against this account yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-3 text-left">{{ __('Date') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Description') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Context') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Party') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('In') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Out') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($rows as $row)
                                    <tr>
                                        <td class="px-5 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $row->date?->format('d M Y') }}</td>
                                        <td class="px-5 py-3 text-gray-900 dark:text-gray-100">
                                            @if ($row->link)
                                                <a href="{{ $row->link }}" class="text-accent-600 hover:underline">{{ $row->description }}</a>
                                            @else
                                                {{ $row->description }}
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $row->context ?: '—' }}</td>
                                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $row->party ?: '—' }}</td>
                                        <td class="px-5 py-3 text-right font-medium text-green-600">{{ $row->direction === 'in' ? \App\Support\Tenant::currencySymbol().number_format($row->amount, 0) : '' }}</td>
                                        <td class="px-5 py-3 text-right font-medium text-red-600">{{ $row->direction === 'out' ? \App\Support\Tenant::currencySymbol().number_format($row->amount, 0) : '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
