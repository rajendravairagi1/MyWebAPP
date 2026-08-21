<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Project') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
                    @csrf
                    @include('projects._form')

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('projects.index') }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Save Project') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
