<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Follow-ups') }}</h2>
            <a href="{{ route('followups.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900">{{ __('+ Schedule Follow-up') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            @if ($overdue->isNotEmpty())
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 font-medium text-red-700">{{ __('Overdue') }} ({{ $overdue->count() }})</div>
                    <ul class="divide-y divide-gray-100">
                        @foreach ($overdue as $followup)
                            @include('followups._row', ['followup' => $followup])
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 font-medium text-gray-800">{{ __('Upcoming') }} ({{ $upcoming->count() }})</div>
                @if ($upcoming->isEmpty())
                    <div class="p-5 text-sm text-gray-500">{{ __('Nothing scheduled.') }}</div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($upcoming as $followup)
                            @include('followups._row', ['followup' => $followup])
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
