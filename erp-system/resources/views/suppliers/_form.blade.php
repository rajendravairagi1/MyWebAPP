@php $s = $supplier ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name *</label>
        <input type="text" name="name" value="{{ old('name', $s?->name) }}" required
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $s?->phone) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">GST Number</label>
        <input type="text" name="gst_number" value="{{ old('gst_number', $s?->gst_number) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
        <textarea name="address" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('address', $s?->address) }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('notes', $s?->notes) }}</textarea>
    </div>
</div>
