<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Leads') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-lead')" class="inline-flex items-center px-3 py-1.5 bg-accent-600 text-white text-xs font-medium rounded-md hover:bg-accent-700">{{ __('+ Add Lead') }}</button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            {{-- QR + link panel — hand this to a walk-in customer to fill their own details, or send the link directly on WhatsApp. --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5" x-data="{ copied: false }">
                <div class="flex flex-col sm:flex-row items-center gap-5">
                    @if ($qrDataUri)
                        <div class="shrink-0 text-center space-y-1.5">
                            <img src="{{ $qrDataUri }}" alt="{{ __('Lead form QR code') }}" class="h-40 w-40 rounded-lg border border-gray-200 dark:border-slate-700">
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 break-all max-w-[10rem]">{{ $publicUrl }}</div>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1 space-y-3 text-center sm:text-left">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Your lead-capture QR code') }}</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Show this to a customer, or send them the link — they fill in their own details, and it lands here once you approve it.') }}</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <button type="button" x-on:click="shareImageDataUri('{{ $qrDataUri }}', 'lead-qr.png', $el)" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">{{ __('Share QR (WhatsApp etc.)') }}</button>
                            <a href="{{ $qrDataUri }}" download="lead-qr.png" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Download QR') }}</a>
                            <button type="button" x-on:click="navigator.clipboard.writeText('{{ $publicUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">
                                <span x-show="!copied">{{ __('Copy Link') }}</span>
                                <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400">{{ __('"Share QR" sends the QR code image itself (like PhonePe/BHIM) — the receiver can scan it from the chat. "Copy Link" gives a plain link instead.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Pending approval — came in through the form, not reviewed yet. --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Awaiting Approval') }} ({{ $pending->count() }})</div>
                @if ($pending->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing waiting right now.') }}</div>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($pending as $lead)
                            <li class="px-5 py-3 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $lead->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->phone }} · {{ $lead->created_at->diffForHumans() }}</div>
                                    @if ($lead->message)
                                        <div class="text-xs text-gray-400 truncate mt-0.5">{{ $lead->message }}</div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('leads.approve', $lead) }}">
                                        @csrf
                                        <button class="inline-flex items-center px-3 py-1.5 bg-accent-600 text-white text-xs font-medium rounded-md hover:bg-accent-700">{{ __('Approve') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('leads.reject', $lead) }}" onsubmit="return confirm('{{ __('Reject this submission?') }}')">
                                        @csrf
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Reject') }}</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Active pipeline — approved, not yet booked. --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Leads') }} ({{ $active->count() }})</div>
                @if ($active->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No active leads yet.') }}</div>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach ($active as $lead)
                            <li>
                                <a href="{{ route('leads.show', $lead) }}" class="px-5 py-3 flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $lead->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->phone }}</div>
                                    </div>
                                    <x-status-badge :status="$lead->status" class="shrink-0" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="add-lead" :show="$errors->has('name') || $errors->has('phone')">
        <form method="POST" action="{{ route('leads.store') }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Lead') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('For a customer you spoke to directly — this goes straight into your active Leads, no approval needed.') }}</p>

            <div>
                <x-input-label for="manual_name" :value="__('Name')" />
                <x-text-input id="manual_name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name') }}" />
            </div>
            <div>
                <x-input-label for="manual_phone" :value="__('Phone')" />
                <x-text-input id="manual_phone" name="phone" type="text" class="mt-1 block w-full" required value="{{ old('phone') }}" />
            </div>
            <div>
                <x-input-label for="manual_email" :value="__('Email (optional)')" />
                <x-text-input id="manual_email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" />
            </div>
            <div>
                <x-input-label for="manual_message" :value="__('Note (optional)')" />
                <textarea id="manual_message" name="message" rows="2" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('message') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Add Lead') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
