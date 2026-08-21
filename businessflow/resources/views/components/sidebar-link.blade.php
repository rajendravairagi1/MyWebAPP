@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium bg-accent-600 text-white'
    : 'flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            {{ $icon }}
        </svg>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
