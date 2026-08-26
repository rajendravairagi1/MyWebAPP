<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Edit Customer') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->has('delete'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first('delete') }}</div>
            @endif
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('customers._form')

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('customers.show', $customer) }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Deleting a customer cannot be undone.') }}</div>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('{{ __('Delete this customer?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Delete customer') }}</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
