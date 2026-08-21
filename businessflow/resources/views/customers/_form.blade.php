@php $customer = $customer ?? null; @endphp

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
