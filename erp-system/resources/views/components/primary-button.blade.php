<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[image:var(--brand-grad)] text-[var(--btn-text)] border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--brand-500)] focus:ring-offset-2 transition ease-in-out duration-150 shadow']) }}>
    {{ $slot }}
</button>
