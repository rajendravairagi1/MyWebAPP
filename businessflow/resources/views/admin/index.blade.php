<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Platform Admin') }}</h2>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.clear-cache') }}">
                    @csrf
                    <button class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                        {{ __('Clear Cache') }}
                    </button>
                </form>
                <a href="{{ route('admin.login-activity') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                    {{ __('Login Activity') }}
                </a>
                <a href="{{ route('admin.expiring') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap border border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    {{ __('Expiring Soon') }}
                </a>
                <a href="{{ route('admin.create') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                    {{ __('+ Add Customer Account') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            {{-- Demo account — normally exactly one row. The oldest one is
                 treated as the real public demo; any others only exist
                 because "is_demo" got ticked by mistake while adding a
                 real customer, which otherwise makes that customer's
                 whole account silently disappear from the lists below. --}}
            @php $demoBusiness = $demoBusinesses->first(); @endphp
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 flex items-center justify-between gap-4">
                <div>
                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ __('Public Demo Account') }}</div>
                    @if ($demoBusiness)
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $demoBusiness->name }} — {{ __('linked to the homepage "See Demo" button') }}</div>
                    @else
                        <div class="text-xs text-amber-600 mt-0.5">{{ __('None set up yet — the "See Demo" button on the homepage won\'t work until you tick "is_demo" on an account below.') }}</div>
                    @endif
                </div>
                @if ($demoBusiness)
                    <div class="flex items-center gap-3 shrink-0">
                        <form method="POST" action="{{ route('admin.businesses.plan', $demoBusiness) }}" class="inline-flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('Plan') }}</label>
                            <select name="plan" onchange="this.form.submit()" class="text-xs border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                <option value="solo" @selected($demoBusiness->plan === 'solo')>{{ __('Solo') }}</option>
                                <option value="team" @selected($demoBusiness->plan === 'team')>{{ __('Team') }}</option>
                                <option value="company" @selected($demoBusiness->plan === 'company')>{{ __('Company (unlock)') }}</option>
                            </select>
                        </form>
                        <form method="POST" action="{{ route('admin.demo.reset', $demoBusiness) }}" onsubmit="return confirm('{{ __('Wipe all data in the demo account? This cannot be undone.') }}')">
                            @csrf
                            <button class="text-xs px-3 py-1.5 rounded-md border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 whitespace-nowrap">{{ __('Reset Demo Data') }}</button>
                        </form>
                    </div>
                @endif
            </div>

            @if ($demoBusinesses->count() > 1)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-5 space-y-3">
                    <div class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                        {{ __('These accounts are also marked as the public demo — probably by mistake ("This is the public demo account" got ticked while adding a real customer). While marked this way, they\'re hidden from the Businesses list below, share the same demo login as everyone who clicks "See Demo", and their data would be wiped by "Reset Demo Data" above.') }}
                    </div>
                    @foreach ($demoBusinesses->skip(1) as $strayDemo)
                        @php $strayOwner = $strayDemo->users->first(); @endphp
                        <div class="flex items-center justify-between gap-3 bg-white dark:bg-slate-800 rounded-md p-3">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $strayDemo->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $strayOwner?->name }} <span class="text-gray-400">({{ $strayOwner?->email }})</span></div>
                            </div>
                            <form method="POST" action="{{ route('admin.businesses.unmark-demo', $strayDemo) }}" class="shrink-0">
                                @csrf
                                <button class="text-xs px-3 py-1.5 rounded-md bg-amber-600 text-white hover:bg-amber-700 whitespace-nowrap">{{ __('Not a demo — restore as normal account') }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Standalone businesses (Solo / Team plans) --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Businesses') }} ({{ __('Solo / Team plans') }})</div>
                @if ($businesses->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Business') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Owner') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Plan') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Valid till') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($businesses as $business)
                                @php $owner = $business->users->first(); @endphp
                                <tr>
                                    <td class="px-5 py-2 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $business->name }}
                                        @if ($business->is_demo)
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 ml-1">{{ __('demo') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $owner?->name }} <span class="text-gray-400">({{ $owner?->email }})</span></td>
                                    <td class="px-5 py-2">
                                        <form method="POST" action="{{ route('admin.businesses.plan', $business) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="plan" onchange="this.form.submit()" class="text-xs border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                                <option value="solo" @selected($business->plan === 'solo')>{{ __('Solo') }}</option>
                                                <option value="team" @selected($business->plan === 'team')>{{ __('Team') }}</option>
                                                <option value="company" @selected($business->plan === 'company')>{{ __('Company (unlock)') }}</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-5 py-2">
                                        @php
                                            $expired = $business->isSubscriptionExpired();
                                            $expiringSoon = ! $expired && $business->subscription_expires_at && $business->subscription_expires_at->diffInDays(now(), false) >= -7;
                                        @endphp
                                        <form method="POST" action="{{ route('admin.businesses.expiry', $business) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            @if ($expired)
                                                <span class="shrink-0 text-xs font-semibold text-red-600">{{ __('Expired') }}</span>
                                            @elseif ($expiringSoon)
                                                <span class="shrink-0 text-xs font-semibold text-amber-600">{{ __('Soon') }}</span>
                                            @endif
                                            <input type="date" name="subscription_expires_at" value="{{ $business->subscription_expires_at?->format('Y-m-d') }}" @class([
                                                'w-32 shrink-0 text-xs rounded-md shadow-sm dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500 py-1',
                                                'border-red-300 dark:border-red-800' => $expired,
                                                'border-amber-300 dark:border-amber-800' => $expiringSoon,
                                                'border-gray-300 dark:border-slate-600' => ! $expired && ! $expiringSoon,
                                            ])>
                                            <button class="shrink-0 text-xs text-accent-600 hover:underline whitespace-nowrap">{{ __('Save') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Companies (Company plan) --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Companies') }} ({{ __('Multi-branch plan') }})</div>
                @if ($companies->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('None yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 text-left">{{ __('Company') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Owner') }}</th>
                                <th class="px-5 py-2 text-right">{{ __('Branches') }}</th>
                                <th class="px-5 py-2 text-left">{{ __('Valid till') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($companies as $company)
                                <tr>
                                    <td class="px-5 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $company->owner->name }} <span class="text-gray-400">({{ $company->owner->email }})</span></td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ $company->branches_count }}</td>
                                    <td class="px-5 py-2">
                                        @php
                                            $expired = $company->subscription_expires_at?->copy()->endOfDay()->isPast() ?? false;
                                            $expiringSoon = ! $expired && $company->subscription_expires_at && $company->subscription_expires_at->diffInDays(now(), false) >= -7;
                                        @endphp
                                        <form method="POST" action="{{ route('admin.companies.expiry', $company) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            @if ($expired)
                                                <span class="shrink-0 text-xs font-semibold text-red-600">{{ __('Expired') }}</span>
                                            @elseif ($expiringSoon)
                                                <span class="shrink-0 text-xs font-semibold text-amber-600">{{ __('Soon') }}</span>
                                            @endif
                                            <input type="date" name="subscription_expires_at" value="{{ $company->subscription_expires_at?->format('Y-m-d') }}" @class([
                                                'w-32 shrink-0 text-xs rounded-md shadow-sm dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500 py-1',
                                                'border-red-300 dark:border-red-800' => $expired,
                                                'border-amber-300 dark:border-amber-800' => $expiringSoon,
                                                'border-gray-300 dark:border-slate-600' => ! $expired && ! $expiringSoon,
                                            ])>
                                            <button class="shrink-0 text-xs text-accent-600 hover:underline whitespace-nowrap">{{ __('Save') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
