<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Invoice') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('invoices.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="customer_id" :value="__('Customer')" />
                            <select id="customer_id" name="customer_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select a customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            @if ($customers->isEmpty())
                                <p class="mt-1 text-xs text-amber-600">{{ __('No customers yet — ') }}<a href="{{ route('customers.create') }}" class="underline">{{ __('add one first') }}</a>.</p>
                            @endif
                        </div>
                        <div>
                            <x-input-label for="due_date" :value="__('Due date')" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', now()->addDays(14)->toDateString())" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                    </div>

                    <x-line-items :products="$products" />
                    <x-input-error :messages="$errors->get('items')" class="mt-2" />

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('invoices.index') }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Save Invoice') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
