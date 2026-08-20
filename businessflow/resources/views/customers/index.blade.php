<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Customers') }}</h2>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-900">{{ __('+ Add Customer') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <form method="GET" class="max-w-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search customers...') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if ($customers->isEmpty())
                    <div class="p-6 text-sm text-gray-500">{{ __('No customers yet.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Company') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Phone') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Email') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($customers as $customer)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:underline font-medium">{{ $customer->name }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $customer->company }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $customer->phone }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $customer->email }}</td>
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
