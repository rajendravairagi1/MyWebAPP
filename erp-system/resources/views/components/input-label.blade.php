@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[var(--muted)]']) }}>
    {{ $value ?? $slot }}
</label>
