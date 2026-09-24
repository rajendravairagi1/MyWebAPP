<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ url()->previous() }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ __('Add Bank Loan') }} — {{ $unit->customer->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('For') }} <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $unit->customer->name }}</span>
                    @if ($unit->project)
                        · {{ $unit->project->name }} · {{ $unit->unit_number }}
                    @endif
                </div>

                <form method="POST" action="{{ route('loans.store', $unit) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label :value="__('Bank Name')" />
                        <x-text-input name="bank_name" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. SBI, HDFC') }}" value="{{ old('bank_name') }}" required autofocus />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Loan Account Number')" />
                            <x-text-input name="loan_account_number" type="text" class="mt-1 block w-full" value="{{ old('loan_account_number') }}" />
                        </div>
                        <div>
                            <x-input-label :value="__('Sanctioned Amount')" />
                            <input type="number" step="0.01" min="0.01" name="sanctioned_amount" value="{{ old('sanctioned_amount') }}" required placeholder="{{ \App\Support\Tenant::currencySymbol() }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Interest Rate % p.a. (optional)')" />
                            <input type="number" step="0.01" min="0" max="100" name="interest_rate" value="{{ old('interest_rate') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                        <div>
                            <x-input-label :value="__('Sanctioned Date (optional)')" />
                            <input type="date" name="sanctioned_at" value="{{ old('sanctioned_at') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                    </div>
                    <div>
                        <x-input-label :value="__('Notes (optional)')" />
                        <textarea name="notes" rows="3" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes') }}</textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Add Loan') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
