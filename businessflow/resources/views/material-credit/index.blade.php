<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Material Credit') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Material or labor taken on credit from a vendor — nothing paid yet. Marked with the "Udhar liya" checkbox when adding a Payment (Kharcha) entry on a project.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="text-xs uppercase text-gray-400">{{ __('Total Udhar Outstanding') }}</div>
                <div class="mt-1 text-3xl font-bold text-red-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totalOutstanding, 0) }}</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('By Project') }}</div>
                    @if ($byProject->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing outstanding.') }}</div>
                    @else
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($byProject as $project => $amount)
                                    <tr>
                                        <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $project }}</td>
                                        <td class="px-5 py-2 text-right font-medium text-red-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($amount, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('By Vendor') }}</div>
                    @if ($byVendor->isEmpty())
                        <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('Nothing outstanding.') }}</div>
                    @else
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($byVendor as $vendor => $amount)
                                    <tr>
                                        <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $vendor }}</td>
                                        <td class="px-5 py-2 text-right font-medium text-red-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($amount, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden" x-data="{ paying: null }">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Outstanding — one by one') }} ({{ $outstanding->count() }})</div>
                @if ($outstanding->isEmpty())
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ __('No udhar outstanding right now.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-2 text-left">{{ __('Date') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Project') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Vendor') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Description') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                                    <th class="px-5 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($outstanding as $cost)
                                    <tr>
                                        <td class="px-5 py-2 whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $cost->spent_on->format('d M Y') }}</td>
                                        <td class="px-5 py-2 text-gray-900 dark:text-gray-100">
                                            @if ($cost->project)
                                                <a href="{{ route('projects.show', $cost->project) }}" class="text-accent-600 hover:underline">{{ $cost->project->name }}</a>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $cost->vendor ?: '—' }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $cost->description }}</td>
                                        <td class="px-5 py-2 text-right font-medium text-red-600">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($cost->amount, 0) }}</td>
                                        <td class="px-5 py-2 text-right whitespace-nowrap">
                                            <button type="button" x-on:click="paying = paying === {{ $cost->id }} ? null : {{ $cost->id }}" class="text-xs text-accent-600 hover:underline">{{ __('Mark as Paid') }}</button>
                                        </td>
                                    </tr>
                                    <tr x-show="paying === {{ $cost->id }}" x-cloak>
                                        <td colspan="6" class="px-5 py-3 bg-gray-50 dark:bg-slate-700/40">
                                            <form method="POST" action="{{ route('project-costs.settle', [$cost->project_id, $cost]) }}" class="flex flex-wrap items-end gap-3">
                                                @csrf
                                                <div>
                                                    <x-input-label :value="__('Paid from')" class="text-xs" />
                                                    <select name="payment_account_id" required class="mt-1 text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                                        <option value="">{{ __('Choose account…') }}</option>
                                                        @foreach ($paymentAccounts as $account)
                                                            <option value="{{ $account->id }}">{{ $account->label() }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Paid on')" class="text-xs" />
                                                    <input type="date" name="credit_settled_at" value="{{ now()->toDateString() }}" required class="mt-1 text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                                                </div>
                                                <x-primary-button>{{ __('Confirm Paid') }}</x-primary-button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if ($settled->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Recently Settled') }}</div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-2 text-left">{{ __('Paid On') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Project') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Vendor') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Description') }}</th>
                                    <th class="px-5 py-2 text-left">{{ __('Paid From') }}</th>
                                    <th class="px-5 py-2 text-right">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($settled->take(20) as $cost)
                                    <tr>
                                        <td class="px-5 py-2 whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $cost->credit_settled_at->format('d M Y') }}</td>
                                        <td class="px-5 py-2 text-gray-900 dark:text-gray-100">{{ $cost->project?->name }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $cost->vendor ?: '—' }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $cost->description }}</td>
                                        <td class="px-5 py-2 text-gray-600 dark:text-gray-400">{{ $cost->account?->label() ?? '—' }}</td>
                                        <td class="px-5 py-2 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($cost->amount, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
