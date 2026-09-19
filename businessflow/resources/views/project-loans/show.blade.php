<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('project-loans.index') }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ $loan->lender_name }} — {{ $loan->project->name }}</h2>
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
                        <a href="{{ route('projects.show', $loan->project) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $loan->project->name }}</a>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ \App\Models\ProjectLoan::REPAYMENT_TYPES[$loan->repayment_type] ?? $loan->repayment_type }}
                            @if ($loan->isClosed())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400">{{ __('Closed') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button type="button" x-on:click="editingLoan = !editingLoan" class="text-sm text-accent-600 hover:underline" x-text="editingLoan ? '{{ __('Cancel') }}' : '{{ __('Edit') }}'"></button>
                        <form method="POST" action="{{ route('project-loans.destroy', $loan) }}" onsubmit="return confirm('{{ __('Remove this loan record? Payments already recorded will be removed too.') }}')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:underline">{{ __('Remove loan') }}</button>
                        </form>
                    </div>
                </div>

                <form method="POST" action="{{ route('project-loans.update', $loan) }}" x-show="editingLoan" x-cloak class="grid grid-cols-2 gap-3 mt-4 border border-gray-200 dark:border-slate-700 rounded-md p-3" x-data="{ repaymentType: '{{ $loan->repayment_type }}' }">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="project_id" value="{{ $loan->project_id }}">
                    <div class="col-span-2">
                        <x-input-label :value="__('Lender (bank / NBFC / person)')" />
                        <input type="text" name="lender_name" value="{{ $loan->lender_name }}" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label :value="__('Loan A/C No. (optional)')" />
                        <input type="text" name="account_number" value="{{ $loan->account_number }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label :value="__('Principal Amount')" />
                        <input type="number" step="0.01" min="0.01" name="principal_amount" value="{{ $loan->principal_amount }}" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label :value="__('Interest Rate % p.a. (optional)')" />
                        <input type="number" step="0.01" min="0" max="100" name="interest_rate" value="{{ $loan->interest_rate }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label :value="__('Disbursed On')" />
                        <input type="date" name="disbursed_at" value="{{ $loan->disbursed_at?->format('Y-m-d') }}" required class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div x-show="repaymentType === 'emi'">
                        <x-input-label :value="__('Tenure (months)')" />
                        <input type="number" min="1" max="600" name="tenure_months" value="{{ $loan->tenure_months }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div class="col-span-2">
                        <x-input-label :value="__('Repayment')" />
                        <div class="mt-1 flex gap-4">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="radio" name="repayment_type" value="emi" x-model="repaymentType" class="text-accent-600 focus:ring-accent-500 border-gray-300 dark:border-slate-600">
                                {{ __('Installments (EMI)') }}
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="radio" name="repayment_type" value="full_payment" x-model="repaymentType" class="text-accent-600 focus:ring-accent-500 border-gray-300 dark:border-slate-600">
                                {{ __('Full Payment (Bullet)') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <x-input-label :value="__('Notes (optional)')" />
                        <textarea name="notes" rows="2" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $loan->notes }}</textarea>
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <button class="px-3 py-1.5 bg-accent-600 text-white text-xs font-semibold rounded-md hover:bg-accent-700">{{ __('Save') }}</button>
                    </div>
                </form>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                        <div class="text-xs text-gray-400">{{ __('Principal') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->principal_amount, 0) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                        <div class="text-xs text-gray-400">{{ __('Repaid (Principal)') }}</div>
                        <div class="text-lg font-semibold text-green-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalPrincipalPaid(), 0) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                        <div class="text-xs text-gray-400">{{ __('Interest Paid') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalInterestPaid(), 0) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-3">
                        <div class="text-xs text-gray-400">{{ __('Outstanding Today') }}</div>
                        <div class="text-lg font-semibold {{ $loan->totalOutstanding() > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalOutstanding(), 0) }}</div>
                        @if ($loan->accruedInterestToDate() > 0)
                            <div class="text-xs text-gray-400">{{ __('incl.') }} {{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->accruedInterestToDate(), 0) }} {{ __('accrued interest') }}</div>
                        @endif
                    </div>
                </div>

                <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mt-4" x-show="!editingLoan">
                    @if ($loan->account_number)
                        <div>{{ __('Loan A/C No.') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->account_number }}</span></div>
                    @endif
                    @if ($loan->interest_rate)
                        <div>{{ __('Interest Rate') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ rtrim(rtrim(number_format($loan->interest_rate, 2), '0'), '.') }}% p.a.</span></div>
                    @endif
                    <div>{{ __('Disbursed on') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $loan->disbursed_at->format('d M Y') }}</span></div>
                    @if ($loan->repayment_type === 'emi' && $loan->suggestedMonthlyEmi())
                        <div>{{ __('Suggested EMI') }}: <span class="text-gray-800 dark:text-gray-200 font-medium">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->suggestedMonthlyEmi(), 0) }}/{{ __('month') }}</span> <span class="text-gray-400">({{ __('estimate only') }})</span></div>
                    @endif
                    @if ($loan->notes)
                        <div class="pt-1">{{ $loan->notes }}</div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Payments') }} ({{ $loan->payments->count() }})
                </div>
                @if ($loan->payments->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No payments recorded yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-2 text-left">{{ __('Date') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Method') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Reference') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Interest') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Principal') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                                    <th class="px-5 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($loan->payments as $payment)
                                    <tr>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $payment->paid_at->format('d M Y') }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $payment->method ? ucfirst(str_replace('_', ' ', $payment->method)) : '—' }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $payment->reference ?: '—' }}</td>
                                        <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($payment->interest_portion, 0) }}</td>
                                        <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($payment->principal_portion, 0) }}</td>
                                        <td class="px-5 py-2 text-right font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($payment->amount, 0) }}</td>
                                        <td class="px-5 py-2 text-right">
                                            <form method="POST" action="{{ route('project-loans.payments.destroy', [$loan, $payment]) }}" onsubmit="return confirm('{{ __('Remove this payment?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <form method="POST" action="{{ route('project-loans.payments.store', $loan) }}" class="border-t border-gray-100 dark:border-slate-700 p-5 space-y-3">
                    @csrf
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('+ Add Payment') }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="{{ __('Amount') }}" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <select name="method" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="cheque">{{ __('Cheque') }}</option>
                            <option value="neft">{{ __('NEFT') }}</option>
                            <option value="rtgs">{{ __('RTGS') }}</option>
                            <option value="cash">{{ __('Cash') }}</option>
                        </select>
                        <input type="text" name="reference" placeholder="{{ __('Reference / Cheque No.') }}" class="block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Add Payment') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
