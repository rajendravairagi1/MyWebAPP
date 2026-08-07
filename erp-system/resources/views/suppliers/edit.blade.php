<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Supplier Edit') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                        @csrf
                        @method('PUT')
                        @include('suppliers._form')

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Update Supplier
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="ml-3 text-sm text-gray-500 hover:underline">Cancel</a>
                    </form>

                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="mt-6 pt-6 border-t"
                          onsubmit="return confirm('Supplier delete karna hai?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:underline">Delete Supplier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
