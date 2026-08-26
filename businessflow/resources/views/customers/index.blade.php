<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Customers') }}</h2>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">{{ __('+ Add Customer') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <form method="GET" class="max-w-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search customers...') }}"
                    class="w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm text-sm focus:border-accent-500 focus:ring-accent-500">
            </form>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($customers->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No customers yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Company') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Phone') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Email') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($customers as $customer)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('customers.show', $customer) }}" class="text-accent-600 hover:underline font-medium">{{ $customer->name }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $customer->company }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $customer->phone }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $customer->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{ $customers->links() }}
        </div>
    </div>
</x-app-layout>
