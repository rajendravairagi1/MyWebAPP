<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $unit->project->name }} — {{ $unit->unit_number }}</title>
        <meta name="description" content="{{ $unit->type ? $unit->type.' · ' : '' }}{{ $unit->project->location }}">

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
            <div class="flex items-center gap-3">
                @if ($unit->business?->logoDataUri())
                    <img src="{{ $unit->business->logoDataUri() }}" alt="{{ $unit->business->name }}" class="h-10">
                @endif
                <div class="min-w-0">
                    <div class="font-semibold tracking-tight truncate">{{ $unit->business?->name ?? config('app.name') }}</div>
                    @php $contactLine = $unit->business ? collect([$unit->business->phone, $unit->business->email, $unit->business->website])->filter()->implode(' · ') : ''; @endphp
                    @if ($contactLine)
                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $contactLine }}</div>
                    @endif
                </div>
            </div>

            @if ($photos->isNotEmpty())
                <div x-data="{ active: 0, count: {{ $photos->count() }} }" class="relative rounded-xl overflow-hidden bg-black">
                    @foreach ($photos as $i => $photo)
                        <img x-show="active === {{ $i }}" x-cloak src="{{ route('property-share.photo', [$unit->share_token, $photo]) }}" alt="{{ $unit->project->name }}" class="w-full h-72 object-cover">
                    @endforeach
                    @if ($photos->count() > 1)
                        <button type="button" x-on:click="active = (active - 1 + count) % count" class="absolute left-2 top-1/2 -translate-y-1/2 h-8 w-8 rounded-full bg-black/40 text-white flex items-center justify-center hover:bg-black/60">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button type="button" x-on:click="active = (active + 1) % count" class="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8 rounded-full bg-black/40 text-white flex items-center justify-center hover:bg-black/60">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </button>
                        <div class="absolute bottom-0 inset-x-0 flex justify-center gap-1.5 py-2 bg-gradient-to-t from-black/60 to-transparent">
                            @foreach ($photos as $i => $photo)
                                <button type="button" x-on:click="active = {{ $i }}" :class="active === {{ $i }} ? 'bg-white' : 'bg-white/40'" class="h-1.5 w-1.5 rounded-full"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="rounded-xl bg-gray-200 dark:bg-slate-800 h-56 flex items-center justify-center text-gray-400">
                    {{ __('No photos yet') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="text-lg font-semibold">{{ $unit->project->name }} — {{ $unit->unit_number }}</h1>
                        @if ($unit->project->location)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $unit->project->location }}</div>
                        @endif
                    </div>
                    <span @class([
                        'text-xs px-2 py-1 rounded font-medium shrink-0',
                        'bg-green-100 text-green-700' => $unit->status === 'available',
                        'bg-amber-100 text-amber-700' => $unit->status === 'booked',
                        'bg-gray-200 text-gray-600' => $unit->status === 'sold',
                    ])>{{ ucfirst($unit->status) }}</span>
                </div>

                <div class="text-2xl font-bold text-accent-600">{{ $unit->business?->currencySymbol() ?? '₹' }}{{ number_format($unit->price, 0) }}</div>

                <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300 pt-1">
                    @if ($unit->type)
                        <div><span class="text-gray-400">{{ __('Type') }}:</span> {{ $unit->type }}</div>
                    @endif
                    @if ($unit->area_sqft)
                        <div><span class="text-gray-400">{{ __('Area') }}:</span> {{ number_format($unit->area_sqft, 0) }} {{ __('sqft') }}</div>
                    @endif
                </div>
            </div>

            @if ($unit->contact_name || $unit->contact_phone || $unit->contact_email)
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400 mb-2">{{ __('Contact for this property') }}</div>
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            @if ($unit->contact_name)
                                <div class="font-medium truncate">{{ $unit->contact_name }}</div>
                            @endif
                            @if ($unit->contact_email)
                                <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $unit->contact_email }}</div>
                            @endif
                        </div>
                        @if ($unit->contact_phone)
                            <a href="tel:{{ $unit->contact_phone }}" class="shrink-0 inline-flex items-center gap-1.5 h-10 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                {{ $unit->contact_phone }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex gap-3">
                <a href="{{ route('property-share.pdf', $unit->share_token) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 h-11 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    {{ __('Download PDF') }}
                </a>
                <a href="https://wa.me/?text={{ urlencode($unit->project->name.' — '.$unit->unit_number."\n".($unit->business?->currencySymbol() ?? '₹').number_format($unit->price, 0)."\n".url()->current()) }}" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-1.5 h-11 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-green-600 text-white hover:bg-green-700">
                    <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/><path d="M12.004 2.003c-5.514 0-9.997 4.483-9.997 9.997 0 1.762.462 3.478 1.34 4.985L2 22l5.146-1.35a9.955 9.955 0 004.858 1.237h.004c5.514 0 9.997-4.483 9.997-9.997 0-2.67-1.04-5.182-2.929-7.071a9.935 9.935 0 00-7.072-2.926zm0 18.19a8.222 8.222 0 01-4.19-1.147l-.301-.179-3.055.801.815-2.978-.196-.306a8.213 8.213 0 01-1.259-4.384c0-4.535 3.69-8.225 8.226-8.225 2.196 0 4.26.856 5.815 2.41a8.169 8.169 0 012.408 5.816c0 4.536-3.69 8.226-8.226 8.226z"/></svg>
                    {{ __('Share on WhatsApp') }}
                </a>
            </div>
        </div>
    </body>
</html>
