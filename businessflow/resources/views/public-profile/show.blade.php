<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $user->name }}@if ($business) — {{ $business->name }} @endif</title>
        <meta name="description" content="{{ $user->about }}">

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) document.documentElement.classList.add('dark');
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100">
        <div class="max-w-2xl mx-auto px-4 py-6 space-y-5">
            @if ($business?->logoDataUri())
                <div class="flex items-center gap-2">
                    <img src="{{ $business->logoDataUri() }}" alt="{{ $business->name }}" class="h-8">
                    <span class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $business->name }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 flex items-start gap-4">
                <span class="h-20 w-20 rounded-full overflow-hidden shrink-0 bg-gray-100 dark:bg-slate-700">
                    @if ($user->photo_path)
                        <img src="{{ route('public-profile.photo', $user->profile_token) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        <x-avatar-graphic :style="$user->avatar" :initials="collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')" class="h-full w-full" />
                    @endif
                </span>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold truncate">{{ $user->name }}</h1>
                    @if ($business)
                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $business->name }}</div>
                    @endif
                    @if ($user->about)
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $user->about }}</p>
                    @endif
                </div>
            </div>

            @if ($user->phone || $user->email || $business?->website)
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400 mb-3">{{ __('Contact') }}</div>
                    <div class="flex flex-wrap gap-3">
                        @if ($user->phone)
                            <a href="tel:{{ $user->phone }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                {{ $user->phone }}
                            </a>
                        @endif
                        @if ($user->email)
                            <a href="mailto:{{ $user->email }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                                {{ $user->email }}
                            </a>
                        @endif
                        @if ($business?->website)
                            <a href="{{ str_starts_with($business->website, 'http') ? $business->website : 'https://'.$business->website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-lg text-sm font-medium whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                                {{ $business->website }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if ($units->isNotEmpty())
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-400 mb-3">{{ __('Available Properties') }} ({{ $units->count() }})</div>
                    <div class="space-y-3">
                        @foreach ($units as $unit)
                            @php $photo = $unit->photos->first(); @endphp
                            <a href="{{ route('property-share.show', $unit->share_token) }}" class="flex items-center gap-4 bg-white dark:bg-slate-800 shadow-sm rounded-xl p-4 hover:ring-2 hover:ring-accent-500 transition">
                                <span class="h-16 w-16 rounded-lg overflow-hidden shrink-0 bg-gray-100 dark:bg-slate-700">
                                    @if ($photo)
                                        <img src="{{ route('property-share.photo', [$unit->share_token, $photo]) }}" alt="{{ $unit->unit_number }}" class="h-full w-full object-cover">
                                    @endif
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium truncate">{{ $unit->project->name }} — {{ $unit->unit_number }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        @if ($unit->type){{ $unit->type }}@endif
                                        @if ($unit->project->location) · {{ $unit->project->location }} @endif
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="font-semibold text-accent-600">{{ $business?->currencySymbol() ?? '₹' }}{{ number_format($unit->price, 0) }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </body>
</html>
