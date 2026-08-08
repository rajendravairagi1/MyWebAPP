<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Naya Product') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    @if ($customers->isEmpty())
                        <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded p-3 mb-4">
                            Pehle ek Customer add karo, tabhi uske liye Product bana payenge.
                            <a href="{{ route('customers.create') }}" class="underline font-semibold">Customer add karo &rarr;</a>
                        </p>
                    @else
                        <form method="POST" action="{{ route('products.store') }}">
                            @csrf
                            @include('products._form')

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                Save Product
                            </button>
                            <a href="{{ route('products.index') }}" class="ml-3 text-sm text-[var(--muted)] hover:underline">Cancel</a>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
