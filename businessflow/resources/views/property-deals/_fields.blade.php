@php $deal ??= null; @endphp

<div>
    <x-input-label :value="__('Property')" />
    <x-text-input name="property_title" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. 2BHK Flat, Sector 10') }}" value="{{ old('property_title', $deal->property_title ?? '') }}" required autofocus />
</div>
<div>
    <x-input-label :value="__('Address (optional)')" />
    <x-text-input name="address" type="text" class="mt-1 block w-full" value="{{ old('address', $deal->address ?? '') }}" />
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('Seller Name')" />
        <x-text-input name="seller_name" type="text" class="mt-1 block w-full" value="{{ old('seller_name', $deal->seller_name ?? '') }}" />
    </div>
    <div>
        <x-input-label :value="__('Seller Phone')" />
        <x-text-input name="seller_phone" type="text" class="mt-1 block w-full" value="{{ old('seller_phone', $deal->seller_phone ?? '') }}" />
    </div>
</div>

<div>
    <x-input-label :value="__('Purchase Price (what the seller wants)')" />
    <input type="number" step="0.01" min="0.01" name="purchase_price" required placeholder="{{ \App\Support\Tenant::currencySymbol() }}" value="{{ old('purchase_price', $deal->purchase_price ?? '') }}"
        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
</div>

<div class="border-t border-gray-100 dark:border-slate-700 pt-4">
    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Buyer (once sold)') }}</p>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label :value="__('Buyer Name')" />
            <x-text-input name="buyer_name" type="text" class="mt-1 block w-full" value="{{ old('buyer_name', $deal->buyer_name ?? '') }}" />
        </div>
        <div>
            <x-input-label :value="__('Buyer Phone')" />
            <x-text-input name="buyer_phone" type="text" class="mt-1 block w-full" value="{{ old('buyer_phone', $deal->buyer_phone ?? '') }}" />
        </div>
    </div>
    <div class="mt-4">
        <x-input-label :value="__('Sale Price (what the buyer pays)')" />
        <input type="number" step="0.01" min="0.01" name="sale_price" placeholder="{{ \App\Support\Tenant::currencySymbol() }}" value="{{ old('sale_price', $deal->sale_price ?? '') }}"
            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
</div>

@if ($deal)
    <div>
        <x-input-label :value="__('Status')" />
        <select name="status" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="open" @selected($deal->status !== 'cancelled')>{{ __('Open / Sold (auto)') }}</option>
            <option value="cancelled" @selected($deal->status === 'cancelled')>{{ __('Cancelled') }}</option>
        </select>
        <p class="text-xs text-gray-400 mt-1">{{ __('Becomes "Sold" automatically as soon as a Sale Price is filled in above — remove the sale price to move it back to Open.') }}</p>
    </div>
@endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('Deal Date')" />
        <input type="date" name="deal_date" value="{{ old('deal_date', $deal?->deal_date?->toDateString() ?? now()->toDateString()) }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
    @if ($deal)
        <div>
            <x-input-label :value="__('Sold Date')" />
            <input type="date" name="sold_date" value="{{ old('sold_date', $deal->sold_date?->toDateString()) }}" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
        </div>
    @endif
</div>

<div>
    <x-input-label :value="__('Notes (optional)')" />
    <textarea name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $deal->notes ?? '') }}</textarea>
</div>
