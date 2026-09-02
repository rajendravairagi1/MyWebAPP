<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Expiring Soon') }}</h2>
            <a href="{{ route('admin.index') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                {{ __('Back to Admin') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Every builder, solo, and company account whose subscription expires within 7 days, or has already expired. Mark one "Done" once you\'ve chased it — it reappears automatically the moment its expiry date is changed.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($alerts->isEmpty())
                    <div class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing needs renewal right now. 🎉') }}</div>
                @else
                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Account') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Type') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Status') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($alerts as $alert)
                                <tr class="{{ $alert['expired'] ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $alert['name'] }}</td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 capitalize">{{ __($alert['type'] === 'company' ? 'Company' : ucfirst($alert['plan'])) }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs px-1.5 py-0.5 rounded {{ $alert['expired'] ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $alert['expired'] ? __('Expired') : __('Expires') }} {{ $alert['expires_at']->format('d M Y') }}
                                            @unless ($alert['expired'])
                                                · {{ trans_choice('{0} today|{1} :count day left|[2,*] :count days left', now()->startOfDay()->diffInDays($alert['expires_at']->copy()->startOfDay(), false), ['count' => now()->startOfDay()->diffInDays($alert['expires_at']->copy()->startOfDay(), false)]) }}
                                            @endunless
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <form method="POST" action="{{ route($alert['type'] === 'company' ? 'admin.companies.dismiss-renewal' : 'admin.businesses.dismiss-renewal', $alert['id']) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs px-3 py-1.5 rounded-md border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                                                {{ __('Mark Done') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
