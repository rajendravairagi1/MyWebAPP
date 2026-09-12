<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('admin.index') }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="{{ __('Back to Platform Admin') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Admin') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Everyone with a login, across every account') }}</div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Every person on any customer\'s Business or Company — their role, and the same suspend / remove / reset-password actions each business owner already has for their own Team, just not limited to one business.') }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    {{ __('Owners, Company Owners and Branch Managers are shown but not editable here — their account access is controlled by the Business/Company status toggle on Platform Admin instead.') }}
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-2 text-left">{{ __('Person') }}</th>
                            <th class="px-5 py-2 text-left">{{ __('Business / Company') }}</th>
                            <th class="px-5 py-2 text-left">{{ __('Role') }}</th>
                            <th class="px-5 py-2 text-left">{{ __('Status') }}</th>
                            <th class="px-5 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($memberships as $m)
                            <tr>
                                <td class="px-5 py-2">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $m['user']->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $m['user']->email }}</div>
                                </td>
                                <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $m['business_label'] }}</td>
                                <td class="px-5 py-2 text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $m['role']) }}</td>
                                <td class="px-5 py-2">
                                    @if ($m['status'] === 'suspended')
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ __('Suspended') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ ucfirst($m['status']) }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-2">
                                    <div class="flex flex-col items-start gap-1">
                                        <div x-data="{ open: false }">
                                            <button type="button" x-show="!open" x-on:click="open = true" class="text-xs text-accent-600 hover:underline whitespace-nowrap">{{ __('Reset password') }}</button>
                                            <form x-show="open" x-cloak method="POST" action="{{ route('admin.users.password', $m['user']) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="password" placeholder="{{ __('New password') }}" minlength="8" required class="w-32 shrink-0 text-xs border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500 py-1">
                                                <button class="shrink-0 text-xs text-accent-600 hover:underline whitespace-nowrap">{{ __('Save') }}</button>
                                            </form>
                                        </div>
                                        @unless ($m['protected'])
                                            <form method="POST" action="{{ route('admin.users.status', [$m['business_id'], $m['user']]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $m['status'] === 'suspended' ? 'active' : 'suspended' }}">
                                                <button class="text-xs {{ $m['status'] === 'suspended' ? 'text-green-600' : 'text-amber-600' }} hover:underline whitespace-nowrap">
                                                    {{ $m['status'] === 'suspended' ? __('Reactivate') : __('Suspend') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.destroy', [$m['business_id'], $m['user']]) }}" onsubmit="return confirm('{{ __('Remove this person from this account? They lose access immediately.') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs text-red-600 hover:underline whitespace-nowrap">{{ __('Remove') }}</button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
