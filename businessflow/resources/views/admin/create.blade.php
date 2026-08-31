<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Add Customer Account') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6" x-data="{ plan: 'solo' }">
                <form method="POST" action="{{ route('admin.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label :value="__('Plan')" />
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            <label class="border rounded-lg p-3 text-sm cursor-pointer" :class="plan === 'solo' ? 'border-accent-500 ring-1 ring-accent-500' : 'border-gray-300 dark:border-slate-600'">
                                <input type="radio" name="plan" value="solo" x-model="plan" class="sr-only">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Solo') }}</div>
                                <div class="text-xs text-gray-400">₹1000/mo</div>
                            </label>
                            <label class="border rounded-lg p-3 text-sm cursor-pointer" :class="plan === 'team' ? 'border-accent-500 ring-1 ring-accent-500' : 'border-gray-300 dark:border-slate-600'">
                                <input type="radio" name="plan" value="team" x-model="plan" class="sr-only">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Builder + Team') }}</div>
                                <div class="text-xs text-gray-400">₹1500/mo</div>
                            </label>
                            <label class="border rounded-lg p-3 text-sm cursor-pointer" :class="plan === 'company' ? 'border-accent-500 ring-1 ring-accent-500' : 'border-gray-300 dark:border-slate-600'">
                                <input type="radio" name="plan" value="company" x-model="plan" class="sr-only">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Company') }}</div>
                                <div class="text-xs text-gray-400">₹3000/mo</div>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Owner login') }}</p>
                        <div class="space-y-3">
                            <div>
                                <x-input-label for="owner_name" :value="__('Name')" />
                                <x-text-input id="owner_name" name="owner_name" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="owner_email" :value="__('Email')" />
                                <x-text-input id="owner_email" name="owner_email" type="email" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('owner_email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="owner_password" :value="__('Password')" />
                                <x-text-input id="owner_password" name="owner_password" type="text" class="mt-1 block w-full" required />
                                <p class="text-xs text-gray-400 mt-1">{{ __('Share this with them — they log in with this email and password.') }}</p>
                                <x-input-error :messages="$errors->get('owner_password')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                        <div>
                            <x-input-label for="account_name" :value="__('Business / Company name')" />
                            <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. Shivani Enterprises') }}" required />
                            <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                        </div>

                        <div x-show="plan !== 'company'" x-cloak class="mt-4 space-y-4">
                            <div>
                                <x-input-label for="business_type" :value="__('Business type')" />
                                <select id="business_type" name="business_type" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                    <option value="">{{ __('Select one') }}</option>
                                    @foreach ($businessTypes as $value => $label)
                                        <option value="{{ $value }}" @selected($value === 'real_estate')>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="country" :value="__('Country code')" />
                                    <x-text-input id="country" name="country" type="text" maxlength="2" value="IN" class="mt-1 block w-full uppercase" />
                                </div>
                                <div>
                                    <x-input-label for="currency" :value="__('Currency')" />
                                    <select id="currency" name="currency" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                        @foreach ($currencies as $value => $label)
                                            <option value="{{ $value }}" @selected($value === 'INR')>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="timezone" :value="__('Timezone')" />
                                    <select id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                        @foreach (\DateTimeZone::listIdentifiers() as $tz)
                                            <option value="{{ $tz }}" @selected($tz === 'Asia/Kolkata')>{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" name="is_demo" value="1" class="rounded border-gray-300 text-accent-600 focus:ring-accent-500">
                                {{ __('This is the public demo account (shown from the "See Demo" button on the homepage)') }}
                            </label>
                        </div>
                        <p x-show="plan === 'company'" x-cloak class="mt-2 text-xs text-gray-400">{{ __('They\'ll log in straight to their Company Dashboard and can add branches and builders themselves.') }}</p>
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                        <x-input-label for="subscription_expires_at" :value="__('Valid till (optional)')" />
                        <x-text-input id="subscription_expires_at" name="subscription_expires_at" type="date" class="mt-1 block w-full sm:w-56" />
                        <p class="text-xs text-gray-400 mt-1">{{ __('Leave blank for no expiry. Access pauses automatically the day after this date — you can change it anytime from the account list.') }}</p>
                        <x-input-error :messages="$errors->get('subscription_expires_at')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('admin.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Create Account') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
