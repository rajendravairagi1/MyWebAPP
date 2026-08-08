<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Nayi Purchase Entry') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    @if ($suppliers->isEmpty() || $materials->isEmpty())
                        <div class="mb-4 px-4 py-2 rounded bg-amber-50 text-amber-700 text-sm">
                            Pehle {{ $suppliers->isEmpty() ? 'ek supplier' : '' }}{{ $suppliers->isEmpty() && $materials->isEmpty() ? ' aur ' : '' }}{{ $materials->isEmpty() ? 'ek raw material' : '' }} add karo.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('purchases.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-[var(--muted)] mb-1">Supplier *</label>
                                <select name="supplier_id" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                    <option value="">-- Select --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--muted)] mb-1">Raw Material *</label>
                                <select name="raw_material_id" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                    <option value="">-- Select --</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id }}" @selected(old('raw_material_id') == $material->id)>{{ $material->name }} ({{ $material->unit }})</option>
                                    @endforeach
                                </select>
                                @error('raw_material_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--muted)] mb-1">Bill Number</label>
                                <input type="text" name="bill_number" value="{{ old('bill_number') }}" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--muted)] mb-1">Purchase Date *</label>
                                <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--muted)] mb-1">Quantity *</label>
                                <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                @error('quantity') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--muted)] mb-1">Rate per unit (₹) *</label>
                                <input type="number" step="0.01" name="rate" value="{{ old('rate') }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                @error('rate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-[var(--muted)] mb-1">Notes</label>
                                <textarea name="notes" rows="2" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Save Purchase (Stock update ho jayega)
                        </button>
                        <a href="{{ route('purchases.index') }}" class="ml-3 text-sm text-[var(--muted)] hover:underline">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
