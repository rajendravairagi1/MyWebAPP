<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Set up your business') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-6">
                    {{ __('A couple of details and your BusinessFlow dashboard will be ready.') }}
                </p>

                <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Business name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="business_type" :value="__('Business type')" />
                        <select id="business_type" name="business_type" required
                            class="mt-1 block w-full border-gray-300 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm">
                            <option value="">{{ __('Select one') }}</option>
                            @foreach ($businessTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('business_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="country" :value="__('Country code')" />
                            <x-text-input id="country" name="country" type="text" maxlength="2" placeholder="IN"
                                class="mt-1 block w-full uppercase" :value="old('country')" required />
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="currency" :value="__('Currency')" />
                            <select id="currency" name="currency" required
                                class="mt-1 block w-full border-gray-300 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm">
                                @foreach ($currencies as $value => $label)
                                    <option value="{{ $value }}" @selected(old('currency') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="timezone" :value="__('Timezone')" />
                            <select id="timezone" name="timezone" required
                                class="mt-1 block w-full border-gray-300 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm">
                                @foreach (\DateTimeZone::listIdentifiers() as $tz)
                                    <option value="{{ $tz }}" @selected(old('timezone') === $tz)>{{ $tz }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Continue to dashboard') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
