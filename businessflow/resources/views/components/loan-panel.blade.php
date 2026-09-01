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
            @if ($loan->interest_rate)
                <span>{{ __('Interest') }}: <strong>{{ rtrim(rtrim(number_format($loan->interest_rate, 2), '0'), '.') }}%</strong></span>
            @endif
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
    <div class="p-6" x-data="{ editingLoan: false }">
        @if ($unit->loan)
            @php $loan = $unit->loan; @endphp
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Bank Loan') }}</h2>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('loans.statement', $loan) }}" class="text-xs text-accent-600 hover:underline">{{ __('Download Statement (PDF)') }}</a>
                    <button type="button" x-on:click="editingLoan = !editingLoan" class="text-xs text-accent-600 hover:underline" x-text="editingLoan ? '{{ __('Cancel') }}' : '{{ __('Edit') }}'"></button>
                    <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('{{ __('Remove this loan record? Disbursements already recorded stay in the payment ledger.') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs text-red-600 hover:underline">{{ __('Remove loan') }}</button>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('loans.update', $loan) }}" x-show="editingLoan" x-cloak class="grid grid-cols-2 gap-3 mb-4 border border-gray-200 dark:border-slate-700 rounded-md p-3">
                @csrf
                @method('PUT')
                <div class="col-span-2">
                    <x-input-label :value="__('Bank')" />
                    <input type="text" name="bank_name" value="{{ $loan->bank_name }}" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <x-input-label :value="__('Loan A/C No. (optional)')" />
                    <input type="text" name="loan_account_number" value="{{ $loan->loan_account_number }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <x-input-label :value="__('Sanctioned Amount')" />
                    <input type="number" step="0.01" min="0.01" name="sanctioned_amount" value="{{ $loan->sanctioned_amount }}" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <x-input-label :value="__('Interest Rate % p.a. (optional)')" />
                    <input type="number" step="0.01" min="0" max="100" name="interest_rate" value="{{ $loan->interest_rate }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <x-input-label :value="__('Sanctioned On (optional)')" />
                    <input type="date" name="sanctioned_at" value="{{ $loan->sanctioned_at?->format('Y-m-d') }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div class="col-span-2">
                    <x-input-label :value="__('Notes (optional)')" />
                    <textarea name="notes" rows="2" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $loan->notes }}</textarea>
                </div>
                <div class="col-span-2 flex justify-end">
                    <button class="px-3 py-1.5 bg-accent-600 text-white text-xs font-semibold rounded-md hover:bg-accent-700">{{ __('Save') }}</button>
                </div>
            </form>

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

            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mb-4" x-show="!editingLoan">
                <div>{{ __('Bank') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->bank_name }}</span></div>
                @if ($loan->loan_account_number)
                    <div>{{ __('Loan A/C No.') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->loan_account_number }}</span></div>
                @endif
                @if ($loan->interest_rate)
                    <div>{{ __('Interest Rate') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ rtrim(rtrim(number_format($loan->interest_rate, 2), '0'), '.') }}% p.a.</span></div>
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
                                <div class="text-gray-800 dark:text-gray-200">{{ $disbursement->paid_at->format('d M Y') }} @if ($disbursement->method)<span class="text-xs text-gray-400">· {{ ucfirst(str_replace('_', ' ', $disbursement->method)) }}</span>@endif</div>
                                @if ($disbursement->reference)
                                    <div class="text-xs text-gray-400">{{ __('Ref') }}: {{ $disbursement->reference }}</div>
                                @endif
                                @if ($disbursement->account)
                                    <div class="text-xs text-gray-400">{{ __('Received in') }}: {{ $disbursement->account->label() }}</div>
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
                <div class="grid grid-cols-2 gap-3">
                    <select name="method" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                        <option value="cheque">{{ __('Cheque') }}</option>
                        <option value="neft">{{ __('NEFT') }}</option>
                        <option value="rtgs">{{ __('RTGS') }}</option>
                    </select>
                    <input type="text" name="reference" placeholder="{{ __('Reference / Cheque No. (optional)') }}" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                @php $bankAccounts = $accounts ? $accounts->filter(fn ($a) => ! $a->isCash()) : null; @endphp
                @if ($bankAccounts && $bankAccounts->isNotEmpty())
                    {{-- Disbursements always come in by bank transfer — no cash-in-hand accounts here. --}}
                    <select name="payment_account_id" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('Received in (optional)') }}</option>
                        @foreach ($bankAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->label() }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="flex justify-end">
                    <x-primary-button>{{ __('Add Disbursement') }}</x-primary-button>
                </div>
            </form>

            <div class="border-t border-gray-100 dark:border-slate-700 pt-4 mt-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Documents') }} ({{ $loan->documents->count() }})</p>
                <p class="text-xs text-gray-400 mb-2">{{ __('Loan approval, sanction letter, or anything else worth keeping with this loan.') }}</p>

                @if ($loan->documents->isNotEmpty())
                    <ul class="space-y-1.5 mb-3">
                        @foreach ($loan->documents as $document)
                            <li class="flex items-center justify-between gap-3 text-xs bg-gray-50 dark:bg-slate-700/40 rounded-md px-3 py-2">
                                <a href="{{ route('loan-documents.download', [$loan, $document]) }}" class="flex items-center gap-1.5 min-w-0 text-accent-600 hover:underline">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    <span class="truncate">{{ $document->name }}</span>
                                </a>
                                <div class="flex items-center gap-2 shrink-0 text-gray-400">
                                    <span>{{ $document->humanSize() }}</span>
                                    <form method="POST" action="{{ route('loan-documents.destroy', [$loan, $document]) }}" onsubmit="return confirm('{{ __('Delete this document?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('loan-documents.store', $loan) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input name="file" type="file" required class="block w-full text-xs text-gray-600 dark:text-gray-300 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                    <button class="shrink-0 px-2.5 py-1.5 bg-accent-600 text-white text-xs font-semibold rounded-md hover:bg-accent-700">{{ __('Upload') }}</button>
                </form>
            </div>
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Interest Rate % p.a. (optional)')" />
                        <input type="number" step="0.01" min="0" max="100" name="interest_rate" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label :value="__('Sanctioned Date (optional)')" />
                        <input type="date" name="sanctioned_at" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
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
