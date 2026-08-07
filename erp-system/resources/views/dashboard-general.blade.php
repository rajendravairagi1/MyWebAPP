<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Aaj Naye Jobs</p>
                    <p class="text-2xl font-bold mt-1">{{ $todayReceived }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pending/Working Jobs</p>
                    <p class="text-2xl font-bold mt-1">{{ $myPendingJobs }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Welcome, {{ Auth::user()->name }} — upar menu se apne modules access kar sakte ho.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
