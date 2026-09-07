<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="history.back()" class="shrink-0 inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    {{ __('Back') }}
                </button>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ $lead->name }}</h2>
            </div>
            <x-status-badge :status="$lead->status" class="text-sm" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            @if ($lead->message)
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-1">{{ __('Message from form') }}</div>
                    <div class="text-sm text-gray-800 dark:text-gray-100">{{ $lead->message }}</div>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Details') }}</div>
                    @if ($url = $lead->whatsappUrl())
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center px-2.5 py-1 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">{{ __('WhatsApp') }}</a>
                    @endif
                </div>

                <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="$lead->name" required />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="$lead->phone" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email (optional)')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="$lead->email" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Stage')" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            @foreach (\App\Models\Lead::STAGES as $val => $label)
                                <option value="{{ $val }}" @selected($lead->status === $val)>{{ $label }}</option>
                            @endforeach
                            <option value="{{ \App\Models\Lead::LOST }}" @selected($lead->status === \App\Models\Lead::LOST)>{{ __('Not Interested / Lost') }}</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-400">{{ __('Set to "Booked" once a property is booked — this converts the lead into a Customer below.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $lead->notes }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>

            @if ($lead->converted_customer_id)
                <a href="{{ route('customers.show', $lead->converted_customer_id) }}" class="block bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-700 dark:text-green-400 hover:underline">
                    {{ __('This lead was booked — view the Customer record →') }}
                </a>
            @else
                <form method="POST" action="{{ route('leads.convert', $lead) }}" onsubmit="return confirm('{{ __('Convert this lead into a Customer? Do this once a property is actually booked.') }}')">
                    @csrf
                    <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-accent-600 text-white text-sm font-semibold rounded-md hover:bg-accent-700">
                        {{ __('Property Booked — Convert to Customer') }}
                    </button>
                </form>
            @endif

            {{-- Follow-ups --}}
            <div id="followups" class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden scroll-mt-6">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Follow-ups') }}</span>
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-lead-followup')" class="text-xs text-accent-600 hover:underline">{{ __('+ Add') }}</button>
                </div>

                @if ($lead->followups->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No follow-ups yet — e.g. "Site visit planned next week".') }}</div>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($lead->followups as $followup)
                            <li class="px-5 py-3 flex items-center justify-between gap-4 {{ $followup->status === 'done' ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-accent-100 dark:bg-slate-700 text-accent-700">{{ $followup->categoryLabel() }}</span>
                                        <x-status-badge :status="$followup->status" />
                                        @if ($followup->status === 'pending' && $followup->due_at->isPast())
                                            <span class="text-xs px-2 py-0.5 rounded font-medium bg-red-100 text-red-700">{{ __('Overdue') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-gray-900 dark:text-gray-100 truncate mt-1">{{ $followup->note }}</div>
                                    <div class="text-xs text-gray-400">{{ $followup->due_at->format('d M Y, h:i A') }}</div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    @if ($followup->status === 'pending')
                                        <form method="POST" action="{{ route('followups.complete', $followup) }}">
                                            @csrf
                                            <button class="text-xs text-accent-600 hover:underline">{{ __('Mark done') }}</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('followups.destroy', $followup) }}" onsubmit="return confirm('{{ __('Remove this follow-up?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <x-modal name="add-lead-followup" :show="$errors->has('note') || $errors->has('due_at')">
                <form method="POST" action="{{ route('followups.store') }}" x-data="{ category: 'general' }" class="p-6 space-y-4">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Follow-up') }}</h2>
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                    <div>
                        <x-input-label for="category" :value="__('This follow-up is about')" />
                        <select id="category" name="category" x-model="category" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            @foreach (\App\Models\Followup::CATEGORIES as $val => $label)
                                <option value="{{ $val }}" @selected($val === 'general')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="category === 'other'" x-cloak>
                        <x-input-label for="category_other" :value="__('If Other, specify')" />
                        <x-text-input id="category_other" name="category_other" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. Loan / bank paperwork') }}" />
                    </div>

                    <div>
                        <x-input-label for="note" :value="__('Note')" />
                        <div class="mt-1 flex items-center gap-2">
                            <x-text-input id="note" name="note" type="text" class="block w-full" required placeholder="{{ __('e.g. Site visit planned next week') }}" />
                            <x-voice-mic-button target="note" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="due_at" :value="__('Due')" />
                        <input id="due_at" name="due_at" type="datetime-local" value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" required
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                        <x-primary-button>{{ __('+ Add') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>

            <form method="POST" action="{{ route('leads.destroy', $lead) }}" onsubmit="return confirm('{{ __('Delete this lead? This cannot be undone.') }}')">
                @csrf
                @method('DELETE')
                <button class="text-xs text-red-600 hover:underline">{{ __('Delete lead') }}</button>
            </form>
        </div>
    </div>
</x-app-layout>
