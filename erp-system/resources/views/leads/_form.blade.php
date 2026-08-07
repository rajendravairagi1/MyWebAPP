@php $l = $lead ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
        <input type="text" name="name" value="{{ old('name', $l?->name) }}" required
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
        <input type="text" name="company_name" value="{{ old('company_name', $l?->company_name) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
        <input type="text" name="mobile" value="{{ old('mobile', $l?->mobile) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $l?->email) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
        <input type="text" name="source" placeholder="e.g. Referral, Website" value="{{ old('source', $l?->source) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
        <select name="status" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
            @foreach (['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'converted' => 'Converted', 'lost' => 'Lost'] as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $l?->status ?? 'new') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
        <select name="assigned_to" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
            <option value="">-- Unassigned --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('assigned_to', $l?->assigned_to) == $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('notes', $l?->notes) }}</textarea>
    </div>
</div>
