<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Business Settings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            @php
                $planLabels = ['solo' => __('Solo'), 'team' => __('Team'), 'company' => __('Company')];
                $planExpiresAt = $business->effectiveExpiresAt();
                $planExpired = $business->isSubscriptionExpired();
                $planDaysRemaining = $planExpiresAt ? now()->startOfDay()->diffInDays($planExpiresAt->copy()->startOfDay(), false) : null;
            @endphp
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-400">{{ __('Your Plan') }}</div>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $planLabels[$business->effectivePlan()] ?? ucfirst($business->effectivePlan()) }}</span>
                        @if ($business->branch_id)
                            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">{{ __('via your Company') }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    @if ($planExpiresAt)
                        <span @class([
                            'text-xs px-2 py-1 rounded font-medium',
                            'bg-red-100 text-red-700' => $planExpired,
                            'bg-amber-100 text-amber-700' => ! $planExpired && $planDaysRemaining !== null && $planDaysRemaining <= 7,
                            'bg-green-100 text-green-700' => ! $planExpired && $planDaysRemaining !== null && $planDaysRemaining > 7,
                        ])>
                            {{ $planExpired ? __('Expired') : __('Valid till') }} {{ $planExpiresAt->format('d M Y') }}
                        </span>
                        @if (! $planExpired && $planDaysRemaining !== null)
                            <div class="text-xs text-gray-400 mt-1">{{ trans_choice(':count day left|:count days left', $planDaysRemaining, ['count' => $planDaysRemaining]) }}</div>
                        @endif
                    @else
                        <span class="text-xs text-gray-400">{{ __('No expiry set') }}</span>
                    @endif
                </div>
            </div>

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
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $business->phone) }}"
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

                    <div>
                        <x-input-label for="currency" :value="__('Currency')" />
                        <select id="currency" name="currency" required
                            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm">
                            @foreach ($currencies as $value => $label)
                                <option value="{{ $value }}" @selected(old('currency', $business->currency) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">{{ __('Changes the symbol shown on every amount, invoice, and report in this account — it doesn\'t convert existing numbers.') }}</p>
                        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-700 pt-5 space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ __('Smart Features') }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('Turn any of these off if your team doesn\'t want them — they can always be switched back on later.') }}</p>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm text-gray-700 dark:text-gray-300">{{ __('Smart Alerts on Dashboard') }}</div>
                                <div class="text-xs text-gray-400">{{ __('The "Needs your attention" card — cold leads and stalled bookings.') }}</div>
                            </div>
                            <x-toggle-switch name="smart_alerts_enabled" :checked="old('smart_alerts_enabled', $business->smart_alerts_enabled)" />
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm text-gray-700 dark:text-gray-300">{{ __('Payment Reminders') }}</div>
                                <div class="text-xs text-gray-400">{{ __('The Payment Reminders page and its one-click WhatsApp button.') }}</div>
                            </div>
                            <x-toggle-switch name="payment_reminders_enabled" :checked="old('payment_reminders_enabled', $business->payment_reminders_enabled)" />
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm text-gray-700 dark:text-gray-300">{{ __('Voice-to-Text Notes') }}</div>
                                <div class="text-xs text-gray-400">{{ __('The 🎤 mic button on Customer notes and Follow-up notes.') }}</div>
                            </div>
                            <x-toggle-switch name="voice_notes_enabled" :checked="old('voice_notes_enabled', $business->voice_notes_enabled)" />
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
