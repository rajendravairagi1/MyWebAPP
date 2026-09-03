<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Contractors / Vendors') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-contractor')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Add Contractor') }}
            </button>
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

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Labor contractors, vendors, and trades (painter, plumber, tiles, electrician...) that you pay across projects — pick them by name when recording a payment, and their full history is here in one place.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($contractors->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No contractors added yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Type') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Phone') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Total Paid') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Outstanding Credit') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($contractors as $contractor)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('contractors.show', $contractor) }}" class="text-accent-600 hover:underline font-medium">{{ $contractor->name }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $contractor->typeLabel() }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $contractor->phone ?? '—' }}</td>
                                    <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($contractor->totalPaid(), 2) }}</td>
                                    <td class="px-5 py-3 text-right {{ $contractor->totalOutstanding() > 0 ? 'text-amber-600 font-semibold' : 'text-gray-400' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($contractor->totalOutstanding(), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="add-contractor" max-width="md" :show="$errors->has('name')">
        <form method="POST" action="{{ route('contractors.store') }}" class="p-6 space-y-4" x-data="{ typeSelect: '{{ old('type', 'other') }}' }">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Contractor') }}</h2>
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="type" :value="__('Type')" />
                <select id="type" name="type" x-model="typeSelect" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}" @selected(old('type') === $key)>{{ __($label) }}</option>
                    @endforeach
                </select>
                <div x-show="typeSelect === 'other'" x-cloak class="mt-2">
                    <input type="text" name="type_other" value="{{ old('type_other') }}" placeholder="{{ __('e.g. Waterproofing Contractor') }}" class="block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
            </div>
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
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
                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-primary-button>{{ __('Add Contractor') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
