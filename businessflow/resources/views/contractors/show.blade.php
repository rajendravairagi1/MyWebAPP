<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('contractors.index') }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ $contractor->name }}</h2>
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
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">{{ $contractor->typeLabel() }}</span>
                </div>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 space-y-0.5">
                    @if ($contractor->phone)<div>{{ __('Mobile') }}: {{ $contractor->phone }}</div>@endif
                    @if ($contractor->email)<div>{{ $contractor->email }}</div>@endif
                    @if ($contractor->notes)<div class="mt-1 text-gray-400 dark:text-gray-500">{{ $contractor->notes }}</div>@endif
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-contractor')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        {{ __('Edit') }}
                    </button>
                    <a href="{{ route('contractors.statement', $contractor) }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        {{ __('Download Statement (PDF)') }}
                    </a>
                    <form method="POST" action="{{ route('contractors.destroy', $contractor) }}" onsubmit="return confirm('{{ __('Delete this contractor? This only works if they have no payments recorded.') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-red-200 dark:border-red-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>

                <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Total Paid') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($contractor->totalPaid(), 2) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Outstanding Credit') }}</div>
                        <div class="text-lg font-semibold {{ $contractor->totalOutstanding() > 0 ? 'text-amber-600' : 'text-gray-900 dark:text-gray-100' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($contractor->totalOutstanding(), 2) }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/40 rounded-md p-4">
                        <div class="text-xs text-gray-400">{{ __('Grand Total (all work/material)') }}</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($contractor->grandTotal(), 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Payments') }} ({{ $contractor->costs->count() }})
                </div>
                @if ($contractor->costs->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No payments recorded for this contractor yet — add one from a Project\'s Payments section and pick this name.') }}</div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Date') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Project') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Description') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Status') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($contractor->costs as $entry)
                                <tr>
                                    <td class="px-5 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $entry->spent_on->format('d M Y') }}</td>
                                    <td class="px-5 py-2">
                                        <a href="{{ route('projects.show', $entry->project) }}" class="text-accent-600 hover:underline">{{ $entry->project->name }}</a>
                                    </td>
                                    <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $entry->description }}</td>
                                    <td class="px-5 py-2">
                                        @if ($entry->isOutstandingCredit())
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">{{ __('On Credit') }}</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ __('Paid') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($entry->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="edit-contractor" max-width="md">
        <form method="POST" action="{{ route('contractors.update', $contractor) }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Contractor') }}</h2>
            <div>
                <x-input-label for="edit_name" :value="__('Name')" />
                <input type="text" id="edit_name" name="name" value="{{ $contractor->name }}" required
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_type" :value="__('Type')" />
                <select id="edit_type" name="type" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    @foreach (\App\Models\Contractor::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected($contractor->type === $key)>{{ __($label) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="edit_phone" :value="__('Phone')" />
                <input type="text" id="edit_phone" name="phone" value="{{ $contractor->phone }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_email" :value="__('Email')" />
                <input type="email" id="edit_email" name="email" value="{{ $contractor->email }}"
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <x-input-label for="edit_notes" :value="__('Notes')" />
                <textarea id="edit_notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $contractor->notes }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
