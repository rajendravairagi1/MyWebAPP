<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Deleted Customers') }}</h2>
            <a href="{{ route('customers.index') }}" class="text-sm text-accent-600 hover:underline">{{ __('← Back to Customers') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Their sale history stays visible in the Ledger and on any quotations/invoices even while deleted. Restore to bring them back into Customers.') }}</p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($customers->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No deleted customers.') }}</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left">{{ __('Deleted on') }}</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($customers as $customer)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('customers.show', $customer) }}" class="text-accent-600 hover:underline font-medium">{{ $customer->name }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $customer->deleted_at->format('d M Y, h:i A') }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <form method="POST" action="{{ route('customers.restore', $customer->id) }}">
                                            @csrf
                                            <button class="text-xs text-accent-600 hover:underline">{{ __('Restore') }}</button>
                                        </form>
                                    </td>
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
