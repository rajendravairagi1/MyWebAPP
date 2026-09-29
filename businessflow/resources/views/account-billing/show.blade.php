<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Account & Billing') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $daysRemaining = $expiresOn ? now()->startOfDay()->diffInDays($expiresOn->copy()->startOfDay(), false) : null;
            @endphp

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-400">{{ __('Account') }}</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $entityName }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $planLabel }} {{ __('plan') }}</div>
                </div>
                <div class="text-right">
                    @if ($expiresOn)
                        <span @class([
                            'text-xs px-2 py-1 rounded font-medium',
                            'bg-red-100 text-red-700' => $isExpired,
                            'bg-amber-100 text-amber-700' => ! $isExpired && $daysRemaining !== null && $daysRemaining <= 7,
                            'bg-green-100 text-green-700' => ! $isExpired && $daysRemaining !== null && $daysRemaining > 7,
                        ])>
                            {{ $isExpired ? __('Expired') : __('Valid till') }} {{ $expiresOn->format('d M Y') }}
                        </span>
                        @if (! $isExpired && $daysRemaining !== null)
                            <div class="text-xs text-gray-400 mt-1">{{ trans_choice(':count day left|:count days left', $daysRemaining, ['count' => $daysRemaining]) }}</div>
                        @endif
                    @else
                        <span class="text-xs text-gray-400">{{ __('No expiry set') }}</span>
                    @endif

                    <div class="mt-2">
                        <a href="{{ route('billing.show') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-accent-600 text-white text-xs font-semibold rounded-md hover:bg-accent-700">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V6a2.25 2.25 0 002.25 2.25h2.25M3 8.25V19.5a2.25 2.25 0 002.25 2.25h13.5a2.25 2.25 0 002.25-2.25V8.25m-18 0V6a2.25 2.25 0 012.25-2.25h9.879a1.5 1.5 0 011.06.44l3.622 3.621a1.5 1.5 0 01.44 1.06v2.129M3 8.25h18" /></svg>
                            {{ __('Renew Plan') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">{{ __('Subscription History') }}</h3>
                <p class="text-xs text-gray-400 mb-4">{{ __('Every renewal recorded on this account — manual (UPI) and through Google Play.') }}</p>

                @if ($renewals->isEmpty())
                    <p class="text-sm text-gray-400">{{ __('No renewals recorded yet.') }}</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($renewals as $renewal)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm text-gray-800 dark:text-gray-100">
                                        @if ($renewal->previous_expires_at)
                                            {{ __('Extended to') }} <span class="font-medium">{{ $renewal->new_expires_at?->format('d M Y') ?? '—' }}</span>
                                        @else
                                            {{ __('Started, valid till') }} <span class="font-medium">{{ $renewal->new_expires_at?->format('d M Y') ?? '—' }}</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $renewal->created_at->format('d M Y, h:i A') }}
                                        @if ($renewal->note)
                                            · {{ $renewal->note }}
                                        @endif
                                    </div>
                                </div>
                                <span @class([
                                    'text-xs px-2 py-1 rounded font-medium shrink-0',
                                    'bg-blue-100 text-blue-700' => $renewal->source === 'google_play',
                                    'bg-gray-100 text-gray-600' => $renewal->source === 'admin_manual',
                                ])>
                                    {{ \App\Models\SubscriptionRenewal::SOURCE_LABELS[$renewal->source] ?? $renewal->source }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
