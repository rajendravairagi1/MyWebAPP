{{--
    The real logo is a wide horizontal lockup (icon + "ProBuilderCRM"
    wordmark side by side), not a square mark - so callers pass only a
    height class (e.g. "h-11"), never a width one, and the image's
    natural aspect ratio decides the width. The "P" fallback mark IS
    square, so it gets an explicit aspect-square instead of stretching
    to match whatever width the real logo would have taken.
--}}
@props(['url' => null])
<span {{ $attributes->class(['inline-flex items-center']) }}>
    <img
        src="{{ config('app.brand_logo_url') }}"
        alt="{{ config('app.name', 'ProBuilderCRM') }}"
        class="h-full w-auto object-contain"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
    >
    <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" class="h-full aspect-square fill-current" style="display: none;">
        <rect width="40" height="40" rx="9" fill="currentColor" />
        <text x="20" y="27" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, Helvetica, sans-serif" font-size="20" font-weight="800" fill="#ffffff">P</text>
    </svg>
</span>
