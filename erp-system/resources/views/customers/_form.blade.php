@php $c = $customer ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Company Name *</label>
        <input type="text" name="name" value="{{ old('name', $c?->name) }}" required
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">GST Number</label>
        <input type="text" name="gst_number" value="{{ old('gst_number', $c?->gst_number) }}"
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Address</label>
        <textarea name="address" rows="2" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">{{ old('address', $c?->address) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $c?->contact_person) }}"
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Mobile</label>
        <input type="text" name="mobile" value="{{ old('mobile', $c?->mobile) }}"
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $c?->email) }}"
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Payment Terms</label>
        <input type="text" name="payment_terms" placeholder="e.g. 30 days" value="{{ old('payment_terms', $c?->payment_terms) }}"
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Opening Balance (₹)</label>
        <input type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', $c?->opening_balance ?? 0) }}"
               class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">{{ old('notes', $c?->notes) }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-[var(--muted)] mb-1">Documents Upload (GST cert, agreement, etc.)</label>
        <input type="file" name="documents[]" multiple class="w-full text-sm">
    </div>
</div>
