<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Naya Customer') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                        @csrf
                        @include('customers._form')

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Save Customer
                        </button>
                        <a href="{{ route('customers.index') }}" class="ml-3 text-sm text-gray-500 hover:underline">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
