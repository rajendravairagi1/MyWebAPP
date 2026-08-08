@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[var(--brand-400)] text-start text-base font-medium text-[var(--brand-700)] bg-[var(--brand-50)] focus:outline-none focus:text-[var(--brand-800)] focus:bg-[var(--brand-100)] focus:border-[var(--brand-700)] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--bg)] hover:border-[var(--border)] focus:outline-none focus:text-[var(--text)] focus:bg-[var(--bg)] focus:border-[var(--border)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
