@props(['url' => null])
<span {{ $attributes->class(['inline-flex']) }}>
    <img
        src="{{ config('app.brand_logo_url') }}"
        alt="{{ config('app.name', 'ProBuilderCRM') }}"
        class="h-full w-full object-contain"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
    >
    <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" class="h-full w-full fill-current" style="display: none;">
        <rect width="40" height="40" rx="9" fill="currentColor" />
        <text x="20" y="27" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, Helvetica, sans-serif" font-size="20" font-weight="800" fill="#ffffff">P</text>
    </svg>
</span>
