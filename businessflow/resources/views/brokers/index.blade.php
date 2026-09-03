<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Brokers') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-broker')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Add Broker') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Commission earned by each broker (fixed amount or % of the sale), what\'s been paid out, and what you still owe them.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($brokers->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No brokers added yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Phone') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Commission Earned') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Paid Out') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Balance Owed') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($brokers as $broker)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('brokers.show', $broker) }}" class="text-accent-600 hover:underline font-medium">{{ $broker->name }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $broker->phone ?? '—' }}</td>
                                    <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($broker->totalCommissionAccrued(), 2) }}</td>
                                    <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($broker->totalPaid(), 2) }}</td>
                                    <td class="px-5 py-3 text-right font-semibold {{ $broker->balance() > 0 ? 'text-amber-600' : 'text-gray-900 dark:text-gray-100' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($broker->balance(), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="add-broker" max-width="md" :show="$errors->has('name')">
        <form method="POST" action="{{ route('brokers.store') }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Broker') }}</h2>
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Add Broker') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
