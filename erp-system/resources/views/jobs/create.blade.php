<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Naya Job Receive Karo') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($customers->isEmpty() || $products->isEmpty())
                        <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded p-3">
                            Job banane ke liye pehle kam se kam ek Customer aur ek Product hona chahiye.
                            <a href="{{ route('customers.create') }}" class="underline font-semibold">Customer add karo</a> ·
                            <a href="{{ route('products.create') }}" class="underline font-semibold">Product add karo</a>
                        </p>
                    @else
                        <form method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data" x-data="{ rows: [{ product_id: '', quantity: 1 }] }">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                                    <select name="customer_id" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="">-- Select --</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Challan Number</label>
                                    <input type="text" name="challan_number" value="{{ old('challan_number') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Number</label>
                                    <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle/Material Photo</label>
                                    <input type="file" name="photo" accept="image/*" class="w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Date *</label>
                                    <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                    <p class="text-xs text-gray-400 mt-1">1 din pehle reminder ke liye dashboard pe dikhega.</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <div class="border-t pt-4 mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-sm font-semibold text-gray-700">Products (Pieces)</h3>
                                    <button type="button" @click="rows.push({ product_id: '', quantity: 1 })"
                                            class="text-xs text-indigo-600 hover:underline">+ Add Row</button>
                                </div>
                                @error('items') <p class="text-red-600 text-xs mb-2">{{ $message }}</p> @enderror

                                <template x-for="(row, index) in rows" :key="index">
                                    <div class="flex gap-3 mb-2 items-center">
                                        <select :name="'items['+index+'][product_id]'" x-model="row.product_id" required
                                                class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                            <option value="">-- Product --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->customer->name }})</option>
                                            @endforeach
                                        </select>
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model="row.quantity" min="1" required
                                               class="w-24 rounded-md border-gray-300 shadow-sm text-sm" placeholder="Qty">
                                        <button type="button" @click="rows.splice(index, 1)" x-show="rows.length > 1"
                                                class="text-red-500 text-xs">Remove</button>
                                    </div>
                                </template>
                            </div>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Job Receive Karo
                            </button>
                            <a href="{{ route('jobs.index') }}" class="ml-3 text-sm text-gray-500 hover:underline">Cancel</a>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
