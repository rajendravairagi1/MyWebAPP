<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Clear All Business Data') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 space-y-4">
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-4">
                    {{ __('This will permanently delete everything below. It cannot be undone.') }}
                </div>

                <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside space-y-1">
                    <li>{{ __('All Projects, Cost entries, and Units') }}</li>
                    <li>{{ __('All Customers') }}</li>
                    <li>{{ __('All Quotations and Invoices (and their payments)') }}</li>
                    <li>{{ __('All Follow-ups') }}</li>
                    <li>{{ __('All uploaded Customer Documents') }}</li>
                    <li>{{ __('All Products') }}</li>
                </ul>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Your login (email/password) will NOT be deleted — you will stay signed in and can start adding fresh data right away.') }}
                </div>

                <form method="POST" action="{{ route('reset-data.store') }}" class="pt-2 border-t border-gray-100 dark:border-slate-700 space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div>
                        <x-input-label for="confirm" :value="__('Type RESET to confirm')" />
                        <x-text-input id="confirm" name="confirm" type="text" class="mt-1 block w-full" required autocomplete="off" placeholder="RESET" />
                        <x-input-error :messages="$errors->get('confirm')" class="mt-2" />
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        {{ __('Yes, delete everything and start fresh') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
