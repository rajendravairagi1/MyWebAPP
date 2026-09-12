<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('admin.index') }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back to Platform Admin') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Signup Requests') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 lg:order-1 space-y-6">
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Awaiting Approval') }} ({{ $pending->count() }})</div>
                        @if ($pending->isEmpty())
                            <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing waiting right now.') }}</div>
                        @else
                            <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                                @foreach ($pending as $r)
                                    <li class="px-5 py-3 flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $r->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $r->phone }} · {{ $r->email }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $r->planLabel() }} · {{ $r->created_at->diffForHumans() }}</div>
                                            @if ($r->address)
                                                <div class="text-xs text-gray-400 truncate mt-0.5">{{ $r->address }}</div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="{{ route('admin.create', ['signup_request_id' => $r->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-accent-600 text-white text-xs font-medium rounded-md hover:bg-accent-700">{{ __('Approve') }}</a>
                                            <form method="POST" action="{{ route('admin.signup-requests.reject', $r) }}" onsubmit="return confirm('{{ __('Reject this request?') }}')">
                                                @csrf
                                                <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Reject') }}</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    @if ($recent->isNotEmpty())
                        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Recently Reviewed') }}</div>
                            <ul class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                                @foreach ($recent as $r)
                                    <li class="px-5 py-3 flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $r->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $r->phone }} · {{ $r->email }} · {{ $r->planLabel() }}</div>
                                        </div>
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium shrink-0 {{ $r->status === 'approved' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                            {{ ucfirst($r->status) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- QR card — same Download / Share / Copy Link pattern as the per-business Lead QR. --}}
                <div class="lg:order-2">
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5" x-data="{ copied: false }">
                        <div class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-3 text-center">{{ __('Share to get new signups') }}</div>
                        <img src="{{ $posterUrl }}" alt="{{ __('Signup request QR') }}" class="w-full max-w-[220px] mx-auto rounded-lg border border-gray-200 dark:border-slate-700">
                        <div class="mt-4 flex flex-col gap-2">
                            <button type="button" id="signup-qr-share-btn" class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">{{ __('Share') }}</button>
                            <button type="button" id="signup-qr-download-btn" class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Download') }}</button>
                            <button type="button" x-on:click="navigator.clipboard.writeText('{{ $publicUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">
                                <span x-show="!copied">{{ __('Copy Link') }}</span>
                                <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Same plain DOM wiring as the Leads QR card (see leads/index.blade.php)
                // rather than Alpine's x-on:click — if the JS bundle is ever stale, an
                // "not defined" error should surface as a visible message, not a
                // silently dead button.
                document.addEventListener('DOMContentLoaded', function () {
                    var posterUrl = @json($posterUrl);
                    var refreshMsg = @json(__('This page loaded an old version of the app. Please close this tab, reopen this page, and try again.'));

                    if (typeof preloadFile === 'function') {
                        preloadFile(posterUrl);
                    }

                    var shareBtn = document.getElementById('signup-qr-share-btn');
                    if (shareBtn) {
                        shareBtn.addEventListener('click', function () {
                            if (typeof shareImageFile === 'function') {
                                shareImageFile(posterUrl, 'signup-qr.png', shareBtn);
                            } else {
                                window.alert(refreshMsg);
                            }
                        });
                    }

                    var downloadBtn = document.getElementById('signup-qr-download-btn');
                    if (downloadBtn) {
                        downloadBtn.addEventListener('click', function () {
                            if (typeof downloadImageFile === 'function') {
                                downloadImageFile(posterUrl, 'signup-qr.png', downloadBtn);
                            } else {
                                window.alert(refreshMsg);
                            }
                        });
                    }
                });
            </script>
        </div>
    </div>
</x-app-layout>
