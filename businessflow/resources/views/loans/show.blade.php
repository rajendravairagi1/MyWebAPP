<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('loans.index') }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ $loan->bank_name }} — {{ $loan->customer->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ editingLoan: false }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <a href="{{ route('customers.show', $loan->customer) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $loan->customer->name }}</a>
                        @if ($loan->unit)
                            <div class="text-xs text-gray-400 mt-0.5">
                                <a href="{{ route('projects.show', $loan->unit->project) }}" class="hover:text-accent-600">{{ $loan->unit->project->name }}</a> · {{ $loan->unit->unit_number }}
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('loans.statement', $loan) }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            {{ __('Download Statement (PDF)') }}
                        </a>
                        <button type="button" x-on:click="editingLoan = !editingLoan" class="text-sm text-accent-600 hover:underline" x-text="editingLoan ? '{{ __('Cancel') }}' : '{{ __('Edit') }}'"></button>
                        <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('{{ __('Remove this loan record? Disbursements already recorded stay in the payment ledger.') }}')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:underline">{{ __('Remove loan') }}</button>
                        </form>
                    </div>
                </div>

                <form method="POST" action="{{ route('loans.update', $loan) }}" x-show="editingLoan" x-cloak class="grid grid-cols-2 gap-3 mt-4 border border-gray-200 dark:border-slate-700 rounded-md p-3">
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

                <div class="grid grid-cols-3 gap-3 mt-5">
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                        <div class="text-xs text-gray-400">{{ __('Sanctioned') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->sanctioned_amount, 0) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                        <div class="text-xs text-gray-400">{{ __('Disbursed') }}</div>
                        <div class="text-lg font-semibold text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalDisbursed(), 0) }}</div>
                        <div class="text-xs text-gray-400">{{ $loan->percentDisbursed() }}%</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                        <div class="text-xs text-gray-400">{{ __('Remaining to disburse') }}</div>
                        <div class="text-lg font-semibold {{ $loan->remainingToDisburse() > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->remainingToDisburse(), 0) }}</div>
                    </div>
                </div>

                <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mt-4" x-show="!editingLoan">
                    @if ($loan->interest_rate)
                        <div>{{ __('Interest Rate') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ rtrim(rtrim(number_format($loan->interest_rate, 2), '0'), '.') }}% p.a.</span></div>
                    @endif
                    @if ($loan->sanctioned_at)
                        <div>{{ __('Sanctioned on') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->sanctioned_at->format('d M Y') }}</span></div>
                    @endif
                    @if ($loan->notes)
                        <div class="pt-1">{{ $loan->notes }}</div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Disbursements') }} ({{ $loan->disbursements->count() }})
                </div>
                @if ($loan->disbursements->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No disbursements recorded yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-2 text-left">{{ __('Date') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Method') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Reference / Cheque No.') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Received In') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($loan->disbursements as $disbursement)
                                    <tr>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $disbursement->paid_at->format('d M Y') }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $disbursement->method ? ucfirst(str_replace('_', ' ', $disbursement->method)) : '—' }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $disbursement->reference ?: '—' }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $disbursement->account?->label() ?? '—' }}</td>
                                        <td class="px-5 py-2 text-right font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($disbursement->amount, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <form method="POST" action="{{ route('loans.disbursements.store', $loan) }}" class="border-t border-gray-100 dark:border-slate-700 p-5 space-y-3">
                    @csrf
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('+ Add Disbursement') }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="{{ __('Amount') }}" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <select name="method" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="cheque">{{ __('Cheque') }}</option>
                            <option value="neft">{{ __('NEFT') }}</option>
                            <option value="rtgs">{{ __('RTGS') }}</option>
                        </select>
                        <input type="text" name="reference" placeholder="{{ __('Reference / Cheque No.') }}" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    @php $bankAccounts = $accounts->filter(fn ($a) => ! $a->isCash()); @endphp
                    @if ($bankAccounts->isNotEmpty())
                        <select name="payment_account_id" class="block w-full sm:w-1/2 text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
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
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Documents') }} ({{ $loan->documents->count() }})
                </div>
                <p class="px-5 pt-3 text-xs text-gray-400">{{ __('Loan approval, sanction letter, or anything else worth keeping with this loan.') }}</p>

                @if ($loan->documents->isNotEmpty())
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm mt-2">
                        @foreach ($loan->documents as $document)
                            <li class="px-5 py-3 flex items-center justify-between gap-4">
                                <a href="{{ route('loan-documents.download', [$loan, $document]) }}" class="flex items-center gap-2 min-w-0 text-accent-600 hover:underline">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    <span class="truncate">{{ $document->name }}</span>
                                </a>
                                <div class="flex items-center gap-3 shrink-0 text-xs text-gray-400">
                                    <span>{{ $document->humanSize() }}</span>
                                    <span>{{ $document->created_at->format('d M Y') }}</span>
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

                <form method="POST" action="{{ route('loan-documents.store', $loan) }}" enctype="multipart/form-data" class="p-5 border-t border-gray-100 dark:border-slate-700 flex flex-wrap items-center gap-3">
                    @csrf
                    <input name="file" type="file" required class="block text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                    <x-primary-button>{{ __('Upload') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
