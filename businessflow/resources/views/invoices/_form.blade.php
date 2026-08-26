@php
    $invoice = $invoice ?? null;
    $prefillProjectId = $prefillProjectId ?? null;
    $prefillUnitId = $prefillUnitId ?? null;
    $prefillCustomerId = $prefillCustomerId ?? null;
@endphp

<div x-data="{
        customerId: '{{ old('customer_id', $invoice?->customer_id ?? $prefillCustomerId) }}',
        customers: @js($customers->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()),
    }" class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="customer_id" :value="__('Customer')" />
            <select id="customer_id" name="customer_id" x-model="customerId" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <option value="">{{ __('Select a customer') }}</option>
                <template x-for="customer in customers" :key="customer.id">
                    <option :value="customer.id" x-text="customer.name"></option>
                </template>
            </select>
            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
            <x-quick-add-customer />
        </div>
        <div>
            <x-input-label for="due_date" :value="__('Due date')" />
            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', $invoice?->due_date?->toDateString() ?? now()->addDays(14)->toDateString())" />
            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
        </div>
    </div>

    <x-project-unit-select :projects="$projects" :selected-project-id="$invoice?->project_id ?? $prefillProjectId" :selected-unit-id="$invoice?->project_unit_id ?? $prefillUnitId" />

    <div class="flex items-start gap-2">
        <input type="hidden" name="counts_toward_property_price" value="0">
        <input type="checkbox" id="counts_toward_property_price" name="counts_toward_property_price" value="1"
            @checked(old('counts_toward_property_price', $invoice?->counts_toward_property_price ?? true))
            class="mt-1 rounded border-gray-300 dark:border-slate-600 text-accent-600 focus:ring-accent-500">
        <label for="counts_toward_property_price" class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Counts toward the property price (if a unit is selected above) — leave checked for a payment installment, uncheck for a separate charge like extra work the customer asked for. Unchecked invoices still show in this customer/property\'s records but won\'t reduce the outstanding balance.') }}
        </label>
    </div>
</div>

<x-line-items :products="$products" :items="$invoice?->items" />
<x-input-error :messages="$errors->get('items')" class="mt-2" />

<div>
    <x-input-label for="notes" :value="__('Notes')" />
    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $invoice?->notes) }}</textarea>
</div>
