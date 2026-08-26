<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Edit Quotation') }} {{ $quotation->number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->has('delete'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first('delete') }}</div>
            @endif
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('quotations.update', $quotation) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('quotations._form')

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('quotations.show', $quotation) }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Deleting a quotation cannot be undone.') }}</div>
                <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" onsubmit="return confirm('{{ __('Delete this quotation?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Delete quotation') }}</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
