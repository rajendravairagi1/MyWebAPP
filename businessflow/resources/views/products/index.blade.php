<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Products & Services') }}</h2>
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">{{ __('+ Add Product') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($products->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No products or services yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Type') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('SKU') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Price') }}</th>
                                <th class="px-5 py-3 text-right">{{ __('Stock') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 capitalize">{{ $product->type }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $product->sku }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($product->price, 2) }}</td>
                                    <td class="px-5 py-3 text-right">
                                        @if ($product->stock_qty !== null)
                                            <span class="{{ $product->isLowStock() ? 'text-red-600 font-medium' : 'text-gray-600 dark:text-gray-400' }}">{{ $product->stock_qty }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('products.edit', $product) }}" class="text-accent-600 hover:underline">{{ __('Edit') }}</a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('{{ __('Delete this product?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline ml-2">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
