<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Archived Accounts') }}</h2>
            <a href="{{ route('admin.index') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                {{ __('Back to Platform Admin') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Accounts removed from the Businesses/Companies lists end up here instead of being deleted — everything about them stays exactly as it was until you restore one.') }}
            </p>

            <x-list-toolbar placeholder="{{ __('Search by name...') }}" />

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($archived->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">
                        {{ request('q') ? __('No archived accounts match that search.') : __('No archived accounts.') }}
                    </div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Account') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Owner') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Phone') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Removed on') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($archived as $entry)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $entry->name }}
                                        <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 ml-1">{{ $entry->type === 'company' ? __('company') : __('business') }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $entry->owner_name }} <span class="text-gray-400">({{ $entry->owner_email }})</span></td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $entry->phone ?: '—' }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $entry->deleted_at->format('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        <form method="POST" action="{{ route($entry->type === 'company' ? 'admin.companies.restore' : 'admin.businesses.restore', $entry->id) }}">
                                            @csrf
                                            <button class="text-xs text-accent-600 hover:underline whitespace-nowrap">{{ __('Restore') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endif
            </div>

            {{ $archived->links() }}
        </div>
    </div>
</x-app-layout>
