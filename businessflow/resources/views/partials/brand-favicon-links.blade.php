{{--
    Browser-tab favicon, shared live from the marketing site's Admin >
    Branding upload instead of keeping a separate copy here - same
    reasoning as the logo (see application-logo.blade.php). Not the
    apple-touch-icon, which stays per-tenant via PwaController - see
    pwa-head.blade.php.
--}}
<link rel="icon" href="{{ config('app.brand_favicon_ico_url') }}" sizes="32x32">
<link rel="icon" type="image/svg+xml" href="{{ config('app.brand_favicon_svg_url') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ config('app.brand_favicon_32_url') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ config('app.brand_favicon_16_url') }}">
