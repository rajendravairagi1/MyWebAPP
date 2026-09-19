@props(['settings'])

@if ($settings->hasUpiId() || $settings->hasPaymentQr())
    <div class="max-w-xs mx-auto rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-accent-600 to-accent-500 px-4 py-2.5 flex items-center justify-center gap-2">
            <svg class="h-4 w-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h4.5v4.5h-4.5v-4.5Zm0 12h4.5v4.5h-4.5v-4.5Zm12-12h4.5v4.5h-4.5v-4.5ZM13.5 13.5h2.25v2.25H13.5v-2.25Zm0 4.5h2.25v2.25H13.5v-2.25Zm4.5-4.5h2.25v2.25H18v-2.25Zm0 4.5h2.25v2.25H18v-2.25Z" /></svg>
            <span class="text-white text-sm font-semibold tracking-wide">{{ __('Scan & Pay via UPI') }}</span>
        </div>

        <div class="p-4 text-center">
            <img src="{{ route('billing.payment-qr') }}" alt="{{ __('Scan to pay') }}" class="mx-auto h-44 w-44 rounded-lg border border-gray-100 dark:border-slate-600 bg-white p-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Works with any UPI app — GPay, PhonePe, Paytm & more') }}</p>

            @if ($settings->hasUpiId())
                <div class="flex items-center gap-2 my-3">
                    <div class="flex-1 h-px bg-gray-200 dark:bg-slate-700"></div>
                    <span class="text-[11px] uppercase tracking-wide text-gray-400">{{ __('or') }}</span>
                    <div class="flex-1 h-px bg-gray-200 dark:bg-slate-700"></div>
                </div>

                <a href="{{ $settings->upiPaymentLink() }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-accent-600 text-white text-sm font-semibold rounded-md hover:bg-accent-700">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75A2.25 2.25 0 0015.75 1.5H13.5m-3 0V3h3V1.5m-3 0h3m-6.75 18h9" /></svg>
                    {{ __('Pay via UPI App') }}
                </a>
                <p class="text-[11px] text-gray-400 mt-1.5">{{ __('Opens GPay / PhonePe / Paytm directly on your phone — no scanning needed.') }}</p>
            @endif
        </div>
    </div>
@endif
