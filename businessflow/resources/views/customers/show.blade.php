@php
    $activeUnits = $customer->units->whereNull('archived_at');
    $historyUnits = $customer->units->whereNotNull('archived_at');
    // Only currently-active properties count toward these — once a
    // property is fully closed (paid off / written off) there's nothing
    // left to collect on it, so it shouldn't inflate the live totals.
    $totalPaid = $activeUnits->sum(fn ($u) => $u->totalCollected());
    $totalDue = $activeUnits->sum(fn ($u) => $u->totalOutstanding());
    $totalValue = $activeUnits->sum(fn ($u) => $u->price);
    $unitCount = $customer->units->count();
    $activeUnitCount = $activeUnits->count();
    $primaryUnit = $unitCount === 1 ? $customer->units->first() : null;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $customer->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Identity card --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="h-14 w-14 shrink-0 rounded-lg overflow-hidden bg-accent-100 dark:bg-slate-700 text-accent-700 dark:text-accent-100 flex items-center justify-center text-2xl font-semibold">
                        @if ($customer->photo_path)
                            <img src="{{ route('customers.photo', $customer) }}" alt="{{ $customer->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $customer->name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">#{{ $customer->id }}</span>
                            @if ($unitCount > 0)
                                <span class="text-xs px-2 py-0.5 rounded font-medium {{ $totalDue > 0 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $totalDue > 0 ? __('Active') : __('Paid Up') }}
                                </span>
                            @endif
                        </div>
                        @if ($customer->company)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $customer->company }}</div>
                        @endif
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1.5 text-sm text-gray-600 dark:text-gray-400">
                            @if ($customer->phone)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 00-1.173.417l-.97 1.293a.9.9 0 01-1.05.256 11.267 11.267 0 01-5.146-5.146.9.9 0 01.256-1.05l1.293-.97a1.125 1.125 0 00.417-1.173L7.964 3.102a1.125 1.125 0 00-1.091-.852H5.5A2.25 2.25 0 003.25 4.5v1.5c0 .414.336.75.75.75z" /></svg>
                                    {{ $customer->phone }}
                                </span>
                            @endif
                            @if ($customer->email)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                    {{ $customer->email }}
                                </span>
                            @endif
                            @if ($customer->address)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                    {{ $customer->address }}
                                </span>
                            @endif
                            @if ($customer->source)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                                    {{ __('Source') }}: {{ $customer->source }}
                                </span>
                            @endif
                            @if ($customer->aadhar_path)
                                <a href="{{ route('customers.aadhar', $customer) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-accent-600 hover:underline">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ __('Aadhar on file') }}
                                </a>
                            @endif
                        </div>
                        @if ($customer->notes)
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $customer->notes }}</div>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col gap-2 shrink-0">
                    <div class="grid grid-cols-3 gap-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('customers.statement', $customer) }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-accent-100 dark:border-slate-600 bg-accent-50 dark:bg-slate-700 text-accent-700 dark:text-accent-100 hover:bg-accent-100 dark:hover:bg-slate-600">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            {{ __('Statement') }}
                        </a>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'upload-document')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-19.5 0v6a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25v-6m-19.5 0h19.5M2.25 12.75L4.06 5.19A2.25 2.25 0 016.243 3.75h11.514a2.25 2.25 0 012.183 1.44l1.81 7.56" /></svg>
                            {{ __('Documents') }}
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-followup')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            {{ __('Follow-up') }}
                        </button>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'commitments')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ __('Commitment') }}
                        </button>
                        @if ($historyUnits->isNotEmpty())
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'history')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ __('History') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Summary stat cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Properties') }}</div>
                    @if ($primaryUnit)
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $primaryUnit->project->name }} · {{ $primaryUnit->unit_number }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">#{{ $primaryUnit->id }} · {{ $primaryUnit->isArchived() ? __('closed') : __('active') }}</div>
                    @else
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $unitCount }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $activeUnitCount }} {{ __('active') }}</div>
                    @endif
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Value') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($totalValue, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Collected') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600">₹{{ number_format($totalPaid, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $totalDue > 0 ? 'text-red-600' : 'text-gray-400' }}">₹{{ number_format($totalDue, 0) }}</div>
                </div>
            </div>

            {{-- Properties (active) --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Properties') }} ({{ $activeUnitCount }})</span>
                    <a href="{{ route('customers.statement', $customer) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium border border-accent-100 dark:border-slate-600 bg-accent-50 dark:bg-slate-700 text-accent-700 dark:text-accent-100 hover:bg-accent-100 dark:hover:bg-slate-600">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        {{ __('Download full statement') }}
                    </a>
                </div>

                @forelse ($activeUnits as $unit)
                    @php $progress = $unit->price > 0 ? min(100, ($unit->totalCollected() / $unit->price) * 100) : 0; @endphp
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700 last:border-b-0">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('projects.show', $unit->project) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $unit->project->name }}</a>
                                    <span class="text-gray-400">·</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $unit->unit_number }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">#{{ $unit->id }}</span>
                                    <x-status-badge :status="$unit->status" />
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $unit->type }}{{ $unit->area_sqft ? ' · '.$unit->area_sqft.' sqft' : '' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-400">{{ __('Price') }}</div>
                                <div class="font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($unit->price, 0) }}</div>
                            </div>
                        </div>

                        <div class="mt-3 h-1.5 rounded-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full bg-green-500" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="mt-1.5 flex flex-wrap gap-x-6 gap-y-1 text-xs">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Collected') }}: <strong class="text-green-600">₹{{ number_format($unit->totalCollected(), 0) }}</strong></span>
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}: <strong class="{{ $unit->totalOutstanding() > 0 ? 'text-red-600' : 'text-gray-400' }}">₹{{ number_format($unit->totalOutstanding(), 0) }}</strong></span>
                        </div>

                        @if ($unit->invoices->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-3 text-xs">
                                @foreach ($unit->invoices as $unitInvoice)
                                    <a href="{{ route('invoices.show', $unitInvoice) }}" class="text-accent-600 hover:underline">{{ $unitInvoice->number }}</a>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4">
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'record-payment-{{ $unit->id }}')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg bg-accent-600 text-white text-sm font-semibold hover:bg-accent-700">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                {{ __('Record Payment') }}
                            </button>
                        </div>

                        <x-unit-payment-ledger :unit="$unit" :editable="true" />

                        @if ($unit->totalOutstanding() > 0)
                            <details class="mt-3 group">
                                <summary class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-red-200 dark:border-red-900 text-red-600 dark:text-red-400 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-900/20 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ __('Write off remaining balance') }}
                                </summary>
                                <form method="POST" action="{{ route('project-units.write-off', $unit) }}" class="mt-2 flex flex-wrap items-end gap-2 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900 rounded-lg p-3" onsubmit="return confirm('{{ __('Write off the remaining outstanding balance for this property? This moves it to History.') }}')">
                                    @csrf
                                    <div class="flex-1 min-w-[200px]">
                                        <label class="text-[11px] text-gray-500 dark:text-gray-400">{{ __('Reason (optional)') }}</label>
                                        <input type="text" name="note" placeholder="{{ __('e.g. Customer unable to pay, settled verbally') }}" class="mt-0.5 block w-full text-sm rounded-md border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-red-500 focus:ring-red-500">
                                    </div>
                                    <button class="inline-flex items-center justify-center h-9 px-4 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-500 whitespace-nowrap">{{ __('Confirm Write-off') }} (₹{{ number_format($unit->totalOutstanding(), 0) }})</button>
                                </form>
                            </details>
                        @endif
                    </div>

                    <x-modal name="record-payment-{{ $unit->id }}" max-width="md">
                        <form method="POST" action="{{ route('unit-payments.store', $unit) }}" x-data="{ purpose: 'installment' }" class="p-6 space-y-4">
                            @csrf
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Record Payment') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $unit->project->name }} · {{ $unit->unit_number }}</p>

                            <div>
                                <x-input-label for="purpose-{{ $unit->id }}" :value="__('Payment for')" />
                                <select id="purpose-{{ $unit->id }}" name="purpose" x-model="purpose" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                    @foreach (\App\Models\UnitPayment::PURPOSES as $val => $label)
                                        <option value="{{ $val }}" @selected($val === 'installment')>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="purpose === 'other'" x-cloak>
                                <x-input-label for="purpose_other-{{ $unit->id }}" :value="__('If Other, specify')" />
                                <x-text-input id="purpose_other-{{ $unit->id }}" name="purpose_other" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. Parking charges') }}" />
                            </div>

                            <div>
                                <x-input-label for="description-{{ $unit->id }}" :value="__('Description (optional)')" />
                                <x-text-input id="description-{{ $unit->id }}" name="description" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. 2nd installment as per agreement') }}" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="amount-{{ $unit->id }}" :value="__('Amount')" />
                                    <input id="amount-{{ $unit->id }}" type="number" step="0.01" min="0.01" name="amount" required placeholder="₹" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                </div>
                                <div>
                                    <x-input-label for="paid_at-{{ $unit->id }}" :value="__('Date')" />
                                    <input id="paid_at-{{ $unit->id }}" type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                </div>
                                <div>
                                    <x-input-label for="method-{{ $unit->id }}" :value="__('Method')" />
                                    <select id="method-{{ $unit->id }}" name="method" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                        <option value="cash">{{ __('Cash') }}</option>
                                        <option value="upi">{{ __('UPI') }}</option>
                                        <option value="bank_transfer">{{ __('Bank transfer') }}</option>
                                        <option value="cheque">{{ __('Cheque') }}</option>
                                        <option value="card">{{ __('Card') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="reference-{{ $unit->id }}" :value="__('Reference (optional)')" />
                                    <x-text-input id="reference-{{ $unit->id }}" name="reference" type="text" class="mt-1 block w-full" />
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                                <x-primary-button>{{ __('+ Record Payment') }}</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No properties assigned yet.') }}</div>
                @endforelse

                <div class="p-5 border-t border-gray-100 dark:border-slate-700">
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'assign-property')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        {{ __('Assign Property') }}
                    </button>
                </div>
            </div>

            <x-modal name="assign-property" max-width="lg">
                <div class="p-6" x-data="{ customerId: {{ $customer->id }} }">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Assign a Property') }}</h2>
                    <form method="POST" action="{{ route('project-units.assign') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <x-project-unit-select :projects="$projects" />
                        <div>
                            <x-input-label for="assign_commitment_date" :value="__('Commitment date (optional)')" />
                            <input id="assign_commitment_date" type="date" name="commitment_date" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                            <x-primary-button>{{ __('+ Assign Property') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="commitments" max-width="lg">
                <div class="p-6 space-y-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Possession Commitments') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('The handover date promised for each active property. We\'ll remind you here and in the notification bell if one goes overdue.') }}</p>

                    @forelse ($activeUnits as $unit)
                        @php $cs = $unit->commitmentStatus(); @endphp
                        <form method="POST" action="{{ route('project-units.commitment', $unit) }}" class="border border-gray-200 dark:border-slate-700 rounded-lg p-4 space-y-3">
                            @csrf
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $unit->project->name }} · {{ $unit->unit_number }} <span class="text-xs text-gray-400">#{{ $unit->id }}</span></div>
                                @if ($cs === 'overdue')
                                    <span class="text-xs px-2 py-0.5 rounded font-medium bg-red-100 text-red-700">{{ __('Overdue') }}</span>
                                @elseif ($cs === 'upcoming')
                                    <span class="text-xs px-2 py-0.5 rounded font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">{{ __('Upcoming') }}</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="commitment_date-{{ $unit->id }}" :value="__('Commitment date')" class="text-xs" />
                                    <input id="commitment_date-{{ $unit->id }}" type="date" name="commitment_date" value="{{ $unit->commitment_date?->format('Y-m-d') }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                </div>
                                <div>
                                    <x-input-label for="commitment_note-{{ $unit->id }}" :value="__('Note (optional)')" class="text-xs" />
                                    <input id="commitment_note-{{ $unit->id }}" type="text" name="commitment_note" value="{{ $unit->commitment_note }}" placeholder="{{ __('e.g. Handover with full finishing') }}" class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                </div>
                            </div>
                            <div class="text-right">
                                <button class="px-3 py-1.5 bg-accent-600 text-white text-xs font-semibold rounded-md hover:bg-accent-700">{{ __('Save') }}</button>
                            </div>
                        </form>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('No active properties to set a commitment on.') }}</div>
                    @endforelse

                    <div class="flex justify-end pt-2">
                        <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Close') }}</button>
                    </div>
                </div>
            </x-modal>

            {{-- History: fully paid off or written off. Opens from the header's "History" button so it doesn't clutter the active view. --}}
            @if ($historyUnits->isNotEmpty())
                <x-modal name="history" max-width="2xl">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">
                        {{ __('History') }} ({{ $historyUnits->count() }})
                    </div>
                    <div class="max-h-[70vh] overflow-y-auto">
                        @foreach ($historyUnits as $unit)
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
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ __('Closed on') }} {{ $unit->archived_at->format('d M Y') }}
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

                                <x-unit-payment-ledger :unit="$unit" :editable="false" />
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 text-right">
                        <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Close') }}</button>
                    </div>
                </x-modal>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Quotations') }}</span>
                        <a href="{{ route('quotations.create', ['customer_id' => $customer->id]) }}" class="text-xs text-accent-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($customer->quotations->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($customer->quotations as $quotation)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-accent-600 hover:underline">{{ $quotation->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($quotation->total, 2) }}</span>
                                        <x-status-badge :status="$quotation->status" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Invoices') }}</span>
                        <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="text-xs text-accent-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($customer->invoices->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($customer->invoices as $invoice)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-accent-600 hover:underline">{{ $invoice->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($invoice->total, 2) }}</span>
                                        <x-status-badge :status="$invoice->status" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Follow-ups — reminders to us, e.g. "customer said will pay X by date Y". Not the same thing as a completion/possession commitment (that's a separate, still-being-designed feature). Adding one happens via the header's "Follow-up" button (popup); this section only shows when there's something to list. --}}
            @if ($customer->followups->isNotEmpty())
            <div id="followups" class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden scroll-mt-6">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Follow-ups') }}</div>

                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($customer->followups as $followup)
                            <li class="px-5 py-3 flex items-center justify-between gap-4 {{ $followup->status === 'done' ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-accent-100 dark:bg-slate-700 text-accent-700 dark:text-accent-100">{{ $followup->categoryLabel() }}</span>
                                        <x-status-badge :status="$followup->status" />
                                        @if ($followup->status === 'pending' && $followup->due_at->isPast())
                                            <span class="text-xs px-2 py-0.5 rounded font-medium bg-red-100 text-red-700">{{ __('Overdue') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-gray-900 dark:text-gray-100 truncate mt-1">{{ $followup->note }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ $followup->due_at->format('d M Y, h:i A') }}
                                        @if ($followup->project) · {{ $followup->project->name }} @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    @if ($followup->status === 'pending')
                                        @if ($url = $followup->whatsappUrl())
                                            <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center px-2.5 py-1 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">{{ __('WhatsApp') }}</a>
                                        @endif
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
            </div>
            @endif

            <x-modal name="add-followup" :show="$errors->has('note') || $errors->has('due_at')">
                <form method="POST" action="{{ route('followups.store') }}" x-data="{ category: 'general' }" class="p-6 space-y-4">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Follow-up') }}</h2>
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">

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
                        <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" required placeholder="{{ __('e.g. Said will pay ₹50,000 by 25th') }}" />
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

            {{-- Documents --}}
            @if ($customer->documents->isNotEmpty())
            <div id="documents" class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden scroll-mt-6">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Documents') }}</div>

                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($customer->documents as $document)
                            <li class="px-5 py-3 flex items-center justify-between gap-4">
                                <a href="{{ route('customer-documents.download', [$customer, $document]) }}" class="flex items-center gap-2 min-w-0 text-accent-600 hover:underline">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    <span class="truncate">{{ $document->name }}</span>
                                </a>
                                <div class="flex items-center gap-3 shrink-0 text-xs text-gray-400">
                                    <span>{{ $document->humanSize() }}</span>
                                    <span>{{ $document->created_at->format('d M Y') }}</span>
                                    <form method="POST" action="{{ route('customer-documents.destroy', [$customer, $document]) }}" onsubmit="return confirm('{{ __('Delete this document?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
            </div>
            @endif

            <x-modal name="upload-document" :show="$errors->has('file')">
                <form method="POST" action="{{ route('customer-documents.store', $customer) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Upload Document') }}</h2>
                    <div>
                        <x-input-label for="file" :value="__('File (ID proof, agreement, receipt...)')" />
                        <input id="file" name="file" type="file" required
                            class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                        <x-primary-button>{{ __('Upload') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>
        </div>
    </div>
</x-app-layout>
