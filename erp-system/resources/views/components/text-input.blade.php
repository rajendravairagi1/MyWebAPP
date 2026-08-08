@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[var(--border)] focus:border-[var(--brand-500)] focus:ring-[var(--brand-500)] rounded-md shadow-sm']) }}>
