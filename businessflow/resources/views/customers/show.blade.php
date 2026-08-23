@php
    $totalPaid = $customer->units->sum(fn ($u) => $u->totalPaid());
    $totalDue = $customer->units->sum(fn ($u) => $u->balanceDue());
    $totalValue = $customer->units->sum(fn ($u) => $u->price);
    $unitCount = $customer->units->count();
    $activeUnitCount = $customer->units->where('status', '!=', 'sold')->count();
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
                    <div class="h-14 w-14 shrink-0 rounded-lg bg-accent-100 dark:bg-slate-700 text-accent-700 dark:text-accent-100 flex items-center justify-center text-2xl font-semibold">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
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
                        </div>
                        @if ($customer->notes)
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $customer->notes }}</div>
                        @endif
                    </div>
                </div>
                <div class="flex lg:flex-col gap-2 shrink-0">
                    <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700 text-center">{{ __('Edit') }}</a>
                    <a href="{{ route('customers.statement', $customer) }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700 text-center">{{ __('Statement') }}</a>
                    <a href="#documents" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700 text-center">{{ __('Documents') }}</a>
                    <a href="#followups" class="px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700 text-center">{{ __('+ Follow-up') }}</a>
                </div>
            </div>

            {{-- Summary stat cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Properties') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $unitCount }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $activeUnitCount }} {{ __('active') }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Value') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalValue, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Collected') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600">{{ number_format($totalPaid, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $totalDue > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($totalDue, 0) }}</div>
                </div>
            </div>

            {{-- Properties --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Properties') }} ({{ $unitCount }})</span>
                    <a href="{{ route('customers.statement', $customer) }}" class="text-xs text-accent-600 hover:underline">{{ __('Download full statement') }}</a>
                </div>

                @forelse ($customer->units as $unit)
                    @php $progress = $unit->price > 0 ? min(100, ($unit->totalPaid() / $unit->price) * 100) : 0; @endphp
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700 last:border-b-0">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('projects.show', $unit->project) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent-600">{{ $unit->project->name }}</a>
                                    <span class="text-gray-400">·</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $unit->unit_number }}</span>
                                    <x-status-badge :status="$unit->status" />
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $unit->type }}{{ $unit->area_sqft ? ' · '.$unit->area_sqft.' sqft' : '' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-400">{{ __('Price') }}</div>
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($unit->price, 0) }}</div>
                            </div>
                        </div>

                        <div class="mt-3 h-1.5 rounded-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full bg-green-500" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="mt-1.5 flex flex-wrap gap-x-6 gap-y-1 text-xs">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Paid') }}: <strong class="text-green-600">{{ number_format($unit->totalPaid(), 0) }}</strong></span>
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Balance') }}: <strong class="{{ $unit->balanceDue() > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($unit->balanceDue(), 0) }}</strong></span>
                        </div>

                        @if ($unit->invoices->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-3 text-xs">
                                @foreach ($unit->invoices as $unitInvoice)
                                    <a href="{{ route('invoices.show', $unitInvoice) }}" class="text-accent-600 hover:underline">{{ $unitInvoice->number }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No properties assigned yet.') }}</div>
                @endforelse

                <div class="p-5 border-t border-gray-100 dark:border-slate-700">
                    <form method="POST" action="{{ route('project-units.assign') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <div class="sm:col-span-2">
                            <x-project-unit-select :projects="$projects" />
                        </div>
                        <div>
                            <x-primary-button class="w-full justify-center">{{ __('+ Assign Property') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Quotations') }}</span>
                        <a href="{{ route('quotations.create') }}" class="text-xs text-accent-600 hover:underline">{{ __('+ New') }}</a>
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
                        <a href="{{ route('invoices.create') }}" class="text-xs text-accent-600 hover:underline">{{ __('+ New') }}</a>
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

            {{-- Follow-ups --}}
            <div id="followups" class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden scroll-mt-6">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Follow-ups & reminders') }}</div>

                @if ($customer->followups->isNotEmpty())
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($customer->followups as $followup)
                            <li class="px-5 py-3 flex items-center justify-between gap-4 {{ $followup->status === 'done' ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
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
                @endif

                <form method="POST" action="{{ route('followups.store') }}" class="p-5 border-t border-gray-100 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div class="sm:col-span-2">
                        <x-input-label for="note" :value="__('Note (e.g. promised payment, site visit)')" class="text-xs" />
                        <x-text-input id="note" name="note" type="text" class="mt-1 block w-full text-sm" required placeholder="{{ __('e.g. Said will pay ₹50,000 by 25th') }}" />
                    </div>
                    <div>
                        <x-input-label for="due_at" :value="__('Due')" class="text-xs" />
                        <input id="due_at" name="due_at" type="datetime-local" value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" required
                            class="mt-1 block w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>
                    <div>
                        <x-primary-button class="w-full justify-center">{{ __('+ Add') }}</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Documents --}}
            <div id="documents" class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden scroll-mt-6">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Documents') }}</div>

                @if ($customer->documents->isNotEmpty())
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
                @else
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No documents uploaded yet.') }}</div>
                @endif

                <form method="POST" action="{{ route('customer-documents.store', $customer) }}" enctype="multipart/form-data" class="p-5 border-t border-gray-100 dark:border-slate-700 flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <x-input-label for="file" :value="__('Upload file (ID proof, agreement, receipt...)')" class="text-xs" />
                        <input id="file" name="file" type="file" required
                            class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                    </div>
                    <x-primary-button>{{ __('Upload') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
