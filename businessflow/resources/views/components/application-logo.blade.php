{{--
    The real logo is a wide horizontal lockup (icon + "ProBuilderCRM"
    wordmark side by side), not a square mark - so its height is set
    explicitly via $baseHeight and its width follows the image's own
    aspect ratio. That base height then scales up/down together with
    every other logo on the page via --brand-logo-scale, kept in sync
    with the marketing site's Admin > Branding "Logo size" setting -
    see partials/brand-logo-sync.blade.php.

    The "P" fallback mark IS square, so it gets an explicit aspect-square
    instead of stretching to match whatever width the real logo would
    have taken.
--}}
@props(['url' => null, 'baseHeight' => '2.75rem'])
<span {{ $attributes->class(['inline-flex items-center']) }} style="--brand-logo-base-h: {{ $baseHeight }};">
    <img
        src="{{ config('app.brand_logo_url') }}"
        alt="{{ config('app.name', 'ProBuilderCRM') }}"
        class="brand-logo-img object-contain"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
    >
    <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" class="brand-logo-img aspect-square fill-current" style="display: none;">
        <rect width="40" height="40" rx="9" fill="currentColor" />
        <text x="20" y="27" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, Helvetica, sans-serif" font-size="20" font-weight="800" fill="#ffffff">P</text>
    </svg>
</span>
