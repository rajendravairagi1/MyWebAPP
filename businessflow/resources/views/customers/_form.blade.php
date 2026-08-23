@php $customer = $customer ?? null; @endphp

<div class="flex items-start gap-4">
    <div class="h-16 w-16 shrink-0 rounded-lg overflow-hidden bg-accent-100 dark:bg-slate-700 flex items-center justify-center">
        @if ($customer?->photo_path)
            <img src="{{ route('customers.photo', $customer) }}" alt="{{ $customer->name }}" class="h-full w-full object-cover">
        @else
            <svg class="h-8 w-8 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
        @endif
    </div>
    <div class="flex-1">
        <x-input-label for="photo" :value="__('Customer photo')" />
        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="aadhar" :value="__('Aadhar card (photo or PDF)')" />
    <input id="aadhar" name="aadhar" type="file" accept="image/*,.pdf" class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
    @if ($customer?->aadhar_path)
        <a href="{{ route('customers.aadhar', $customer) }}" target="_blank" rel="noopener" class="mt-1 inline-block text-xs text-accent-600 hover:underline">{{ __('View current Aadhar card') }}: {{ $customer->aadhar_name }}</a>
    @endif
    <x-input-error :messages="$errors->get('aadhar')" class="mt-2" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $customer?->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="company" :value="__('Company')" />
        <x-text-input id="company" name="company" type="text" class="mt-1 block w-full" :value="old('company', $customer?->company)" />
        <x-input-error :messages="$errors->get('company')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $customer?->phone)" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $customer?->email)" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="source" :value="__('Source')" />
        <x-text-input id="source" name="source" type="text" class="mt-1 block w-full" :value="old('source', $customer?->source)" placeholder="{{ __('e.g. referral, walk-in, website') }}" />
        <x-input-error :messages="$errors->get('source')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="address" :value="__('Address')" />
    <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('address', $customer?->address) }}</textarea>
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div>
    <x-input-label for="notes" :value="__('Notes')" />
    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $customer?->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
