@php
    $cost = $project->totalCost();
    $revenue = $project->totalRevenue();
    $invoiced = $project->totalInvoiced();
    $profit = $revenue - $cost;

    $unitsCount = $project->units->count();
    $costsCount = $project->costs->count();
    $canFinancials = \App\Support\Tenant::canFinancials('projects');
    $deleteConfirmMsg = "Delete \"{$project->name}\"? This permanently removes its {$unitsCount} unit(s) and {$costsCount} payment (kharcha) entries";
    if ($revenue > 0 && $canFinancials) {
        $deleteConfirmMsg .= ", including ".number_format($revenue, 0)." already recorded as received";
    }
    $deleteConfirmMsg .= ". Any quotations/invoices linked to it are kept but unlinked. This cannot be undone.";
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $project->name }}</h2>
                <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $project->type) }} @if($project->location) · {{ $project->location }} @endif</div>
            </div>
            <div class="flex items-center gap-3">
                <x-status-badge :status="$project->status" />
                <a href="{{ route('projects.edit', $project) }}" class="text-sm text-accent-600 hover:underline">{{ __('Edit') }}</a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm(@js($deleteConfirmMsg))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Delete Project') }}</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            @if ($canFinancials)
            {{-- P&L summary --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total cost') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($cost, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Booked value') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($invoiced, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Received') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($revenue, 0) }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Profit / Loss') }}</div>
                    <div class="mt-1 text-2xl font-semibold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profit, 0) }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ __('received − cost') }}</div>
                </div>
            </div>

            @if ($costsByCategory->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3">{{ __('Cost by category') }}</div>
                    <div class="flex flex-wrap gap-4 text-sm">
                        @foreach ($costsByCategory as $category => $amount)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-slate-700/50 rounded-md">
                                <span class="capitalize text-gray-600 dark:text-gray-400">{{ $category }}:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($amount, 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @endif

            {{-- Units --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between gap-4">
                    <div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Units / Properties') }} ({{ $project->units->count() }})</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Flats, plots or houses in this project that you plan to sell. "Price" here is what you will charge the customer.') }}</div>
                    </div>
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-unit')" class="shrink-0 inline-flex items-center px-3 py-1.5 bg-accent-600 text-white text-xs font-semibold rounded-md hover:bg-accent-700">{{ __('+ Add Unit') }}</button>
                </div>
                @if ($project->units->isNotEmpty())
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Unit') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Type') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Area (sqft)') }}</th>
                                @if ($canFinancials)
                                <th class="px-5 py-2 text-right">{{ __('Price') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Paid') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Balance') }}</th>
                                @endif
                                <th class="px-5 py-2 text-left">{{ __('Status') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Customer') }}</th>
                                <th class="px-5 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($project->units as $unit)
                                <tr>
                                    <td class="px-5 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $unit->unit_number }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $unit->type }}</td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ $unit->area_sqft ?? '—' }}</td>
                                    @if ($canFinancials)
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ number_format($unit->price, 0) }}</td>
                                    @if ($unit->customer)
                                        <td class="px-5 py-2 text-right text-green-600">{{ number_format($unit->totalCollected(), 0) }}</td>
                                        <td class="px-5 py-2 text-right {{ $unit->totalOutstanding() > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($unit->totalOutstanding(), 0) }}</td>
                                    @else
                                        <td class="px-5 py-2 text-right text-gray-300 dark:text-slate-600">—</td>
                                        <td class="px-5 py-2 text-right text-gray-300 dark:text-slate-600">—</td>
                                    @endif
                                    @endif
                                    <td class="px-5 py-2"><x-status-badge :status="$unit->status" /></td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400 min-w-[10rem]">
                                        @if ($unit->customer)
                                            <a href="{{ route('customers.show', $unit->customer) }}" class="text-accent-600 hover:underline block mb-1">{{ $unit->customer->name }}</a>
                                        @endif
                                        @if (\App\Support\Tenant::can('brokers') && $unit->broker)
                                            <div class="text-xs text-gray-400 mb-1">{{ __('Broker') }}: <a href="{{ route('brokers.show', $unit->broker) }}" class="text-accent-600 hover:underline">{{ $unit->broker->name }}</a></div>
                                        @endif
                                        <form method="POST" action="{{ route('project-units.assign') }}">
                                            @csrf
                                            <input type="hidden" name="project_unit_id" value="{{ $unit->id }}">
                                            <select name="customer_id" onchange="this.form.submit()" class="block w-full text-xs border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                                <option value="">{{ $unit->customer ? __('— Unassign —') : __('— Assign to —') }}</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" @selected($unit->customer_id === $customer->id)>{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-5 py-2 text-right whitespace-nowrap">
                                        <button type="button" x-data="" @click="$dispatch('open-modal', 'material-{{ $unit->id }}')" class="text-xs text-accent-600 hover:underline mr-3">{{ __('Material') }}</button>
                                        <form method="POST" action="{{ route('project-units.destroy', [$project, $unit]) }}" onsubmit="return confirm('{{ __('Remove this unit?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>

            <x-modal name="add-unit" :show="$errors->has('unit_number') || $errors->has('price')">
                <form method="POST" action="{{ route('project-units.store', $project) }}" class="p-6 space-y-4">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add a Unit / Property') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('This is a flat, plot or house you will sell — not a payment. "Selling Price" is the amount you will charge whoever buys it.') }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="unit_number" :value="__('Unit no.')" />
                            <x-text-input id="unit_number" name="unit_number" type="text" placeholder="A-101" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="type" :value="__('Type (optional)')" />
                            <x-text-input id="type" name="type" type="text" placeholder="2BHK" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="area_sqft" :value="__('Area in sqft (optional)')" />
                            <x-text-input id="area_sqft" name="area_sqft" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="price" :value="__('Selling Price (what customer pays)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                <option value="available">{{ __('Available — not sold yet') }}</option>
                                <option value="booked">{{ __('Booked — customer assigned') }}</option>
                                <option value="sold">{{ __('Sold — fully paid') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('+ Add Unit') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>

            @foreach ($project->units as $unit)
                <x-modal name="material-{{ $unit->id }}" max-width="lg">
                    <div class="p-6 space-y-4" x-data="{ direction: 'in' }">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Material Stock') }} — {{ $unit->unit_number }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Optional — only useful if a supervisor is logging material at this site. Nothing else depends on this.') }}</p>

                        @if ($unit->materialStock()->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($unit->materialStock() as $stock)
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-slate-700/50 rounded-md text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $stock['material_name'] }}:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ rtrim(rtrim(number_format($stock['balance'], 2), '0'), '.') }} {{ $stock['unit_label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('material-entries.store', $unit) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-t border-gray-100 dark:border-slate-700 pt-4">
                            @csrf
                            <div class="sm:col-span-2">
                                <x-input-label :value="__('Material name')" />
                                <x-text-input name="material_name" type="text" placeholder="{{ __('e.g. Cement, Bricks, Tiles') }}" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label :value="__('Quantity')" />
                                <x-text-input name="quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label :value="__('Unit (optional)')" />
                                <x-text-input name="unit_label" type="text" placeholder="{{ __('bags, pcs, kg') }}" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label :value="__('Type')" />
                                <select name="direction" x-model="direction" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                    <option value="in">{{ __('Received') }}</option>
                                    <option value="out">{{ __('Used') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label :value="__('Date')" />
                                <x-text-input name="entered_on" type="date" value="{{ now()->toDateString() }}" class="mt-1 block w-full" required />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label :value="__('Note (optional)')" />
                                <x-text-input name="note" type="text" class="mt-1 block w-full" />
                            </div>
                            <div class="sm:col-span-2 flex justify-end gap-3 pt-2">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Close') }}</x-secondary-button>
                                <x-primary-button>{{ __('+ Add Entry') }}</x-primary-button>
                            </div>
                        </form>

                        @if ($unit->materialEntries->isNotEmpty())
                            <div class="border-t border-gray-100 dark:border-slate-700 pt-3 max-h-56 overflow-y-auto space-y-1.5">
                                @foreach ($unit->materialEntries as $entry)
                                    <div x-data="{ editing: false }">
                                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 gap-2" x-show="!editing">
                                            <span>
                                                {{ $entry->entered_on->format('d M') }} —
                                                {{ $entry->material_name }}
                                                <span class="{{ $entry->direction === 'in' ? 'text-green-600' : 'text-red-600' }}">{{ $entry->direction === 'in' ? '+' : '-' }}{{ rtrim(rtrim(number_format($entry->quantity, 2), '0'), '.') }} {{ $entry->unit_label }}</span>
                                                @if ($entry->note) — {{ $entry->note }} @endif
                                            </span>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <button type="button" x-on:click="editing = true" class="text-accent-600 hover:underline">{{ __('Edit') }}</button>
                                                <form method="POST" action="{{ route('material-entries.destroy', [$unit, $entry]) }}" onsubmit="return confirm('{{ __('Remove this entry?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-500 hover:underline">{{ __('Remove') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('material-entries.update', [$unit, $entry]) }}" x-show="editing" x-cloak class="grid grid-cols-2 gap-1.5 bg-gray-50 dark:bg-slate-700/40 p-2 rounded-md">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="material_name" value="{{ $entry->material_name }}" required class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            <input type="number" step="0.01" min="0.01" name="quantity" value="{{ $entry->quantity }}" required class="text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            <input type="text" name="unit_label" value="{{ $entry->unit_label }}" placeholder="{{ __('unit') }}" class="text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            <select name="direction" class="text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                                <option value="in" @selected($entry->direction === 'in')>{{ __('Received') }}</option>
                                                <option value="out" @selected($entry->direction === 'out')>{{ __('Used') }}</option>
                                            </select>
                                            <input type="date" name="entered_on" value="{{ $entry->entered_on->format('Y-m-d') }}" required class="text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            <input type="text" name="note" value="{{ $entry->note }}" placeholder="{{ __('note') }}" class="col-span-2 text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
                                            <div class="col-span-2 flex justify-end gap-2 mt-1">
                                                <button type="button" x-on:click="editing = false" class="px-2 py-1 text-[11px] text-gray-500 dark:text-gray-400">{{ __('Cancel') }}</button>
                                                <button class="px-2 py-1 bg-accent-600 text-white text-[11px] font-semibold rounded hover:bg-accent-700">{{ __('Save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </x-modal>
            @endforeach

            {{-- Costs --}}
            @if ($canFinancials)
            <div x-data="{
                    fixedCategories: ['land', 'construction', 'material', 'labor', 'approval', 'marketing'],
                    editingCost: { id: null, categorySelect: 'land', categoryOther: '', description: '', amount: '', spent_on: '', vendor: '', payment_account_id: '', is_credit: false, notes: '', bill_name: null },
                    openEdit(cost) {
                        const isFixed = this.fixedCategories.includes(cost.category);
                        this.editingCost = {
                            ...cost,
                            categorySelect: isFixed ? cost.category : 'other',
                            categoryOther: isFixed ? '' : cost.category,
                        };
                        $dispatch('open-modal', 'edit-cost');
                    },
                 }">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between gap-4">
                    <div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Payments (Kharcha)') }} ({{ $project->costs->count() }})</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Money you have spent on this project — land, material, labor, etc.') }}</div>
                    </div>
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-cost')" class="shrink-0 inline-flex items-center px-3 py-1.5 bg-accent-600 text-white text-xs font-semibold rounded-md hover:bg-accent-700">{{ __('+ Add Payment') }}</button>
                </div>
                @if ($project->costs->isNotEmpty())
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Date') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Category') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Description') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Vendor') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Paid From') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Bill') }}</th>
                                <th class="px-5 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($project->costs as $entry)
                                <tr>
                                    <td class="px-5 py-2 text-gray-500 dark:text-gray-400">{{ $entry->spent_on->format('d M Y') }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400 capitalize">{{ $entry->category }}</td>
                                    <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $entry->description }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $entry->vendor }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">
                                        @if ($entry->isOutstandingCredit())
                                            <a href="{{ route('material-credit.index') }}" class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:underline">{{ __('Udhar (baki)') }}</a>
                                        @elseif ($entry->is_credit)
                                            {{ $entry->account?->label() ?? '—' }} <span class="text-xs text-gray-400">({{ __('udhar paid') }})</span>
                                        @else
                                            {{ $entry->account?->label() ?? '—' }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ number_format($entry->amount, 2) }}</td>
                                    <td class="px-5 py-2">
                                        @if ($entry->bill_path)
                                            <a href="{{ route('project-costs.bill', [$project, $entry]) }}" target="_blank" rel="noopener" class="text-accent-600 hover:underline text-xs">{{ __('View') }}</a>
                                        @else
                                            <span class="text-gray-300 dark:text-slate-600 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-right whitespace-nowrap">
                                        <button type="button" @click="openEdit(@js([
                                            'id' => $entry->id,
                                            'category' => $entry->category,
                                            'description' => $entry->description,
                                            'amount' => (float) $entry->amount,
                                            'spent_on' => $entry->spent_on->format('Y-m-d'),
                                            'vendor' => $entry->vendor,
                                            'payment_account_id' => $entry->payment_account_id,
                                            'is_credit' => $entry->is_credit && ! $entry->credit_settled_at,
                                            'notes' => $entry->notes,
                                            'bill_name' => $entry->bill_name,
                                        ]))" class="text-xs text-accent-600 hover:underline mr-3">{{ __('Edit') }}</button>
                                        <form method="POST" action="{{ route('project-costs.destroy', [$project, $entry]) }}" onsubmit="return confirm('{{ __('Remove this entry?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>

            <x-modal name="add-cost" :show="$errors->has('category') || $errors->has('description') || $errors->has('amount') || $errors->has('spent_on') || $errors->has('bill')">
                <form method="POST" action="{{ route('project-costs.store', $project) }}" enctype="multipart/form-data" class="p-6 space-y-4" x-data="{ category: 'land', isCredit: false }">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add a Payment') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Record money you paid out for this project.') }}</p>

                    <div>
                        <x-input-label for="category" :value="__('What was it for?')" />
                        <select id="category" name="category" x-model="category" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="land">{{ __('Land') }}</option>
                            <option value="construction">{{ __('Construction') }}</option>
                            <option value="material">{{ __('Material') }}</option>
                            <option value="labor">{{ __('Labor') }}</option>
                            <option value="approval">{{ __('Government / Approvals') }}</option>
                            <option value="marketing">{{ __('Marketing') }}</option>
                            <option value="other">{{ __('Other — type my own') }}</option>
                        </select>
                    </div>

                    <div x-show="category === 'other'" x-cloak>
                        <x-input-label for="category_other" :value="__('Custom category name')" />
                        <x-text-input id="category_other" name="category_other" type="text" placeholder="{{ __('e.g. Electricity Bill') }}" class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <x-text-input id="description" name="description" type="text" placeholder="{{ __('e.g. Cement 20 bags') }}" class="mt-1 block w-full" required />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="spent_on" :value="__('Date')" />
                            <x-text-input id="spent_on" name="spent_on" type="date" value="{{ now()->toDateString() }}" class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="vendor" :value="__('Paid to / Vendor (optional)')" />
                        <x-text-input id="vendor" name="vendor" type="text" placeholder="{{ __('e.g. Ram Lal Cement Store, Contractor name') }}" class="mt-1 block w-full" />
                        <p class="mt-1 text-xs text-gray-400">{{ __('Who you gave this money to. Leave blank if not needed.') }}</p>
                    </div>

                    <div class="flex items-start gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-md p-3">
                        <input id="is_credit" name="is_credit" type="checkbox" value="1" x-model="isCredit" class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-accent-600 focus:ring-accent-500">
                        <label for="is_credit" class="text-sm text-amber-800 dark:text-amber-300">
                            {{ __('Udhar liya — vendor se maal/labour liya, abhi payment nahi kiya.') }}
                            <span class="block text-xs text-amber-700/80 dark:text-amber-400/80 mt-0.5">{{ __('Shows on the Material Udhar page until you mark it paid — no account is picked now.') }}</span>
                        </label>
                    </div>

                    @if ($paymentAccounts->isNotEmpty())
                        <div x-show="!isCredit" x-cloak>
                            <x-input-label for="payment_account_id" :value="__('Paid From (optional)')" />
                            <select id="payment_account_id" name="payment_account_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                <option value="">{{ __('— Not specified —') }}</option>
                                @foreach ($paymentAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->label() }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">{{ __('Which account or person\'s cash this was paid from.') }}</p>
                        </div>
                    @endif

                    <div>
                        <x-input-label for="notes" :value="__('Notes (optional)')" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500"></textarea>
                    </div>

                    <div>
                        <x-input-label for="bill" :value="__('Upload Bill / Receipt (optional)')" />
                        <input id="bill" name="bill" type="file" accept="image/*,.pdf" capture="environment"
                            class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                        <p class="mt-1 text-xs text-gray-400">{{ __('Photo or PDF of the bill/receipt. On mobile this can open your camera directly.') }}</p>
                        <x-input-error :messages="$errors->get('bill')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('+ Add Payment') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>

            <x-modal name="edit-cost">
                <form method="POST" :action="'{{ route('project-costs.update', [$project, '__ID__']) }}'.replace('__ID__', editingCost.id)" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Payment') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Update this expense entry.') }}</p>

                    <div>
                        <x-input-label for="edit_category" :value="__('What was it for?')" />
                        <select id="edit_category" name="category" x-model="editingCost.categorySelect" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <option value="land">{{ __('Land') }}</option>
                            <option value="construction">{{ __('Construction') }}</option>
                            <option value="material">{{ __('Material') }}</option>
                            <option value="labor">{{ __('Labor') }}</option>
                            <option value="approval">{{ __('Government / Approvals') }}</option>
                            <option value="marketing">{{ __('Marketing') }}</option>
                            <option value="other">{{ __('Other — type my own') }}</option>
                        </select>
                    </div>

                    <div x-show="editingCost.categorySelect === 'other'" x-cloak>
                        <x-input-label for="edit_category_other" :value="__('Custom category name')" />
                        <input id="edit_category_other" name="category_other" type="text" x-model="editingCost.categoryOther" placeholder="{{ __('e.g. Electricity Bill') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>

                    <div>
                        <x-input-label for="edit_description" :value="__('Description')" />
                        <input id="edit_description" name="description" type="text" x-model="editingCost.description" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_amount" :value="__('Amount')" />
                            <input id="edit_amount" name="amount" type="number" step="0.01" min="0.01" x-model="editingCost.amount" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                        <div>
                            <x-input-label for="edit_spent_on" :value="__('Date')" />
                            <input id="edit_spent_on" name="spent_on" type="date" x-model="editingCost.spent_on" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                    </div>

                    <div>
                        <x-input-label for="edit_vendor" :value="__('Paid to / Vendor (optional)')" />
                        <input id="edit_vendor" name="vendor" type="text" x-model="editingCost.vendor" placeholder="{{ __('e.g. Ram Lal Cement Store, Contractor name') }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    </div>

                    <div class="flex items-start gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-md p-3">
                        <input id="edit_is_credit" name="is_credit" type="checkbox" value="1" x-model="editingCost.is_credit" class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-accent-600 focus:ring-accent-500">
                        <label for="edit_is_credit" class="text-sm text-amber-800 dark:text-amber-300">
                            {{ __('Udhar liya — vendor se maal/labour liya, abhi payment nahi kiya.') }}
                            <span class="block text-xs text-amber-700/80 dark:text-amber-400/80 mt-0.5">{{ __('Shows on the Material Udhar page until you mark it paid — no account is picked now.') }}</span>
                        </label>
                    </div>

                    @if ($paymentAccounts->isNotEmpty())
                        <div x-show="!editingCost.is_credit" x-cloak>
                            <x-input-label for="edit_payment_account_id" :value="__('Paid From (optional)')" />
                            <select id="edit_payment_account_id" name="payment_account_id" x-model="editingCost.payment_account_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                <option value="">{{ __('— Not specified —') }}</option>
                                @foreach ($paymentAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <x-input-label for="edit_notes" :value="__('Notes (optional)')" />
                        <textarea id="edit_notes" name="notes" rows="2" x-model="editingCost.notes" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500"></textarea>
                    </div>

                    <div>
                        <x-input-label for="edit_bill" :value="__('Bill / Receipt')" />
                        <p x-show="editingCost.bill_name" class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Current file:') }} <span x-text="editingCost.bill_name"></span></p>
                        <input id="edit_bill" name="bill" type="file" accept="image/*,.pdf" capture="environment"
                            class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                        <p class="mt-1 text-xs text-gray-400">{{ __('Leave blank to keep the existing bill. Upload a new file to replace it.') }}</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>
            </div>
            @endif

            {{-- Quotations & Invoices --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ __('Quotations') }}</span>
                        <a href="{{ route('quotations.create') }}" class="text-xs text-accent-600 hover:underline">{{ __('+ New') }}</a>
                    </div>
                    @if ($project->quotations->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($project->quotations as $quotation)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-accent-600 hover:underline">{{ $quotation->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($quotation->total, 0) }}</span>
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
                    @if ($project->invoices->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                    @else
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach ($project->invoices as $invoice)
                                <li class="px-5 py-3 flex items-center justify-between">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-accent-600 hover:underline">{{ $invoice->number }}</a>
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-600 dark:text-gray-400">{{ number_format($invoice->total, 0) }}</span>
                                        <x-status-badge :status="$invoice->status" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
