<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Meetings') }}</h2>
            <a href="{{ route('meetings.create') }}" class="inline-flex items-center px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">{{ __('+ Schedule Meeting') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            @if ($upcoming->isEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No meetings scheduled — add your first one above.') }}
                </div>
            @else
                @foreach ($upcoming as $date => $meetings)
                    @php $isToday = \Illuminate\Support\Carbon::parse($date)->isToday(); @endphp
                    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium {{ $isToday ? 'text-accent-600' : 'text-gray-800 dark:text-gray-100' }}">
                            {{ $isToday ? __('Today') : \Illuminate\Support\Carbon::parse($date)->format('l, d M Y') }}
                            <span class="text-xs font-normal text-gray-400">({{ $meetings->count() }})</span>
                        </div>
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($meetings as $meeting)
                                @include('meetings._row', ['meeting' => $meeting])
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif

            @if ($past->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 font-medium text-gray-800 dark:text-gray-100">{{ __('Past') }}</div>
                    <ul class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($past as $meeting)
                            @include('meetings._row', ['meeting' => $meeting, 'showStatus' => true])
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
