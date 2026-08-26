{{--
    Small "+ New Customer" popup usable on Quotation/Invoice forms so a
    customer can be created without leaving the page. Reads/writes
    `customerId` and `customers` from the parent form's x-data (same
    scope-chain pattern as <x-project-unit-select>), so the newly created
    customer is immediately selected and shows up in the dropdown.
--}}
<div x-data="{
        open: false,
        saving: false,
        error: '',
        name: '',
        phone: '',
        async submit() {
            this.error = '';
            if (! this.name.trim()) {
                this.error = 'Customer ka naam dalna zaroori hai.';
                return;
            }
            this.saving = true;
            try {
                const res = await fetch('{{ route('customers.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ name: this.name, phone: this.phone }),
                });
                if (! res.ok) {
                    const body = await res.json().catch(() => ({}));
                    this.error = body.message || 'Customer save nahi ho paya, dubara try karo.';
                    this.saving = false;
                    return;
                }
                const data = await res.json();
                this.customers.push({ id: data.id, name: data.name });
                this.customerId = String(data.id);
                this.open = false;
                this.name = '';
                this.phone = '';
            } catch (e) {
                this.error = 'Network error — dubara try karo.';
            }
            this.saving = false;
        },
    }">
    <button type="button" @click="open = true" class="mt-1 text-xs font-medium text-accent-600 hover:text-accent-800 underline">
        {{ __('+ New customer') }}
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="open = false">
        <div @click.outside="open = false" class="w-full max-w-sm bg-white dark:bg-slate-800 rounded-lg shadow-xl p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('New customer') }}</h3>

            <div class="space-y-3">
                <div>
                    <x-input-label :value="__('Name')" />
                    <input type="text" x-model="name" @keydown.enter.prevent="submit()" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500" autofocus>
                </div>
                <div>
                    <x-input-label :value="__('Phone (optional)')" />
                    <input type="text" x-model="phone" @keydown.enter.prevent="submit()" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <p x-show="error" x-text="error" class="text-xs text-red-600"></p>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" @click="open = false" class="px-3 py-1.5 text-sm rounded-md border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200">{{ __('Cancel') }}</button>
                <button type="button" @click="submit()" :disabled="saving" class="px-3 py-1.5 text-sm rounded-md bg-accent-600 text-white disabled:opacity-50">
                    <span x-show="!saving">{{ __('Save') }}</span>
                    <span x-show="saving">{{ __('Saving…') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
