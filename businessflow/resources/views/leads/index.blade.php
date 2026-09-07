<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Leads') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-lead')" class="inline-flex items-center px-3 py-1.5 bg-accent-600 text-white text-xs font-medium rounded-md hover:bg-accent-700">{{ __('+ Add Lead') }}</button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 lg:order-1 space-y-6">
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
                </div>

                {{-- QR card — image plus three plain-labelled buttons, shifted to the right as a sidebar. --}}
                <div class="lg:order-2">
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5" x-data="{ copied: false }">
                        <img src="{{ $posterUrl }}" alt="{{ __('Lead form QR poster') }}" class="w-full max-w-[220px] mx-auto rounded-lg border border-gray-200 dark:border-slate-700">
                        <div class="mt-4 flex flex-col gap-2">
                            <button type="button" id="lead-qr-share-btn" class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">{{ __('Share') }}</button>
                            <button type="button" id="lead-qr-download-btn" class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Download') }}</button>
                            <button type="button" x-on:click="navigator.clipboard.writeText('{{ $publicUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">
                                <span x-show="!copied">{{ __('Copy Link') }}</span>
                                <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Deliberately plain DOM wiring (not Alpine's x-on:click) for
                // these two buttons: if the JS bundle is ever stale (an old
                // cached tab, a deploy that didn't fully take), Alpine's
                // click-expression evaluator swallows the resulting
                // "not defined" error into the console — the button looks
                // completely dead with nothing on screen to explain why.
                // This runs after the module script (which defines
                // shareImageFile/downloadImageFile/preloadFile) has executed,
                // and falls back to a visible, explicit message instead of
                // silent failure if those still aren't there.
                document.addEventListener('DOMContentLoaded', function () {
                    var posterUrl = @json($posterUrl);
                    var refreshMsg = @json(__('This page loaded an old version of the app. Please close this tab, reopen the Leads page, and try again.'));

                    if (typeof preloadFile === 'function') {
                        preloadFile(posterUrl);
                    }

                    var shareBtn = document.getElementById('lead-qr-share-btn');
                    if (shareBtn) {
                        shareBtn.addEventListener('click', function () {
                            if (typeof shareImageFile === 'function') {
                                shareImageFile(posterUrl, 'lead-qr.png', shareBtn);
                            } else {
                                window.alert(refreshMsg);
                            }
                        });
                    }

                    var downloadBtn = document.getElementById('lead-qr-download-btn');
                    if (downloadBtn) {
                        downloadBtn.addEventListener('click', function () {
                            if (typeof downloadImageFile === 'function') {
                                downloadImageFile(posterUrl, 'lead-qr.png', downloadBtn);
                            } else {
                                window.alert(refreshMsg);
                            }
                        });
                    }
                });
            </script>
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
