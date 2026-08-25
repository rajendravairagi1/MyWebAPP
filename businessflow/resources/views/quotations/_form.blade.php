@php
    $quotation = $quotation ?? null;
    $prefillProjectId = $prefillProjectId ?? null;
    $prefillUnitId = $prefillUnitId ?? null;
@endphp

<div x-data="{
        customerId: '{{ old('customer_id', $quotation?->customer_id) }}',
        customers: @js($customers->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()),
    }" class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="customer_id" :value="__('Customer')" />
            <select id="customer_id" name="customer_id" x-model="customerId" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <option value="">{{ __('Select a customer') }}</option>
                <template x-for="customer in customers" :key="customer.id">
                    <option :value="customer.id" x-text="customer.name"></option>
                </template>
            </select>
            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
            <x-quick-add-customer />
        </div>
        <div>
            <x-input-label for="valid_until" :value="__('Valid until')" />
            <x-text-input id="valid_until" name="valid_until" type="date" class="mt-1 block w-full" :value="old('valid_until', $quotation?->valid_until?->toDateString() ?? now()->addDays(2)->toDateString())" />
            <x-input-error :messages="$errors->get('valid_until')" class="mt-2" />
        </div>
    </div>

    <x-project-unit-select :projects="$projects" :selected-project-id="$quotation?->project_id ?? $prefillProjectId" :selected-unit-id="$quotation?->project_unit_id ?? $prefillUnitId" />
</div>

<x-line-items :products="$products" :items="$quotation?->items" />
<x-input-error :messages="$errors->get('items')" class="mt-2" />

<div>
    <x-input-label for="notes" :value="__('Notes')" />
    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $quotation?->notes) }}</textarea>
</div>
<div>
    <x-input-label for="terms" :value="__('Terms')" />
    <textarea id="terms" name="terms" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('terms', $quotation?->terms) }}</textarea>
</div>
