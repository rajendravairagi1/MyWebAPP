<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Available Properties') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Every unit across all your projects that is still open for sale — in one place, without opening each project.') }}
                </p>
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-property')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700 shrink-0">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    {{ __('Add Property') }}
                </button>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Available') }} ({{ $units->count() }})
                </div>

                @forelse ($units as $unit)
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700 last:border-b-0">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('project-units.show', $unit) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $unit->project->name }} · {{ $unit->unit_number }}</a>
                                    @if ($unit->type)
                                        <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">{{ $unit->type }}</span>
                                    @endif
                                    @if ($unit->photos->count())
                                        <span class="text-xs text-gray-400">{{ __('· :count media', ['count' => $unit->photos->count()]) }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    @if ($unit->area_sqft)
                                        {{ number_format($unit->area_sqft, 0) }} {{ __('sqft') }}
                                    @endif
                                    @if ($unit->project->location)
                                        · {{ $unit->project->location }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-400">{{ __('Price') }}</div>
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($unit->price, 0) }}</div>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('project-units.show', $unit) }}" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                                {{ __('View Details') }}
                            </a>
                            <a href="{{ route('quotations.create', ['project_id' => $unit->project_id, 'unit_id' => $unit->id]) }}" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                                {{ __('+ Quotation') }}
                            </a>
                            <a href="{{ route('invoices.create', ['project_id' => $unit->project_id, 'unit_id' => $unit->id]) }}" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                                {{ __('+ Invoice') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No properties available for sale right now.') }}</div>
                @endforelse
            </div>

            @if ($deals->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                        {{ __('Direct Deals') }} ({{ $deals->count() }})
                    </div>
                    @foreach ($deals as $deal)
                        <div class="p-5 border-b border-gray-100 dark:border-slate-700 last:border-b-0">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('property-deals.show', $deal) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $deal->property_title }}</a>
                                        <span class="text-xs px-2 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">{{ __('Deal') }}</span>
                                        @if ($deal->photos->count())
                                            <span class="text-xs text-gray-400">{{ __('· :count media', ['count' => $deal->photos->count()]) }}</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        @if ($deal->address){{ $deal->address }}@endif
                                    </div>
                                </div>
                                @if ($deal->asking_price)
                                    <div class="text-right">
                                        <div class="text-xs text-gray-400">{{ __('Asking Price') }}</div>
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($deal->asking_price, 0) }}</div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('property-deals.show', $deal) }}" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                                    {{ __('View Details') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <x-modal name="add-property" max-width="lg" :show="$errors->has('unit_number') || $errors->has('price') || $errors->has('new_project_name')">
        <form method="POST" action="{{ route('available-properties.store') }}" class="p-6 space-y-4" x-data="{ mode: {{ $projects->isEmpty() ? "'new'" : "'existing'" }} }">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Property for Sale') }}</h2>

            @if ($projects->isNotEmpty())
                <div class="flex gap-5 text-sm text-gray-700 dark:text-gray-300">
                    <label class="flex items-center gap-1.5">
                        <input type="radio" x-model="mode" value="existing" class="border-gray-300 text-accent-600 focus:ring-accent-500">
                        {{ __('Existing project') }}
                    </label>
                    <label class="flex items-center gap-1.5">
                        <input type="radio" x-model="mode" value="new" class="border-gray-300 text-accent-600 focus:ring-accent-500">
                        {{ __('New — standalone plot / project') }}
                    </label>
                </div>
            @endif

            <div x-show="mode === 'existing'" @if ($projects->isEmpty()) x-cloak @endif>
                <x-input-label for="project_id" :value="__('Project')" />
                <select id="project_id" name="project_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    <option value="">{{ __('Choose a project…') }}</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
            </div>

            <div x-show="mode === 'new'" @if ($projects->isNotEmpty()) x-cloak @endif class="space-y-4">
                <div>
                    <x-input-label for="new_project_name" :value="__('Plot / project name')" />
                    <input type="text" id="new_project_name" name="new_project_name" value="{{ old('new_project_name') }}" placeholder="{{ __('e.g. Survey No. 45 Plot') }}"
                        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    <x-input-error :messages="$errors->get('new_project_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="new_project_type" :value="__('Type')" />
                    <select id="new_project_type" name="new_project_type" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="plot" @selected(old('new_project_type', 'plot') === 'plot')>{{ __('Plot') }}</option>
                        <option value="residential" @selected(old('new_project_type') === 'residential')>{{ __('Residential') }}</option>
                        <option value="commercial" @selected(old('new_project_type') === 'commercial')>{{ __('Commercial') }}</option>
                        <option value="mixed" @selected(old('new_project_type') === 'mixed')>{{ __('Mixed') }}</option>
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-slate-700 pt-4 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="unit_number" :value="__('Unit / Plot No.')" />
                    <input type="text" id="unit_number" name="unit_number" value="{{ old('unit_number') }}" required
                        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    <x-input-error :messages="$errors->get('unit_number')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="type" :value="__('Type (e.g. 2BHK, Plot)')" />
                    <input type="text" id="type" name="type" value="{{ old('type') }}"
                        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <x-input-label for="area_sqft" :value="__('Area (sqft)')" />
                    <input type="number" step="0.01" min="0" id="area_sqft" name="area_sqft" value="{{ old('area_sqft') }}"
                        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <x-input-label for="price" :value="__('Price')" />
                    <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price') }}" required
                        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                <p class="text-xs text-gray-400 mb-3">{{ __('Who a customer should contact about this property (optional — shown on the shareable link and PDF).') }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <x-input-label for="contact_name" :value="__('Contact name')" />
                        <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label for="contact_phone" :value="__('Contact mobile')" />
                        <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-input-label for="contact_email" :value="__('Contact email')" />
                        <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Add Property') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
