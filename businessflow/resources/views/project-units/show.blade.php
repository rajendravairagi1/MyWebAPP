<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('projects.show', $unit->project) }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">{{ $unit->unit_number }} — {{ $unit->project->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'photos' }">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            @if (session('shareUrl'))
                <div class="bg-accent-50 dark:bg-accent-900/20 border border-accent-200 dark:border-accent-800 rounded-lg p-4 space-y-3" x-data="{ shareUrl: '{{ session('shareUrl') }}', copied: false }">
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Shareable link — anyone with this link can view this property, no login needed.') }}</div>
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="text" readonly x-model="shareUrl" x-on:click="$event.target.select()" class="flex-1 min-w-0 text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm">
                        <button type="button" x-on:click="navigator.clipboard.writeText(shareUrl); copied = true; setTimeout(() => copied = false, 2000)" class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700" x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
                        <a :href="'https://wa.me/?text=' + encodeURIComponent(shareUrl)" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg text-sm font-medium bg-green-600 text-white hover:bg-green-700">
                            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/><path d="M12.004 2.003c-5.514 0-9.997 4.483-9.997 9.997 0 1.762.462 3.478 1.34 4.985L2 22l5.146-1.35a9.955 9.955 0 004.858 1.237h.004c5.514 0 9.997-4.483 9.997-9.997 0-2.67-1.04-5.182-2.929-7.071a9.935 9.935 0 00-7.072-2.926zm0 18.19a8.222 8.222 0 01-4.19-1.147l-.301-.179-3.055.801.815-2.978-.196-.306a8.213 8.213 0 01-1.259-4.384c0-4.535 3.69-8.225 8.226-8.225 2.196 0 4.26.856 5.815 2.41a8.169 8.169 0 012.408 5.816c0 4.536-3.69 8.226-8.226 8.226z"/></svg>
                            {{ __('Share on WhatsApp') }}
                        </a>
                        <a href="{{ route('property-share.pdf', $unit->shareToken()) }}" class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Download PDF') }}</a>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('projects.show', $unit->project) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $unit->project->name }}</a>
                        <span class="text-gray-400">·</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $unit->unit_number }}</span>
                        @if ($unit->type)
                            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">{{ $unit->type }}</span>
                        @endif
                        <span @class([
                            'text-xs px-2 py-0.5 rounded font-medium',
                            'bg-green-100 text-green-700' => $unit->status === 'available',
                            'bg-amber-100 text-amber-700' => $unit->status === 'booked',
                            'bg-gray-200 text-gray-600' => $unit->status === 'sold',
                        ])>{{ ucfirst($unit->status) }}</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        @if ($unit->area_sqft){{ number_format($unit->area_sqft, 0) }} {{ __('sqft') }}@endif
                        @if ($unit->project->location) · {{ $unit->project->location }}@endif
                        @if ($unit->customer) · {{ __('Assigned to') }} {{ $unit->customer->name }}@endif
                    </div>
                    @if ($unit->contact_name || $unit->contact_phone || $unit->contact_email)
                        <div class="text-xs text-gray-400 mt-1">
                            {{ __('Contact') }}: {{ collect([$unit->contact_name, $unit->contact_phone, $unit->contact_email])->filter()->implode(' · ') }}
                        </div>
                    @endif
                    @if ($unit->notes)
                        <div class="text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 rounded px-2 py-1 mt-2 whitespace-pre-line max-w-md">
                            <span class="font-medium">{{ __('Notes') }}:</span> {{ $unit->notes }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('property-share.generate', $unit) }}" class="mt-3">
                        @csrf
                        <button class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg>
                            {{ __('Share Property') }}
                        </button>
                    </form>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-400">{{ __('Price') }}</div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($unit->price, 0) }}</div>
                    @if ($firstPayment = $unit->firstPayment())
                        <div class="text-xs text-gray-400 mt-1">{{ __('Booked with') }} {{ \App\Support\Tenant::currencySymbol() }}{{ number_format($firstPayment->amount, 0) }} <span class="text-gray-400">({{ $firstPayment->paid_at->format('d M Y') }})</span></div>
                    @endif
                    <div class="mt-2 flex items-center justify-end gap-3">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-unit')" class="text-xs text-accent-600 hover:underline">{{ __('Edit') }}</button>
                        <form method="POST" action="{{ route('project-units.destroy', [$unit->project, $unit]) }}" onsubmit="return confirm('{{ __('Delete this property? This cannot be undone.') }}')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-600 hover:underline">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>

            <x-modal name="edit-unit" max-width="md">
                <form method="POST" action="{{ route('project-units.update', [$unit->project, $unit]) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Property') }}</h2>
                    <div>
                        <x-input-label :value="__('Unit number')" />
                        <x-text-input name="unit_number" type="text" class="mt-1 block w-full" value="{{ $unit->unit_number }}" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Type (optional)')" />
                            <x-text-input name="type" type="text" class="mt-1 block w-full" value="{{ $unit->type }}" />
                        </div>
                        <div>
                            <x-input-label :value="__('Area sqft (optional)')" />
                            <x-text-input name="area_sqft" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ $unit->area_sqft }}" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Price')" />
                            <x-text-input name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ $unit->price }}" required />
                        </div>
                        <div>
                            <x-input-label :value="__('Status')" />
                            <select name="status" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                <option value="available" @selected($unit->status === 'available')>{{ __('Available') }}</option>
                                <option value="booked" @selected($unit->status === 'booked')>{{ __('Booked') }}</option>
                                <option value="sold" @selected($unit->status === 'sold')>{{ __('Sold') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100 dark:border-slate-700">
                        <p class="text-xs text-gray-400 mb-3">{{ __('Who a customer should contact about this property — shown on the shareable link and PDF, separate from your business\'s own contact details.') }}</p>
                        <div class="space-y-4">
                            <div>
                                <x-input-label :value="__('Contact name (optional)')" />
                                <x-text-input name="contact_name" type="text" class="mt-1 block w-full" value="{{ $unit->contact_name }}" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label :value="__('Contact mobile (optional)')" />
                                    <x-text-input name="contact_phone" type="text" class="mt-1 block w-full" value="{{ $unit->contact_phone }}" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Contact email (optional)')" />
                                    <x-text-input name="contact_email" type="email" class="mt-1 block w-full" value="{{ $unit->contact_email }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100 dark:border-slate-700">
                        <x-input-label :value="__('Notes (optional)')" />
                        <p class="text-xs text-gray-400 mb-1">{{ __('For your own reference — e.g. plans to double this unit, or build Ground + 2. Not shown on the shared link.') }}</p>
                        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $unit->notes }}</textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="flex border-b border-gray-100 dark:border-slate-700 text-sm overflow-x-auto">
                    <button type="button" @click="tab = 'photos'" :class="tab === 'photos' ? 'border-accent-600 text-accent-600' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-5 py-3 border-b-2 font-medium whitespace-nowrap">{{ __('Photos') }} ({{ $unit->photos->count() }})</button>
                    <button type="button" @click="tab = 'layout'" :class="tab === 'layout' ? 'border-accent-600 text-accent-600' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-5 py-3 border-b-2 font-medium whitespace-nowrap">{{ __('Layout') }} ({{ $unit->layouts->count() }})</button>
                    <button type="button" @click="tab = 'papers'" :class="tab === 'papers' ? 'border-accent-600 text-accent-600' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-5 py-3 border-b-2 font-medium whitespace-nowrap">{{ __('Papers') }} ({{ $unit->documents->count() }})</button>
                </div>

                <div x-show="tab === 'photos'" class="p-5 space-y-4">
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'upload-photo')" class="text-sm text-accent-600 hover:underline">{{ __('+ Upload photos') }}</button>
                    @if ($unit->photos->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No photos uploaded yet.') }}</p>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach ($unit->photos as $photo)
                                <div class="relative group">
                                    <a href="{{ route('unit-media.show', [$unit, $photo]) }}" target="_blank" rel="noopener">
                                        <img src="{{ route('unit-media.show', [$unit, $photo]) }}" alt="{{ $photo->original_name }}" loading="lazy" class="w-full h-32 object-cover rounded-md border border-gray-200 dark:border-slate-700">
                                    </a>
                                    <form method="POST" action="{{ route('unit-media.destroy', [$unit, $photo]) }}" onsubmit="return confirm('{{ __('Delete this photo?') }}')" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-white/90 dark:bg-slate-900/90 text-red-600 rounded-full h-6 w-6 flex items-center justify-center text-xs shadow">&times;</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div x-show="tab === 'layout'" x-cloak class="p-5 space-y-4">
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'upload-layout')" class="text-sm text-accent-600 hover:underline">{{ __('+ Upload layout') }}</button>
                    @if ($unit->layouts->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No layout uploaded yet.') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($unit->layouts as $item)
                                @include('project-units._media-row', ['item' => $item])
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div x-show="tab === 'papers'" x-cloak class="p-5 space-y-4">
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'upload-document')" class="text-sm text-accent-600 hover:underline">{{ __('+ Upload papers') }}</button>
                    @if ($unit->documents->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No papers uploaded yet.') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($unit->documents as $item)
                                @include('project-units._media-row', ['item' => $item])
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <x-modal name="upload-photo" max-width="md">
            <form method="POST" action="{{ route('unit-media.store', $unit) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="type" value="photo">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Upload Photos') }}</h2>
                <div>
                    <input type="file" name="files[]" accept="image/*" multiple required
                        class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                    <p class="text-xs text-gray-400 mt-1">{{ __('Photos are compressed automatically for fast loading.') }}</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                    <x-primary-button>{{ __('Upload') }}</x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="upload-layout" max-width="md">
            <form method="POST" action="{{ route('unit-media.store', $unit) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="type" value="layout">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Upload Layout') }}</h2>
                <div>
                    <input type="file" name="files[]" accept="image/*,application/pdf" multiple required
                        class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                    <x-primary-button>{{ __('Upload') }}</x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="upload-document" max-width="md">
            <form method="POST" action="{{ route('unit-media.store', $unit) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="type" value="document">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Upload Papers') }}</h2>
                <div>
                    <input type="file" name="files[]" accept="image/*,application/pdf" multiple required
                        class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                    <x-primary-button>{{ __('Upload') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
