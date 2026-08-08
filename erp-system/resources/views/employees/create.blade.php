<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Naya Employee') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <form method="POST" action="{{ route('employees.store') }}">
                        @csrf
                        @include('employees._form')

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Save Employee
                        </button>
                        <a href="{{ route('employees.index') }}" class="ml-3 text-sm text-[var(--muted)] hover:underline">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
