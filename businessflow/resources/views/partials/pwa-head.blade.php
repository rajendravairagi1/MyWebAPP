{{-- "Add to Home Screen" — the manifest and icon are generated per
     business, so whichever logo is set in Business Settings is exactly
     what installs on the phone's home screen. Included on every
     top-level page (app, guest, welcome) so the install prompt is
     available whether or not you're logged in yet. --}}
<link rel="manifest" href="{{ route('pwa.manifest') }}">
<link rel="apple-touch-icon" href="{{ route('pwa.icon', 192) }}">
<meta name="theme-color" content="#4f46e5">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'BusinessFlow') }}">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () { navigator.serviceWorker.register('/sw.js'); });
    }
</script>
