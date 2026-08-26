<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Set up your Company') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    {{ __('Use this if you run multiple builder firms across branches — you\'ll be able to add branches, and multiple builders under each branch, each with its own team and data.') }}
                </p>

                <form method="POST" action="{{ route('company.store') }}" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Company name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" placeholder="{{ __('e.g. Shivani Group') }}" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Create Company') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
