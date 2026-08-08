<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[var(--card)] border border-[var(--border)] rounded-md font-semibold text-xs text-[var(--muted)] uppercase tracking-widest shadow-sm hover:bg-[var(--bg)] focus:outline-none focus:ring-2 focus:ring-[var(--brand-500)] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
