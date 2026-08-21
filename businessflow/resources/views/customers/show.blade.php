<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $customer->name }}</h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('customers.statement', $customer) }}" class="text-sm text-accent-600 hover:underline">{{ __('Download Statement') }}</a>
                <a href="{{ route('customers.edit', $customer) }}" class="text-sm text-accent-600 hover:underline">{{ __('Edit') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Company') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->company ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Phone') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->phone ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Email') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->email ?: '—' }}</div>
                </div>
                <div class="sm:col-span-3">
                    <div class="text-gray-500 dark:text-gray-400">{{ __('Address') }}</div>
                    <div class="text-gray-900 dark:text-gray-100">{{ $customer->address ?: '—' }}</div>
                </div>
                @if ($customer->notes)
                    <div class="sm:col-span-3">
                        <div class="text-gray-500 dark:text-gray-400">{{ __('Notes') }}</div>
                        <div class="text-gray-900 dark:text-gray-100">{{ $customer->notes }}</div>
                    </div>
                @endif
            </div>

            {{-- Assign a property --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Assign a Property') }}</div>
                <form method="POST" action="{{ route('project-units.assign') }}" class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div class="sm:col-span-2">
                        <x-project-unit-select :projects="$projects" />
                    </div>
                    <div>
                        <x-primary-button class="w-full justify-center">{{ __('Assign to '.$customer->name) }}</x-primary-button>
                    </div>
                </form>
            </div>

            @if ($customer->units->isNotEmpty())
                @php
                    $totalPaid = $customer->units->sum(fn ($u) => $u->totalPaid());
                    $totalDue = $customer->units->sum(fn ($u) => $u->balanceDue());
                @endphp
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Properties Purchased') }}</div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Project') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Unit') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Price') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Paid') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Balance') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($customer->units as $unit)
                                <tr>
                                    <td class="px-5 py-2">
                                        <a href="{{ route('projects.show', $unit->project) }}" class="text-accent-600 hover:underline">{{ $unit->project->name }}</a>
                                    </td>
                                    <td class="px-5 py-2 text-gray-900 dark:text-gray-100 font-medium">{{ $unit->unit_number }}</td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($unit->price, 0) }}</td>
                                    <td class="px-5 py-2 text-right text-green-600">{{ number_format($unit->totalPaid(), 0) }}</td>
                                    <td class="px-5 py-2 text-right {{ $unit->balanceDue() > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($unit->balanceDue(), 0) }}</td>
                                    <td class="px-5 py-2"><x-status-badge :status="$unit->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-gray-200 dark:border-slate-700 font-medium">
                            <tr>
                                <td class="px-5 py-2" colspan="3">{{ __('Total') }}</td>
                                <td class="px-5 py-2 text-right text-green-600">{{ number_format($totalPaid, 0) }}</td>
                                <td class="px-5 py-2 text-right {{ $totalDue > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($totalDue, 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

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
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Follow-ups') }}</div>

                @if ($customer->followups->isNotEmpty())
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($customer->followups as $followup)
                            <li class="px-5 py-3 flex items-center justify-between gap-4 {{ $followup->status === 'done' ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <div class="text-gray-900 dark:text-gray-100 truncate">{{ $followup->note }}</div>
                                    <div class="text-xs {{ $followup->status === 'pending' && $followup->due_at->isPast() ? 'text-red-500' : 'text-gray-400' }}">
                                        {{ $followup->due_at->format('d M Y, h:i A') }}
                                        @if ($followup->project) · {{ $followup->project->name }} @endif
                                        @if ($followup->status === 'done') · {{ __('Done') }} @endif
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
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
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
