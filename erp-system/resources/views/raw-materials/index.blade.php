<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Raw Material Inventory') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-600">Total {{ $materials->count() }} materials.</p>
                        <a href="{{ route('raw-materials.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            + Naya Material
                        </a>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Category</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Current Stock</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($materials as $material)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $material->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $material->category }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($material->current_stock, 2) }} {{ $material->unit }}</td>
                                        <td class="px-4 py-2">
                                            @if ($material->isLowStock())
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Low Stock</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">OK</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('raw-materials.edit', $material) }}" class="text-[var(--brand-600)] hover:underline">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Koi material nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
