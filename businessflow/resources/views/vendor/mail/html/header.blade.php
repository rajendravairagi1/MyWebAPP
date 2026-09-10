@props(['url'])
@php
    // Emails can't run JS, so the logo size sync (see
    // partials/brand-logo-sync.blade.php for the JS version used on
    // regular pages) happens server-side here instead: a short, cached,
    // best-effort call to the same endpoint, scaling this email's base
    // 40px the same way the website's own logo would scale.
    $emailLogoHeight = 40;
    if (trim($slot) !== 'Laravel') {
        $logoHeightPx = \Illuminate\Support\Facades\Cache::remember('brand_logo_height_px', now()->addMinutes(10), function () {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(2)->get(config('app.brand_logo_size_url'));

                return $response->ok() ? (int) $response->json('height') : 44;
            } catch (\Throwable $e) {
                return 44;
            }
        });
        $emailLogoHeight = (int) round(40 * $logoHeightPx / 44);
    }
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ config('app.brand_logo_url') }}" alt="{{ $slot }}" height="{{ $emailLogoHeight }}" style="height: {{ $emailLogoHeight }}px; width: auto; vertical-align: middle;">
@endif
</a>
</td>
</tr>
