<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Completed Projects') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Properties where the sale is fully closed — either paid off in full or written off. These can no longer be assigned to a customer. If the same unit is being sold again, create a fresh unit for it.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Completed') }} ({{ $units->count() }})
                </div>

                @forelse ($units as $unit)
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700 last:border-b-0">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('projects.show', $unit->project) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $unit->project->name }}</a>
                                    <span class="text-gray-400">·</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $unit->unit_number }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">#{{ $unit->id }}</span>
                                    @if ($unit->write_off_at)
                                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-red-100 text-red-700">{{ __('Written off') }}</span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-green-100 text-green-700">{{ __('Paid off') }}</span>
                                    @endif
                                    @php $cs = $unit->commitmentStatus(); @endphp
                                    @if ($cs === 'met')
                                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-green-100 text-green-700">{{ __('Delivered on time') }}</span>
                                    @elseif ($cs === 'late')
                                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-amber-100 text-amber-700">{{ __('Delivered late') }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    @if ($unit->customer)
                                        <a href="{{ route('customers.show', $unit->customer) }}" class="text-accent-600 hover:underline">{{ $unit->customer->name }}</a>
                                    @endif
                                    · {{ __('Closed on') }} {{ $unit->archived_at->format('d M Y') }}
                                    @if ($unit->write_off_at)
                                        · {{ __('Written off') }}: ₹{{ number_format($unit->write_off_amount, 0) }}@if ($unit->write_off_note) — {{ $unit->write_off_note }}@endif
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <div class="text-xs text-gray-400">{{ __('Collected') }}</div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($unit->totalCollected(), 0) }} / ₹{{ number_format($unit->price, 0) }}</div>
                                </div>
                                <form method="POST" action="{{ route('project-units.recover', $unit) }}" onsubmit="return confirm('{{ __('Move this property back to active?') }}')">
                                    @csrf
                                    <button class="px-3 py-1.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Recover') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing completed yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
