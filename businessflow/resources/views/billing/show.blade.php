<x-guest-layout>
    <div class="text-center space-y-4">
        <div class="mx-auto h-12 w-12 rounded-full bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
            <svg class="h-6 w-6 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" /></svg>
        </div>

        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Renew Your Plan') }}</h2>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            @if ($business)
                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $business->name }}</span>
                @if ($expiresOn && $expiresOn->isPast())
                    — {{ __('was valid through') }} {{ $expiresOn->format('d M Y') }}
                @elseif ($expiresOn)
                    — {{ __('valid till') }} {{ $expiresOn->format('d M Y') }}
                @endif
                ({{ ucfirst($business->plan) }} {{ __('plan') }})
            @else
                {{ __('Renew your subscription to keep full access.') }}
            @endif
        </p>

        @if ($settings->hasUpiId() || $settings->hasPaymentQr())
            <div class="pt-1">
                <img src="{{ route('billing.payment-qr') }}" alt="{{ __('Scan to pay') }}" class="mx-auto h-52 w-52 rounded-lg border border-gray-200 dark:border-slate-700 bg-white p-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                    {{ __('Scan and pay with any UPI app for your plan amount.') }}
                </p>
            </div>

            @if ($settings->hasUpiId())
                <div class="pt-1">
                    <a href="{{ $settings->upiPaymentLink() }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-accent-200 dark:border-accent-800 text-accent-700 dark:text-accent-400 text-sm font-semibold rounded-md hover:bg-accent-50 dark:hover:bg-accent-900/20">
                        {{ __('Pay via UPI App') }}
                    </a>
                    <p class="text-xs text-gray-400 mt-1">{{ __('On a phone, this opens GPay/PhonePe/Paytm directly — no scanning needed.') }}</p>
                </div>
            @endif
        @endif

        <p class="text-sm text-gray-600 dark:text-gray-400">
            @if ($settings->support_whatsapp)
                {{ __('After paying, send us the payment screenshot on WhatsApp and we\'ll update your validity — usually within a few hours.') }}
            @else
                {{ __('After paying, please contact us and we\'ll update your validity — usually within a few hours.') }}
            @endif
        </p>

        <div class="flex flex-col gap-2 pt-2">
            @if ($settings->support_whatsapp)
                <a href="https://wa.me/{{ $settings->support_whatsapp }}?text={{ urlencode(__('Hi, I\'ve just paid to renew my Pro Builder CRM plan for :name — sharing the payment screenshot.', ['name' => $business?->name ?? ''])) }}"
                   target="_blank" rel="noopener"
                   class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                    {{ __('Message Us on WhatsApp') }}
                </a>
            @endif

            @if (\App\Support\Tenant::check())
                <a href="{{ route('dashboard') }}" class="text-sm text-accent-600 hover:underline">{{ __('Back to Dashboard') }}</a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-gray-400 hover:underline">{{ __('Log out') }}</button>
            </form>
        </div>
    </div>
</x-guest-layout>
