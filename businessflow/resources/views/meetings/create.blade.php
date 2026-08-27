<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Schedule Meeting') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('meetings.store') }}" class="space-y-6">
                    @csrf
                    @include('meetings._form', ['meeting' => null])
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('meetings.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Schedule') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
