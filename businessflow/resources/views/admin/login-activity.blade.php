<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Login Activity') }}</h2>
            <a href="{{ route('admin.index') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                {{ __('Back to Admin') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Every successful login across every account — who, when, from where, and on what device.') }}
            </p>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <form method="GET" action="{{ route('admin.login-activity') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Search name, email, business, or IP…') }}"
                        class="flex-1 max-w-sm text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                    <button class="text-sm px-4 py-2 rounded-md border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Search') }}</button>
                    @if ($search !== '')
                        <a href="{{ route('admin.login-activity') }}" class="text-sm px-4 py-2 rounded-md text-gray-500 dark:text-gray-400 hover:underline">{{ __('Clear search') }}</a>
                    @endif
                </form>

                @if ($logs->total() > 0)
                    <form method="POST" action="{{ route('admin.login-activity.clear') }}" onsubmit="return confirm('{{ __('Delete every login record? This cannot be undone.') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm px-4 py-2 rounded-md border border-red-200 dark:border-red-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">{{ __('Clear All') }}</button>
                    </form>
                @endif
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($logs->isEmpty())
                    <div class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No logins recorded yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-2 text-left">{{ __('Account') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Business') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('IP Address') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Location') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Device') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Browser') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('When') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($logs as $log)
                                    <tr>
                                        <td class="px-5 py-3">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $log->user_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $log->user_email }}</div>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $log->business_name ?? '—' }}</td>
                                        <td class="px-5 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $log->ip_address }}</td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $log->city ? $log->city.', ' : '' }}{{ $log->country ?? __('Unknown') }}
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-full {{ $log->device_type === 'Mobile' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : ($log->device_type === 'Tablet' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300') }}">
                                                @if ($log->device_type === 'Mobile')
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                                                @elseif ($log->device_type === 'Tablet')
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v13.5a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z" /></svg>
                                                @else
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                                                @endif
                                                {{ $log->device_type }} · {{ $log->platform }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $log->browser }}</td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            <div>{{ $log->logged_in_at->format('d M Y, h:i A') }}</div>
                                            <div class="text-xs text-gray-400">{{ $log->logged_in_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
