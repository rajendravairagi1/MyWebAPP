<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Material') }} — {{ $material->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('raw-materials.update', $material) }}">
                        @csrf
                        @method('PUT')
                        @include('raw-materials._form')

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Update Material
                        </button>
                        <a href="{{ route('raw-materials.index') }}" class="ml-3 text-sm text-gray-500 hover:underline">Cancel</a>
                    </form>

                    <div class="mt-6 border-t pt-4">
                        <form method="POST" action="{{ route('raw-materials.destroy', $material) }}"
                              onsubmit="return confirm('Ye material delete karna hai?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete Material</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
