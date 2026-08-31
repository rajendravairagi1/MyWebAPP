<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Property Deals') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-deal')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Add Deal') }}
            </button>
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

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Properties you buy from one party and sell on to another — not your own construction projects. Track what you paid, what you sold it for, and your margin on each deal.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-5">
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Deals') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $totals['count'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Open') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $totals['open'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Purchased (sold)') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['total_purchase'], 0) }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Sold for') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['total_sale'], 0) }}</div>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Total Profit') }}</div>
                        <div class="mt-1 text-xl font-semibold {{ $totals['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($totals['total_profit'], 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($deals->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No deals added yet.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-5 py-3 text-left">{{ __('Property') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Seller') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Purchase') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Buyer') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Sale') }}</th>
                                    <th class="px-5 py-3 text-right">{{ __('Profit') }}</th>
                                    <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($deals as $deal)
                                    @php $profit = $deal->profit(); @endphp
                                    <tr>
                                        <td class="px-5 py-3">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $deal->property_title }}</div>
                                            @if ($deal->address)
                                                <div class="text-xs text-gray-400">{{ $deal->address }}</div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $deal->seller_name ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ \App\Support\Tenant::currencySymbol() }}{{ number_format($deal->purchase_price, 0) }}</td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $deal->buyer_name ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right text-gray-900 dark:text-gray-100">{{ $deal->sale_price !== null ? \App\Support\Tenant::currencySymbol().number_format($deal->sale_price, 0) : '—' }}</td>
                                        <td class="px-5 py-3 text-right font-semibold {{ $profit === null ? 'text-gray-400' : ($profit >= 0 ? 'text-green-600' : 'text-red-600') }}">
                                            {{ $profit !== null ? \App\Support\Tenant::currencySymbol().number_format($profit, 0) : '—' }}
                                        </td>
                                        <td class="px-5 py-3">
                                            <span @class([
                                                'text-xs px-2 py-0.5 rounded font-medium',
                                                'bg-amber-100 text-amber-700' => $deal->status === 'open',
                                                'bg-green-100 text-green-700' => $deal->status === 'sold',
                                                'bg-gray-100 text-gray-500' => $deal->status === 'cancelled',
                                            ])>{{ $deal->statusLabel() }}</span>
                                        </td>
                                        <td class="px-5 py-3 text-right whitespace-nowrap">
                                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-deal-{{ $deal->id }}')" class="text-accent-600 hover:underline text-xs">{{ __('Edit') }}</button>
                                            <form method="POST" action="{{ route('property-deals.destroy', $deal) }}" onsubmit="return confirm('{{ __('Delete this deal?') }}')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline text-xs ml-2">{{ __('Delete') }}</button>
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

    <x-modal name="add-deal" max-width="md" :show="$errors->has('property_title')">
        <form method="POST" action="{{ route('property-deals.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="status" value="open">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Deal') }}</h2>
            @include('property-deals._fields', ['deal' => null])
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Add Deal') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    @foreach ($deals as $deal)
        <x-modal name="edit-deal-{{ $deal->id }}" max-width="md">
            <form method="POST" action="{{ route('property-deals.update', $deal) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Deal') }}</h2>
                @include('property-deals._fields', ['deal' => $deal])
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>
