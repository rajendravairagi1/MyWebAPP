<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Available Properties') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Every unit across all your projects that is still open for sale — in one place, without opening each project.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Available') }} ({{ $units->count() }})
                </div>

                @forelse ($units as $unit)
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700 last:border-b-0">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('projects.show', $unit->project) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $unit->project->name }}</a>
                                    <span class="text-gray-400">·</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $unit->unit_number }}</span>
                                    @if ($unit->type)
                                        <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">{{ $unit->type }}</span>
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
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <div class="text-xs text-gray-400">{{ __('Price') }}</div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($unit->price, 0) }}</div>
                                </div>
                                <div class="flex flex-col gap-1 text-xs">
                                    <a href="{{ route('quotations.create', ['project_id' => $unit->project_id, 'unit_id' => $unit->id]) }}" class="text-accent-600 hover:underline">{{ __('+ Quotation') }}</a>
                                    <a href="{{ route('invoices.create', ['project_id' => $unit->project_id, 'unit_id' => $unit->id]) }}" class="text-accent-600 hover:underline">{{ __('+ Invoice') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No properties available for sale right now.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
