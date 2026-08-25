<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('investors.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $investor->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400 space-y-0.5">
                        @if ($investor->phone)<div>{{ $investor->phone }}</div>@endif
                        @if ($investor->email)<div>{{ $investor->email }}</div>@endif
                        @if ($investor->notes)<div class="mt-1 text-gray-400 dark:text-gray-500">{{ $investor->notes }}</div>@endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('investors.statement', $investor) }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            {{ __('Download Statement') }}
                        </a>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-investor')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            {{ __('Edit') }}
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Total Invested') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($investor->totalInvested(), 0) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Total Paid Out') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($investor->totalPaidOut(), 0) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Balance (owed to investor)') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($investor->balance(), 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between gap-3">
                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Transaction History') }}</div>
                    <div class="flex items-center gap-2">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'record-investment')" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                            {{ __('+ Record Investment') }}
                        </button>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'record-payout')" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            {{ __('+ Record Payout') }}
                        </button>
                    </div>
                </div>

                @if ($investor->transactions->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No transactions recorded yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Date') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Type') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Project') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Details') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Amount') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($investor->transactions as $transaction)
                                <tr>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        <span @class([
                                            'text-xs px-2 py-0.5 rounded font-medium',
                                            'bg-green-100 text-green-700' => $transaction->type === 'investment',
                                            'bg-amber-100 text-amber-700' => $transaction->type === 'payout',
                                        ])>{{ $transaction->typeLabel() }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $transaction->project?->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $transaction->detailsSummary() ?: '—' }}</td>
                                    <td class="px-5 py-3 text-right font-medium text-gray-900 dark:text-gray-100">₹{{ number_format($transaction->amount, 0) }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <form method="POST" action="{{ route('investor-transactions.destroy', [$investor, $transaction]) }}" onsubmit="return confirm('{{ __('Delete this transaction?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs">{{ __('Delete') }}</button>
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

    <x-modal name="record-investment" max-width="md">
        <form method="POST" action="{{ route('investor-transactions.store', $investor) }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="type" value="investment">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Record Investment') }}</h2>
            <p class="text-xs text-gray-400">{{ __('Money received from :name.', ['name' => $investor->name]) }}</p>
            @include('investors._transaction-fields')
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="record-payout" max-width="md">
        <form method="POST" action="{{ route('investor-transactions.store', $investor) }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="type" value="payout">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Record Payout') }}</h2>
            <p class="text-xs text-gray-400">{{ __('Profit or money paid out to :name.', ['name' => $investor->name]) }}</p>
            @include('investors._transaction-fields')
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-investor" max-width="md">
        <form method="POST" action="{{ route('investors.update', $investor) }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Investor') }}</h2>
            <div>
                <x-input-label for="edit_name" :value="__('Name')" />
                <input type="text" id="edit_name" name="name" value="{{ $investor->name }}" required
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_phone" :value="__('Phone')" />
                <input type="text" id="edit_phone" name="phone" value="{{ $investor->phone }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_email" :value="__('Email')" />
                <input type="email" id="edit_email" name="email" value="{{ $investor->email }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_notes" :value="__('Notes')" />
                <textarea id="edit_notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $investor->notes }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
