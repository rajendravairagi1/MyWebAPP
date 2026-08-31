@props(['unit', 'accounts' => null])

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
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'loan-{{ $unit->id }}')" class="text-xs text-accent-600 hover:underline shrink-0">{{ __('Manage') }}</button>
        </div>
        <div class="mt-2 h-1.5 rounded-full bg-blue-100 dark:bg-blue-950 overflow-hidden">
            <div class="h-full bg-blue-500" style="width: {{ $loan->percentDisbursed() }}%"></div>
        </div>
        <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-blue-700 dark:text-blue-400">
            <span>{{ __('Sanctioned') }}: <strong>{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->sanctioned_amount, 0) }}</strong></span>
            <span>{{ __('Disbursed') }}: <strong>{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalDisbursed(), 0) }}</strong> ({{ $loan->percentDisbursed() }}%)</span>
            <span>{{ __('Remaining') }}: <strong>{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->remainingToDisburse(), 0) }}</strong></span>
        </div>
    </div>
@else
    <div class="mt-3">
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'loan-{{ $unit->id }}')" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" /></svg>
            {{ __('+ Bank Loan') }}
        </button>
    </div>
@endif

<x-modal name="loan-{{ $unit->id }}" max-width="md">
    <div class="p-6">
        @if ($unit->loan)
            @php $loan = $unit->loan; @endphp
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Bank Loan') }}</h2>
                <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('{{ __('Remove this loan record? Disbursements already recorded stay in the payment ledger.') }}')">
                    @csrf
                    @method('DELETE')
                    <button class="text-xs text-red-600 hover:underline">{{ __('Remove loan') }}</button>
                </form>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                    <div class="text-xs text-gray-400">{{ __('Sanctioned') }}</div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->sanctioned_amount, 0) }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                    <div class="text-xs text-gray-400">{{ __('Remaining to disburse') }}</div>
                    <div class="text-lg font-semibold {{ $loan->remainingToDisburse() > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->remainingToDisburse(), 0) }}</div>
                </div>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mb-4">
                <div>{{ __('Bank') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->bank_name }}</span></div>
                @if ($loan->loan_account_number)
                    <div>{{ __('Loan A/C No.') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->loan_account_number }}</span></div>
                @endif
                @if ($loan->sanctioned_at)
                    <div>{{ __('Sanctioned on') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->sanctioned_at->format('d M Y') }}</span></div>
                @endif
                <div>{{ __('Disbursed so far') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->percentDisbursed() }}%</span></div>
                @if ($loan->notes)
                    <div class="pt-1">{{ $loan->notes }}</div>
                @endif
            </div>

            @if ($loan->disbursements->isNotEmpty())
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Disbursements') }} ({{ $loan->disbursements->count() }})</div>
                <div class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                    @foreach ($loan->disbursements as $disbursement)
                        <div class="flex items-center justify-between text-sm bg-gray-50 dark:bg-slate-700/40 rounded-md px-3 py-2">
                            <div>
                                <div class="text-gray-800 dark:text-gray-200">{{ $disbursement->paid_at->format('d M Y') }}</div>
                                @if ($disbursement->reference)
                                    <div class="text-xs text-gray-400">{{ __('Ref') }}: {{ $disbursement->reference }}</div>
                                @endif
                                @if ($disbursement->account)
                                    <div class="text-xs text-gray-400">{{ __('Received in') }}: {{ $disbursement->account->name }}</div>
                                @endif
                            </div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($disbursement->amount, 0) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('loans.disbursements.store', $loan) }}" class="border-t border-gray-100 dark:border-slate-700 pt-4 space-y-3">
                @csrf
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('+ Add Disbursement') }}</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label :value="__('Amount')" />
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="{{ \App\Support\Tenant::currencySymbol() }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label :value="__('Date')" />
                        <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                </div>
                <input type="text" name="reference" placeholder="{{ __('Reference (optional)') }}" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                @if ($accounts && $accounts->isNotEmpty())
                    <select name="payment_account_id" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('Received in (optional)') }}</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="flex justify-end">
                    <x-primary-button>{{ __('Add Disbursement') }}</x-primary-button>
                </div>
            </form>
        @else
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Add Bank Loan') }}</h2>
            <form method="POST" action="{{ route('loans.store', $unit) }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label :value="__('Bank Name')" />
                    <x-text-input name="bank_name" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. SBI, HDFC') }}" required />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Loan Account Number')" />
                        <x-text-input name="loan_account_number" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Sanctioned Amount')" />
                        <input type="number" step="0.01" min="0.01" name="sanctioned_amount" required placeholder="{{ \App\Support\Tenant::currencySymbol() }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                </div>
                <div>
                    <x-input-label :value="__('Sanctioned Date (optional)')" />
                    <input type="date" name="sanctioned_at" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <x-input-label :value="__('Notes (optional)')" />
                    <textarea name="notes" rows="2" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Add Loan') }}</x-primary-button>
                </div>
            </form>
        @endif
    </div>
</x-modal>
