@php $invoice = $invoice ?? null; @endphp

<div x-data="{ customerId: '{{ old('customer_id', $invoice?->customer_id) }}' }" class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="customer_id" :value="__('Customer')" />
            <select id="customer_id" name="customer_id" x-model="customerId" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <option value="">{{ __('Select a customer') }}</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
            @if ($customers->isEmpty())
                <p class="mt-1 text-xs text-amber-600">{{ __('No customers yet — ') }}<a href="{{ route('customers.create') }}" class="underline">{{ __('add one first') }}</a>.</p>
            @endif
        </div>
        <div>
            <x-input-label for="due_date" :value="__('Due date')" />
            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', $invoice?->due_date?->toDateString() ?? now()->addDays(14)->toDateString())" />
            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
        </div>
    </div>

    <x-project-unit-select :projects="$projects" :selected-project-id="$invoice?->project_id" :selected-unit-id="$invoice?->project_unit_id" />
</div>

<x-line-items :products="$products" :items="$invoice?->items" />
<x-input-error :messages="$errors->get('items')" class="mt-2" />

<div>
    <x-input-label for="notes" :value="__('Notes')" />
    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $invoice?->notes) }}</textarea>
</div>
