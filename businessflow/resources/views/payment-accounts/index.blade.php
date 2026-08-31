<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Payment Accounts') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-account')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Add Account') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Money doesn\'t always land in one account — wife\'s, father\'s, a partner\'s, or more than one account for the same person. List every account it actually moves through here — add the bank and account number so accounts belonging to the same person stay tellable apart — so every payment can record exactly which one — useful later for ITR.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($accounts->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No accounts added yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Bank') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Account No.') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Notes') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($accounts as $account)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $account->name }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $account->bank_name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $account->maskedAccountNumber() ?? '—' }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $account->notes ?? '—' }}</td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-account-{{ $account->id }}')" class="text-accent-600 hover:underline text-xs">{{ __('Edit') }}</button>
                                        <form method="POST" action="{{ route('payment-accounts.destroy', $account) }}" onsubmit="return confirm('{{ __('Delete this account?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs ml-2">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="add-account" max-width="md" :show="$errors->has('name')">
        <form method="POST" action="{{ route('payment-accounts.store') }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Account') }}</h2>
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. Wife — Priya, Self, Father — Ramesh') }}" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="bank_name" :value="__('Bank (optional)')" />
                    <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. HDFC') }}" />
                </div>
                <div>
                    <x-input-label for="account_number" :value="__('Account number (optional)')" />
                    <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. 50100123456789') }}" />
                    <p class="mt-1 text-xs text-gray-400">{{ __('Only the last 4 digits show elsewhere — safe to enter the full number.') }}</p>
                </div>
            </div>
            <div>
                <x-input-label for="notes" :value="__('Notes (optional)')" />
                <textarea id="notes" name="notes" rows="2" placeholder="{{ __('anything else that helps you recognize it') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Add Account') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    @foreach ($accounts as $account)
        <x-modal name="edit-account-{{ $account->id }}" max-width="md">
            <form method="POST" action="{{ route('payment-accounts.update', $account) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Account') }}</h2>
                <div>
                    <x-input-label :value="__('Name')" />
                    <x-text-input name="name" type="text" class="mt-1 block w-full" value="{{ $account->name }}" required />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Bank (optional)')" />
                        <x-text-input name="bank_name" type="text" class="mt-1 block w-full" value="{{ $account->bank_name }}" />
                    </div>
                    <div>
                        <x-input-label :value="__('Account number (optional)')" />
                        <x-text-input name="account_number" type="text" class="mt-1 block w-full" value="{{ $account->account_number }}" />
                    </div>
                </div>
                <div>
                    <x-input-label :value="__('Notes (optional)')" />
                    <textarea name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $account->notes }}</textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>
