@php $p = $product ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Company *</label>
        <select name="customer_id" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
            <option value="">-- Select --</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $p?->customer_id) == $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
        @error('customer_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Product Code *</label>
        <input type="text" name="product_code" value="{{ old('product_code', $p?->product_code ?? $nextCode ?? '') }}" required
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm font-mono">
        @error('product_code') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Product Name *</label>
        <input type="text" name="name" value="{{ old('name', $p?->name) }}" required
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Unit *</label>
        <select name="unit" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
            @foreach (['Piece', 'Kg', 'Litre', 'Box'] as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $p?->unit) == $unit)>{{ $unit }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">HSN Code</label>
        <input type="text" name="hsn_code" value="{{ old('hsn_code', $p?->hsn_code) }}"
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Rate (₹) *</label>
        <input type="number" step="0.01" name="rate" value="{{ old('rate', $p?->rate ?? 0) }}" required
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
</div>
