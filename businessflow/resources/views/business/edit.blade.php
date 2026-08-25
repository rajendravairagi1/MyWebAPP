<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Business Settings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('This is what shows up on your Invoices, Quotations, and Statements — your logo, name, phone, email, address, and website.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <form method="POST" action="{{ route('business.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label :value="__('Logo')" />
                        <div class="mt-2 flex items-center gap-4">
                            @if ($business->logo_path)
                                <img src="{{ route('business.logo') }}?v={{ $business->updated_at->timestamp }}" alt="{{ $business->name }}" class="h-16 w-16 object-contain rounded-md border border-gray-200 dark:border-slate-700 bg-white">
                            @else
                                <div class="h-16 w-16 rounded-md border border-dashed border-gray-300 dark:border-slate-600 flex items-center justify-center text-xs text-gray-400">{{ __('No logo') }}</div>
                            @endif
                            <input type="file" name="logo" accept="image/*"
                                class="block text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ __('Compressed automatically. JPG, PNG, or WEBP.') }}</p>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Business Name')" />
                        <input type="text" id="name" name="name" value="{{ old('name', $business->name) }}" required
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $business->phone) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <input type="email" id="email" name="email" value="{{ old('email', $business->email) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Address')" />
                        <textarea id="address" name="address" rows="2"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('address', $business->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="website" :value="__('Website')" />
                            <input type="text" id="website" name="website" value="{{ old('website', $business->website) }}" placeholder="www.example.com"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            <x-input-error :messages="$errors->get('website')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="invoice_prefix" :value="__('Invoice Prefix')" />
                            <input type="text" id="invoice_prefix" name="invoice_prefix" value="{{ old('invoice_prefix', $business->invoice_prefix) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
