<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('admin.index') }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back to Platform Admin') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Backups') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Automatic per-business backups') }}</div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Every business on app.probuildercrm.com gets its own backup file (data + uploaded files), named after it. Backups older than 10 days are deleted automatically so this never fills up the server.') }}
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.backups.run') }}">
                    @csrf
                    <button class="btn btn-primary whitespace-nowrap px-4 py-2 text-sm font-medium rounded-md bg-accent-600 text-white hover:bg-accent-700">
                        {{ __('Run backup now') }}
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-2 text-left">{{ __('Business') }}</th>
                            <th class="px-5 py-2 text-left">{{ __('File') }}</th>
                            <th class="px-5 py-2 text-left">{{ __('Size') }}</th>
                            <th class="px-5 py-2 text-left">{{ __('Created') }}</th>
                            <th class="px-5 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse ($backups as $b)
                            <tr>
                                <td class="px-5 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $b['business_name'] ?? __('(deleted business)') }}</td>
                                <td class="px-5 py-2 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $b['filename'] }}</td>
                                <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ number_format($b['size'] / 1024 / 1024, 2) }} MB</td>
                                <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ \Illuminate\Support\Carbon::createFromTimestamp($b['created_at'])->format('d M Y, h:i A') }}</td>
                                <td class="px-5 py-2">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.backups.download', $b['filename']) }}" class="text-xs text-accent-600 hover:underline whitespace-nowrap">{{ __('Download') }}</a>
                                        <form method="POST" action="{{ route('admin.backups.destroy', $b['filename']) }}" onsubmit="return confirm('{{ __('Delete this backup file permanently?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline whitespace-nowrap">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-center text-gray-500 dark:text-gray-400">{{ __('No backups yet — click "Run backup now" or wait for the next automatic run.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
