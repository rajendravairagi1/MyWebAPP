<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Platform Admin') }}</h2>
            <a href="{{ route('admin.create') }}" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Add Customer Account') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            {{-- Demo account --}}
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
                    <form method="POST" action="{{ route('admin.demo.reset') }}" onsubmit="return confirm('{{ __('Wipe all data in the demo account? This cannot be undone.') }}')">
                        @csrf
                        <button class="text-xs px-3 py-1.5 rounded-md border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 whitespace-nowrap">{{ __('Reset Demo Data') }}</button>
                    </form>
                @endif
            </div>

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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($companies as $company)
                                <tr>
                                    <td class="px-5 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</td>
                                    <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $company->owner->name }} <span class="text-gray-400">({{ $company->owner->email }})</span></td>
                                    <td class="px-5 py-2 text-right text-gray-600 dark:text-gray-400">{{ $company->branches_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
