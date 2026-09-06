{{-- Search-only variant of <x-list-toolbar> for pages that don't paginate. --}}
@props(['placeholder' => 'Search...'])

<form method="GET" class="flex flex-wrap items-center gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}"
        class="w-full max-w-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:border-accent-500 focus:ring-accent-500">

    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-slate-700">
        {{ __('Search') }}
    </button>

    @if (request('q'))
        <a href="{{ url()->current() }}" class="text-sm text-accent-600 hover:underline">{{ __('Clear') }}</a>
    @endif
</form>
