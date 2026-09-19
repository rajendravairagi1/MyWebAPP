<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Project Loans') }}</h2>
            @if ($projects->isNotEmpty())
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-project-loan')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                    {{ __('+ Add Loan') }}
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Construction loans you\'ve taken to fund your own projects — how much is outstanding, how much interest has built up, and whether you\'re paying it off in installments or as a lump sum.') }}
            </p>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Loans') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totals['count'] }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Principal Taken') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['principal'], 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Outstanding Today') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $totals['outstanding'] > 0 ? 'text-amber-600' : 'text-gray-900 dark:text-gray-100' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['outstanding'], 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Interest Paid') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['interest_paid'], 0) }}</div>
                </div>
            </div>

            <x-list-toolbar placeholder="{{ __('Search by lender or project...') }}" />

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($loans->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">
                        @if ($projects->isEmpty())
                            {{ __('Create a project first, then you can record a construction loan against it.') }}
                        @else
                            {{ __('No project loans recorded yet.') }}
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-3 text-left">{{ __('Project') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Lender') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Type') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Principal') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Repaid') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Outstanding') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($loans as $loan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40 cursor-pointer" onclick="window.location='{{ route('project-loans.show', $loan) }}'">
                                        <td class="px-5 py-3">
                                            <a href="{{ route('project-loans.show', $loan) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $loan->project->name }}</a>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $loan->lender_name }}
                                            @if ($loan->isClosed())
                                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400">{{ __('Closed') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ \App\Models\ProjectLoan::REPAYMENT_TYPES[$loan->repayment_type] ?? $loan->repayment_type }}</td>
                                        <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->principal_amount, 0) }}</td>
                                        <td class="px-5 py-3 text-right text-green-600 font-medium">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->totalRepaid(), 0) }}</td>
                                        <td class="px-5 py-3 text-right {{ $loan->outstandingPrincipal() > 0 ? 'text-amber-600' : 'text-gray-400' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($loan->outstandingPrincipal(), 0) }}</td>
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

    @if ($projects->isNotEmpty())
        <x-modal name="add-project-loan" max-width="lg" :show="$errors->has('lender_name')">
            <form method="POST" action="{{ route('project-loans.store') }}" class="p-6 space-y-4" x-data="{ repaymentType: '{{ old('repayment_type', 'emi') }}' }">
                @csrf
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Project Loan') }}</h2>

                <div>
                    <x-input-label for="pl-project" :value="__('Project')" />
                    <select id="pl-project" name="project_id" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected((string) old('project_id') === (string) $project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <x-input-label for="pl-lender" :value="__('Lender (bank / NBFC / person)')" />
                        <input type="text" id="pl-lender" name="lender_name" value="{{ old('lender_name') }}" required autofocus class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <x-input-error :messages="$errors->get('lender_name')" class="mt-2" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="pl-account" :value="__('Loan A/C No. (optional)')" />
                        <input type="text" id="pl-account" name="account_number" value="{{ old('account_number') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label for="pl-principal" :value="__('Principal Amount')" />
                        <input type="number" step="0.01" min="0.01" id="pl-principal" name="principal_amount" value="{{ old('principal_amount') }}" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <x-input-error :messages="$errors->get('principal_amount')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="pl-rate" :value="__('Interest Rate % p.a. (optional)')" />
                        <input type="number" step="0.01" min="0" max="100" id="pl-rate" name="interest_rate" value="{{ old('interest_rate') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label :value="__('Disbursed On')" />
                        <input type="date" name="disbursed_at" value="{{ old('disbursed_at', now()->format('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div x-show="repaymentType === 'emi'">
                        <x-input-label :value="__('Tenure (months)')" />
                        <input type="number" min="1" max="600" name="tenure_months" value="{{ old('tenure_months') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                </div>

                <div>
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

                <div>
                    <x-input-label for="pl-notes" :value="__('Notes (optional)')" />
                    <textarea id="pl-notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                    <x-primary-button>{{ __('Add Loan') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-app-layout>
