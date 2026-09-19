<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Set up your business') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('A couple of details and your :name dashboard will be ready.', ['name' => config('app.name', 'Pro Builder CRM')]) }}
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
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm">
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
                                class="mt-1 block w-full uppercase" :value="old('country', 'IN')" required />
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="currency" :value="__('Currency')" />
                            <select id="currency" name="currency" required
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm">
                                @foreach ($currencies as $value => $label)
                                    <option value="{{ $value }}" @selected(old('currency', 'INR') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="timezone" :value="__('Timezone')" />
                            <select id="timezone" name="timezone" required
                                class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm">
                                @foreach (\DateTimeZone::listIdentifiers() as $tz)
                                    <option value="{{ $tz }}" @selected(old('timezone', 'Asia/Kolkata') === $tz)>{{ $tz }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Continue to dashboard') }}</x-primary-button>
                    </div>
                </form>

                @if ($settings->hasUpiId() || $settings->hasPaymentQr())
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700 text-center">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('Want to pay for your plan now?') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Totally optional — you can start using :name right away either way.', ['name' => config('app.name', 'Pro Builder CRM')]) }}</p>

                        <img src="{{ route('billing.payment-qr') }}" alt="{{ __('Scan to pay') }}" class="mx-auto mt-4 h-40 w-40 rounded-lg border border-gray-200 dark:border-slate-700 bg-white p-2">

                        @if ($settings->hasUpiId())
                            <a href="{{ $settings->upiPaymentLink() }}"
                               class="mt-3 w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-accent-200 dark:border-accent-800 text-accent-700 dark:text-accent-400 text-sm font-semibold rounded-md hover:bg-accent-50 dark:hover:bg-accent-900/20">
                                {{ __('Pay via UPI App') }}
                            </a>
                            <p class="text-xs text-gray-400 mt-1">{{ __('On a phone, this opens GPay/PhonePe/Paytm directly — no scanning needed.') }}</p>
                        @endif

                        @if ($settings->support_whatsapp)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                                {{ __('After paying, message us on WhatsApp with the screenshot and we\'ll confirm.') }}
                                <a href="https://wa.me/{{ $settings->support_whatsapp }}" target="_blank" rel="noopener" class="text-accent-600 hover:underline">{{ __('Message Us') }}</a>
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
