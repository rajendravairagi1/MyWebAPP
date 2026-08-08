@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[var(--brand-400)] text-sm font-medium leading-5 text-[var(--text)] focus:outline-none focus:border-[var(--brand-700)] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[var(--muted)] hover:text-[var(--muted)] hover:border-[var(--border)] focus:outline-none focus:text-[var(--muted)] focus:border-[var(--border)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
