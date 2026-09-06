{{--
    Drop into any index page that has a paginated collection. Submits a
    single GET form so the search term and per-page choice are applied
    together and both survive in the URL (?q=...&per_page=...), which is
    also what the paginator's ->withQueryString() call carries across
    page links.
--}}
@props(['placeholder' => 'Search...'])

<form method="GET" class="flex flex-wrap items-center gap-3">
    {{-- Preserves any other filter already in the URL (e.g. invoices' ?status=paid)
         so submitting a search or changing per-page doesn't silently reset it. --}}
    @foreach (request()->except(['q', 'per_page', 'page']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}"
        class="w-full max-w-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:border-accent-500 focus:ring-accent-500">

    <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 shrink-0">
        {{ __('Show') }}
        <select name="per_page" onchange="this.form.submit()"
            class="border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:border-accent-500 focus:ring-accent-500">
            @foreach ([10, 20, 50] as $option)
                <option value="{{ $option }}" @selected((int) request('per_page', 20) === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </label>

    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">
        {{ __('Search') }}
    </button>

    @if (request('q'))
        <a href="{{ url()->current() }}" class="text-sm text-accent-600 hover:underline">{{ __('Clear') }}</a>
    @endif
</form>
