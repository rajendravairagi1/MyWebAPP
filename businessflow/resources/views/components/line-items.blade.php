@props(['products'])

<div x-data="lineItemsForm(@js($products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float) $p->price, 'tax_rate' => (float) $p->tax_rate])))">
    <div class="overflow-x-auto border border-gray-200 rounded-md">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-3 py-2 text-left w-1/3">{{ __('Item') }}</th>
                    <th class="px-3 py-2 text-right w-20">{{ __('Qty') }}</th>
                    <th class="px-3 py-2 text-right w-28">{{ __('Unit price') }}</th>
                    <th class="px-3 py-2 text-right w-24">{{ __('Discount') }}</th>
                    <th class="px-3 py-2 text-right w-20">{{ __('Tax %') }}</th>
                    <th class="px-3 py-2 text-right w-28">{{ __('Line total') }}</th>
                    <th class="px-3 py-2 w-10"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in rows" :key="index">
                    <tr class="border-t border-gray-100 dark:border-slate-700">
                        <td class="px-3 py-2">
                            <select :name="`items[${index}][product_id]`" x-model="row.product_id" @change="selectProduct(index)"
                                class="block w-full text-sm border-gray-300 rounded-md mb-1">
                                <option value="">{{ __('Custom item — type your own') }}</option>
                                <optgroup label="{{ __('Common Real Estate Items') }}">
                                    <template x-for="item in commonItems" :key="item.id">
                                        <option :value="item.id" x-text="item.name"></option>
                                    </template>
                                </optgroup>
                                @if ($products->isNotEmpty())
                                    <optgroup label="{{ __('My Products') }}">
                                        <template x-for="product in products" :key="product.id">
                                            <option :value="product.id" x-text="product.name"></option>
                                        </template>
                                    </optgroup>
                                @endif
                            </select>
                            <input type="text" :name="`items[${index}][description]`" x-model="row.description" required
                                placeholder="{{ __('Item / product name — e.g. Booking Amount, GST, Registration Charges') }}" class="block w-full text-sm border-gray-300 rounded-md">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="0.01" min="0.01" :name="`items[${index}][quantity]`" x-model.number="row.quantity"
                                class="w-full text-sm text-right border-gray-300 rounded-md">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="0.01" min="0" :name="`items[${index}][unit_price]`" x-model.number="row.unit_price"
                                class="w-full text-sm text-right border-gray-300 rounded-md">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="0.01" min="0" :name="`items[${index}][discount]`" x-model.number="row.discount"
                                class="w-full text-sm text-right border-gray-300 rounded-md">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="0.01" min="0" max="100" :name="`items[${index}][tax_rate]`" x-model.number="row.tax_rate"
                                class="w-full text-sm text-right border-gray-300 rounded-md">
                        </td>
                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300" x-text="lineTotal(row).toFixed(2)"></td>
                        <td class="px-3 py-2 text-right">
                            <button type="button" @click="removeRow(index)" x-show="rows.length > 1" class="text-gray-400 hover:text-red-600">&times;</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <button type="button" @click="addRow()" class="mt-3 text-sm text-accent-600 hover:underline">{{ __('+ Add line') }}</button>

    <div class="mt-4 flex justify-end">
        <table class="text-sm w-64">
            <tr>
                <td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Subtotal') }}</td>
                <td class="py-1 text-right text-gray-900 dark:text-gray-100" x-text="subtotal().toFixed(2)"></td>
            </tr>
            <tr>
                <td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Discount') }}</td>
                <td class="py-1 text-right text-gray-900 dark:text-gray-100" x-text="discountTotal().toFixed(2)"></td>
            </tr>
            <tr>
                <td class="py-1 text-gray-500 dark:text-gray-400">{{ __('Tax') }}</td>
                <td class="py-1 text-right text-gray-900 dark:text-gray-100" x-text="taxTotal().toFixed(2)"></td>
            </tr>
            <tr class="border-t border-gray-200 font-semibold">
                <td class="py-1 text-gray-700 dark:text-gray-300">{{ __('Total') }}</td>
                <td class="py-1 text-right text-gray-900 dark:text-gray-100" x-text="grandTotal().toFixed(2)"></td>
            </tr>
        </table>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function lineItemsForm(products) {
                return {
                    products,
                    commonItems: [
                        { id: 're-booking', name: 'Booking Amount / Token Money' },
                        { id: 're-down-payment', name: 'Down Payment' },
                        { id: 're-construction-installment', name: 'Construction Installment' },
                        { id: 're-registration', name: 'Registration Charges' },
                        { id: 're-stamp-duty', name: 'Stamp Duty' },
                        { id: 're-gst', name: 'GST' },
                        { id: 're-maintenance', name: 'Maintenance Charges' },
                        { id: 're-parking', name: 'Parking Charges' },
                        { id: 're-legal', name: 'Legal / Documentation Charges' },
                        { id: 're-society', name: 'Society / Club Membership Fee' },
                        { id: 're-interest', name: 'Interest / Late Payment Charges' },
                        { id: 're-final', name: 'Full & Final Payment' },
                    ],
                    rows: [{ product_id: '', description: '', quantity: 1, unit_price: 0, discount: 0, tax_rate: 0 }],
                    addRow() {
                        this.rows.push({ product_id: '', description: '', quantity: 1, unit_price: 0, discount: 0, tax_rate: 0 });
                    },
                    removeRow(index) {
                        this.rows.splice(index, 1);
                    },
                    selectProduct(index) {
                        const row = this.rows[index];

                        const preset = this.commonItems.find(item => item.id === row.product_id);
                        if (preset) {
                            row.description = preset.name;
                            row.product_id = '';
                            return;
                        }

                        const product = this.products.find(p => String(p.id) === String(row.product_id));
                        if (product) {
                            row.description = product.name;
                            row.unit_price = product.price;
                            row.tax_rate = product.tax_rate;
                        }
                    },
                    lineTotal(row) {
                        const base = (Number(row.quantity) || 0) * (Number(row.unit_price) || 0) - (Number(row.discount) || 0);
                        return base + base * ((Number(row.tax_rate) || 0) / 100);
                    },
                    subtotal() {
                        return this.rows.reduce((sum, row) => sum + (Number(row.quantity) || 0) * (Number(row.unit_price) || 0), 0);
                    },
                    discountTotal() {
                        return this.rows.reduce((sum, row) => sum + (Number(row.discount) || 0), 0);
                    },
                    taxTotal() {
                        return this.rows.reduce((sum, row) => {
                            const base = (Number(row.quantity) || 0) * (Number(row.unit_price) || 0) - (Number(row.discount) || 0);
                            return sum + base * ((Number(row.tax_rate) || 0) / 100);
                        }, 0);
                    },
                    grandTotal() {
                        return this.subtotal() - this.discountTotal() + this.taxTotal();
                    },
                };
            }
        </script>
    @endpush
@endonce
