@php $product = $product ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product?->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="type" :value="__('Type')" />
        <select id="type" name="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="product" @selected(old('type', $product?->type) === 'product')>{{ __('Product') }}</option>
            <option value="service" @selected(old('type', $product?->type) === 'service')>{{ __('Service') }}</option>
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="sku" :value="__('SKU')" />
        <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku', $product?->sku)" />
        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="unit" :value="__('Unit')" />
        <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full" :value="old('unit', $product?->unit)" placeholder="{{ __('pcs, hr, kg...') }}" />
        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="price" :value="__('Price')" />
        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $product?->price)" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="tax_rate" :value="__('Tax rate (%)')" />
        <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('tax_rate', $product?->tax_rate ?? 0)" />
        <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="stock_qty" :value="__('Stock quantity (optional)')" />
        <x-text-input id="stock_qty" name="stock_qty" type="number" min="0" class="mt-1 block w-full" :value="old('stock_qty', $product?->stock_qty)" />
        <x-input-error :messages="$errors->get('stock_qty')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="low_stock_threshold" :value="__('Low stock alert below')" />
        <x-text-input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0" class="mt-1 block w-full" :value="old('low_stock_threshold', $product?->low_stock_threshold)" />
        <x-input-error :messages="$errors->get('low_stock_threshold')" class="mt-2" />
    </div>
</div>
