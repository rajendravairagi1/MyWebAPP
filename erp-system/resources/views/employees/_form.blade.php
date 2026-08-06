@php $e = $employee ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code *</label>
        <input type="text" name="employee_code" value="{{ old('employee_code', $e?->employee_code ?? $nextCode ?? '') }}" required
               class="w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">
        @error('employee_code') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
        <input type="text" name="name" value="{{ old('name', $e?->name) }}" required
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
        <input type="text" name="mobile" value="{{ old('mobile', $e?->mobile) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Joining Date</label>
        <input type="date" name="joining_date" value="{{ old('joining_date', $e?->joining_date?->format('Y-m-d')) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Aadhaar Number</label>
        <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number', $e?->aadhaar_number) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">PAN Number</label>
        <input type="text" name="pan_number" value="{{ old('pan_number', $e?->pan_number) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
        <input type="text" name="department" value="{{ old('department', $e?->department) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
        <input type="text" name="designation" value="{{ old('designation', $e?->designation) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Per-Day Salary (₹) *</label>
        <input type="number" step="0.01" name="per_day_salary" value="{{ old('per_day_salary', $e?->per_day_salary ?? 0) }}" required
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
            <option value="active" @selected(old('status', $e?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $e?->status) === 'inactive')>Inactive</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number</label>
        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $e?->bank_account_number) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bank IFSC</label>
        <input type="text" name="bank_ifsc" value="{{ old('bank_ifsc', $e?->bank_ifsc) }}"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
    </div>
</div>
