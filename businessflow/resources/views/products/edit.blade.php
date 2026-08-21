<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Edit Product / Service') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('products._form')

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Deleting cannot be undone.') }}</div>
                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('{{ __('Delete this item?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
