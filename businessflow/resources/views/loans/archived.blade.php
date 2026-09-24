<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Archived Loans') }}</h2>
            <a href="{{ route('loans.index') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                {{ __('Back to Bank Loans') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Loans removed from the main list end up here instead of being deleted — every disbursement, receipt invoice and document on them stays exactly as it was. Restore brings one back, or delete it here for good.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($loans->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No archived loans.') }}</div>
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
                                    <th class="px-5 py-3 text-left">{{ __('Removed on') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($loans as $loan)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $loan->customer->name }}</td>
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
                                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $loan->deleted_at->format('d M Y') }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <form method="POST" action="{{ route('loans.restore', $loan->id) }}">
                                                    @csrf
                                                    <button class="text-xs text-accent-600 hover:underline whitespace-nowrap">{{ __('Restore') }}</button>
                                                </form>
                                                <form method="POST" action="{{ route('loans.destroy-permanent', $loan->id) }}" onsubmit="return confirm('{{ __('Permanently delete this loan? This cannot be undone — disbursements already recorded stay in the payment ledger, just no longer grouped under this loan.') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-xs text-red-600 hover:underline whitespace-nowrap">{{ __('Delete Forever') }}</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{ $loans->links() }}
        </div>
    </div>
</x-app-layout>
