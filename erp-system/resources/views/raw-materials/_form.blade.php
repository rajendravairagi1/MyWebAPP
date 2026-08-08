@php $m = $material ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Material Name *</label>
        <input type="text" name="name" value="{{ old('name', $m?->name) }}" required
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Category *</label>
        <select name="category" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
            @foreach (['Paint', 'Thinner', 'Primer', 'Chemical', 'Packing Material', 'Other'] as $cat)
                <option value="{{ $cat }}" @selected(old('category', $m?->category) == $cat)>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Unit *</label>
        <select name="unit" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
            @foreach (['Litre', 'Kg', 'Piece', 'Box'] as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $m?->unit) == $unit)>{{ $unit }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Current Stock *</label>
        <input type="number" step="0.01" name="current_stock" value="{{ old('current_stock', $m?->current_stock ?? 0) }}" required
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Low Stock Alert Level *</label>
        <input type="number" step="0.01" name="low_stock_alert_at" value="{{ old('low_stock_alert_at', $m?->low_stock_alert_at ?? 0) }}" required
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
        <p class="text-xs text-[var(--muted)] mt-1">Stock isse neeche jaate hi "Low Stock" dikhega.</p>
    </div>
</div>
