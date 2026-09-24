<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ url()->previous() }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ __('Record Payment') }} — {{ $unit->customer->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6">
                <form method="POST"
                    :action="paymentSource === 'loan' ? '{{ $unit->loan ? route('loans.disbursements.store', $unit->loan) : '#' }}' : '{{ route('unit-payments.store', $unit) }}'"
                    x-data="{
                        paymentSource: 'direct',
                        purpose: 'installment',
                        method: 'cash',
                        paymentAccountId: '',
                        loanMethods: ['bank_transfer', 'cheque', 'neft', 'rtgs'],
                    }"
                    x-effect="if (paymentSource === 'loan' && ! loanMethods.includes(method)) method = 'bank_transfer'"
                    class="space-y-4">
                    @csrf
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $unit->project->name }} · {{ $unit->unit_number }}</p>

                    @if ($unit->loan)
                        <input type="hidden" name="redirect_to_customer" value="{{ $unit->customer_id }}">
                        <div>
                            <x-input-label :value="__('Payment source')" />
                            <div class="flex flex-wrap gap-4 text-sm mt-1">
                                <label class="flex items-center gap-1.5">
                                    <input type="radio" x-model="paymentSource" value="direct" class="border-gray-300 text-accent-600 focus:ring-accent-500">
                                    {{ __('Direct from customer') }}
                                </label>
                                <label class="flex items-center gap-1.5">
                                    <input type="radio" x-model="paymentSource" value="loan" class="border-gray-300 text-accent-600 focus:ring-accent-500">
                                    {{ __('Bank Loan Disbursement') }} ({{ $unit->loan->bank_name }})
                                </label>
                            </div>
                        </div>
                    @endif

                    <div x-show="paymentSource === 'direct'">
                        <x-input-label for="purpose" :value="__('Payment for')" />
                        <select id="purpose" name="purpose" x-model="purpose" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            @foreach (\App\Models\UnitPayment::PURPOSES as $val => $label)
                                <option value="{{ $val }}" @selected($val === 'installment')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="paymentSource === 'direct' && purpose === 'other'" x-cloak>
                        <x-input-label for="purpose_other" :value="__('If Other, specify')" />
                        <x-text-input id="purpose_other" name="purpose_other" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. Parking charges') }}" />
                    </div>

                    <div x-show="paymentSource === 'direct'">
                        <x-input-label for="description" :value="__('Description (optional)')" />
                        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. 2nd installment as per agreement') }}" />
                    </div>
                    <p x-show="paymentSource === 'loan'" x-cloak class="text-xs text-gray-400">{{ __('Recorded as a bank loan disbursement — description is filled in automatically.') }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <input id="amount" type="number" step="0.01" min="0.01" name="amount" required placeholder="{{ \App\Support\Tenant::currencySymbol() }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                        <div>
                            <x-input-label for="paid_at" :value="__('Date')" />
                            <input id="paid_at" type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                        <div>
                            <x-input-label for="method" :value="__('Method')" />
                            <select id="method" name="method" x-model="method" x-on:change="paymentAccountId = ''" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                <option value="cash" x-bind:hidden="paymentSource === 'loan'">{{ __('Cash') }}</option>
                                <option value="upi" x-bind:hidden="paymentSource === 'loan'">{{ __('UPI') }}</option>
                                <option value="bank_transfer">{{ __('Bank transfer') }}</option>
                                <option value="cheque">{{ __('Cheque') }}</option>
                                <option value="neft" x-bind:hidden="paymentSource === 'direct'">{{ __('NEFT') }}</option>
                                <option value="rtgs" x-bind:hidden="paymentSource === 'direct'">{{ __('RTGS') }}</option>
                                <option value="card" x-bind:hidden="paymentSource === 'loan'">{{ __('Card') }}</option>
                                <option value="other" x-bind:hidden="paymentSource === 'loan'">{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="reference" :value="__('Reference (optional)')" />
                            <x-text-input id="reference" name="reference" type="text" class="mt-1 block w-full" />
                        </div>
                        @if ($paymentAccounts->isNotEmpty())
                            <div class="col-span-1 sm:col-span-2">
                                <x-input-label for="payment_account_id" x-text="method === 'cash' ? '{{ __('Who is holding this cash? (optional)') }}' : '{{ __('Received In (optional)') }}'" />
                                <select id="payment_account_id" name="payment_account_id" x-model="paymentAccountId" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                    <option value="">{{ __('— Not specified —') }}</option>
                                    @foreach ($paymentAccounts as $account)
                                        <option value="{{ $account->id }}" x-bind:hidden="method {{ $account->isCash() ? '!==' : '===' }} 'cash'">{{ $account->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('+ Record Payment') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
