<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $branch->name }}</h2>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <a href="{{ route('company.show') }}" class="hover:underline">{{ $branch->company->name }}</a>
                </div>
            </div>
            @if ($isCompanyOwner)
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-builder')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                    {{ __('+ Add Builder') }}
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5 flex items-center justify-between gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Manager') }}:
                    @if ($branch->manager)
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $branch->manager->name }}</span>
                        <span class="text-gray-400">({{ $branch->manager->email }})</span>
                    @else
                        <span class="text-gray-400">{{ __('None assigned') }}</span>
                    @endif
                </div>
                @if ($isCompanyOwner)
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-branch')" class="text-accent-600 hover:underline text-xs shrink-0">{{ __('Edit') }}</button>
                @endif
            </div>

            {{-- Branch-wide P&L across every builder --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-4">{{ __('Branch-wide — all builders') }}</div>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Projects') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $branchTotals['projects'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Collected') }}</div>
                        <div class="mt-1 text-xl font-semibold text-green-600">{{ $branchCurrencySymbol }}{{ number_format($branchTotals['collected'], 0) }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}</div>
                        <div class="mt-1 text-xl font-semibold {{ $branchTotals['outstanding'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $branchCurrencySymbol }}{{ number_format($branchTotals['outstanding'], 0) }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Cost') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $branchCurrencySymbol }}{{ number_format($branchTotals['cost'], 0) }}</div>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Profit / Loss') }}</div>
                        <div class="mt-1 text-xl font-semibold {{ $branchTotals['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $branchCurrencySymbol }}{{ number_format($branchTotals['profit'], 0) }}</div>
                    </div>
                </div>

                @if ($branch->businesses->isNotEmpty())
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-slate-700">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Profit / loss by builder') }}</div>
                        <x-profit-chart :labels="$branch->businesses->pluck('name')" :values="$branch->businesses->map(fn ($b) => round($businessStats[$b->id]['profit'], 2))" />
                    </div>
                @endif
            </div>

            @if ($branch->businesses->isEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No builders in this branch yet.') }}
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Builder') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Projects') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Customers') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Collected') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Outstanding') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Profit') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($branch->businesses as $business)
                                @php $stats = $businessStats[$business->id]; @endphp
                                <tr>
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $business->name }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ $stats['projects'] }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ $stats['customers'] }}</td>
                                    <td class="px-5 py-3 text-right text-green-600">{{ $business->currencySymbol() }}{{ number_format($stats['collected'], 0) }}</td>
                                    <td class="px-5 py-3 text-right {{ $stats['outstanding'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $business->currencySymbol() }}{{ number_format($stats['outstanding'], 0) }}</td>
                                    <td class="px-5 py-3 text-right font-medium {{ $stats['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $business->currencySymbol() }}{{ number_format($stats['profit'], 0) }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <form method="POST" action="{{ route('businesses.switch', $business) }}">
                                            @csrf
                                            <button class="text-xs px-3 py-1.5 rounded-md border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">{{ __('Enter →') }}</button>
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

    @if ($isCompanyOwner)
        <x-modal name="add-builder" max-width="md">
            <form method="POST" action="{{ route('builders.store', $branch) }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Builder') }} — {{ $branch->name }}</h2>
                <div>
                    <x-input-label for="name" :value="__('Builder name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. Shivani Enterprises') }}" required autofocus />
                </div>
                <div>
                    <x-input-label for="business_type" :value="__('Business type')" />
                    <select id="business_type" name="business_type" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('Select one') }}</option>
                        @foreach (config('business.types') as $value => $label)
                            <option value="{{ $value }}" @selected($value === 'real_estate')>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="country" :value="__('Country code')" />
                        <x-text-input id="country" name="country" type="text" maxlength="2" value="IN" class="mt-1 block w-full uppercase" required />
                    </div>
                    <div>
                        <x-input-label for="currency" :value="__('Currency')" />
                        <select id="currency" name="currency" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            @foreach (config('business.currencies') as $value => $label)
                                <option value="{{ $value }}" @selected($value === 'INR')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="timezone" :value="__('Timezone')" />
                        <select id="timezone" name="timezone" required class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            @foreach (\DateTimeZone::listIdentifiers() as $tz)
                                <option value="{{ $tz }}" @selected($tz === 'Asia/Kolkata')>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('+ Add Builder') }}</x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="edit-branch" max-width="md">
            <form method="POST" action="{{ route('branches.update', $branch) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Branch') }}</h2>
                <div>
                    <x-input-label for="edit_name" :value="__('Branch name')" />
                    <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full" :value="$branch->name" required />
                </div>
                <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Branch Manager') }}</p>
                    <p class="text-xs text-gray-400 mb-3">{{ __('Fill these in to change/assign the manager — existing builders in this branch will switch over to the new manager automatically. Leave blank to keep the current manager.') }}</p>
                    <div class="space-y-3">
                        <x-text-input name="manager_name" type="text" class="block w-full" placeholder="{{ __('Manager name') }}" />
                        <x-text-input name="manager_email" type="email" class="block w-full" placeholder="{{ __('Manager email') }}" />
                        <x-text-input name="manager_password" type="text" class="block w-full" placeholder="{{ __('Password (if new login)') }}" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-app-layout>
