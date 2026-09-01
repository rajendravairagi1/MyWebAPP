<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Bank Loans') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Every bank loan across every customer — who took how much, how much has actually come in, and how much is still owed to be disbursed. Click a row for the full statement, disbursements and documents.') }}
            </p>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Loans') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totals['count'] }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Sanctioned') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['sanctioned'], 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Disbursed') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['disbursed'], 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Remaining') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $totals['remaining'] > 0 ? 'text-amber-600' : 'text-gray-900 dark:text-gray-100' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['remaining'], 0) }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($loans->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No bank loans recorded yet — add one from a customer\'s property page.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-3 text-left">{{ __('Customer') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Property') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Bank') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Sanctioned') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Disbursed') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Remaining') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Progress') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($loans as $loan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40 cursor-pointer" onclick="window.location='{{ route('loans.show', $loan) }}'">
                                        <td class="px-5 py-3">
                                            <a href="{{ route('loans.show', $loan) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $loan->customer->name }}</a>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            @if ($loan->unit)
                                                {{ $loan->unit->project->name }} · {{ $loan->unit->unit_number }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $loan->bank_name }}
                                            @if ($loan->loan_account_number)
                                                <div class="text-xs text-gray-400">A/C {{ $loan->loan_account_number }}</div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->sanctioned_amount, 0) }}</td>
                                        <td class="px-5 py-3 text-right text-green-600 font-medium">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalDisbursed(), 0) }}</td>
                                        <td class="px-5 py-3 text-right {{ $loan->remainingToDisburse() > 0 ? 'text-amber-600' : 'text-gray-400' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->remainingToDisburse(), 0) }}</td>
                                        <td class="px-5 py-3 w-32">
                                            <div class="h-1.5 rounded-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                                                <div class="h-full bg-blue-500" style="width: {{ $loan->percentDisbursed() }}%"></div>
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1">{{ $loan->percentDisbursed() }}%</div>
                                        </td>
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
