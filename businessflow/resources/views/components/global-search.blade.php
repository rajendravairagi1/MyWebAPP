<div
    x-data="{
        open: false,
        q: '',
        loading: false,
        results: [],
        timer: null,
        search() {
            clearTimeout(this.timer);
            const term = this.q.trim();
            if (term.length < 2) { this.results = []; this.open = false; return; }
            this.timer = setTimeout(() => {
                this.loading = true;
                fetch('{{ route('search') }}?q=' + encodeURIComponent(term), { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => { this.results = data.results; this.open = true; this.loading = false; })
                    .catch(() => { this.loading = false; });
            }, 250);
        },
    }"
    @click.outside="open = false"
    @keydown.escape="open = false"
    class="relative hidden sm:block w-56 md:w-72"
>
    <div class="relative">
        <svg class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
        <input
            type="text"
            x-model="q"
            @input="search()"
            @focus="if (results.length) open = true"
            placeholder="{{ __('Search customers, projects, quotations, invoices...') }}"
            class="w-full h-9 pl-9 pr-3 border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 dark:placeholder-slate-400 rounded-lg shadow-sm text-sm focus:border-accent-500 focus:ring-accent-500"
        >
    </div>

    <div x-show="open" x-cloak x-transition.opacity.duration.100ms
         class="absolute z-50 right-0 mt-1 w-80 md:w-96 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg max-h-96 overflow-y-auto">
        <div x-show="loading" class="p-4 text-sm text-gray-500 dark:text-gray-400">{{ __('Searching...') }}</div>
        <div x-show="!loading && results.length === 0" class="p-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No results.') }}</div>

        <template x-for="group in results" :key="group.group">
            <div>
                <div class="px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-slate-700/60" x-text="group.group"></div>
                <template x-for="item in group.items" :key="item.url">
                    <a :href="item.url" class="flex items-center justify-between gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700">
                        <span class="min-w-0">
                            <span class="block text-sm text-gray-800 dark:text-gray-100 font-medium truncate" x-text="item.title"></span>
                            <span x-show="item.subtitle" class="block text-xs text-gray-500 dark:text-gray-400 truncate" x-text="item.subtitle"></span>
                        </span>
                        <span x-show="item.badge" x-text="item.badge" class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 shrink-0"></span>
                    </a>
                </template>
            </div>
        </template>
    </div>
</div>
