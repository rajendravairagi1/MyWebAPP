@props(['unit'])

@if ($unit->loan)
    @php $loan = $unit->loan; @endphp
    <div class="mt-3 rounded-lg border border-blue-100 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-900/10 p-3">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="text-xs font-medium text-blue-800 dark:text-blue-300">
                {{ __('Bank Loan') }} — {{ $loan->bank_name }}
                @if ($loan->loan_account_number)
                    <span class="text-blue-400 dark:text-blue-500 font-normal">· A/C {{ $loan->loan_account_number }}</span>
                @endif
            </div>
            <a href="{{ route('loans.show', $loan) }}" class="text-xs text-accent-600 hover:underline shrink-0">{{ __('Manage') }}</a>
        </div>
        <div class="mt-2 h-1.5 rounded-full bg-blue-100 dark:bg-blue-950 overflow-hidden">
            <div class="h-full bg-blue-500" style="width: {{ $loan->percentDisbursed() }}%"></div>
        </div>
        <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-blue-700 dark:text-blue-400">
            <span>{{ __('Sanctioned') }}: <strong>{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->sanctioned_amount, 0) }}</strong></span>
            <span>{{ __('Disbursed') }}: <strong>{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalDisbursed(), 0) }}</strong> ({{ $loan->percentDisbursed() }}%)</span>
            <span>{{ __('Remaining') }}: <strong>{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->remainingToDisburse(), 0) }}</strong></span>
            @if ($loan->interest_rate)
                <span>{{ __('Interest') }}: <strong>{{ rtrim(rtrim(number_format($loan->interest_rate, 2), '0'), '.') }}%</strong></span>
            @endif
        </div>
    </div>
@else
    <div class="mt-3">
        <a href="{{ route('loans.create', $unit) }}" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" /></svg>
            {{ __('+ Bank Loan') }}
        </a>
    </div>
@endif
