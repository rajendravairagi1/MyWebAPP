<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Leads') }}</h2>
    </x-slot>

    @php
        $statusPill = [
            'new' => 'bg-gray-100 text-gray-600',
            'contacted' => 'bg-amber-100 text-amber-700',
            'qualified' => 'bg-[var(--brand-100)] text-[var(--brand-700)]',
            'converted' => 'bg-green-100 text-green-700',
            'lost' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <div class="flex gap-1 text-sm flex-wrap">
                            <a href="{{ route('leads.index') }}" class="px-3 py-1.5 rounded-md {{ ! $status ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-gray-500 hover:bg-gray-50' }}">All</a>
                            @foreach (['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'converted' => 'Converted', 'lost' => 'Lost'] as $key => $label)
                                <a href="{{ route('leads.index', ['status' => $key]) }}" class="px-3 py-1.5 rounded-md {{ $status === $key ? 'bg-[var(--brand-100)] text-[var(--brand-700)]' : 'text-gray-500 hover:bg-gray-50' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                        <a href="{{ route('leads.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            + Naya Lead
                        </a>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Company</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Mobile</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Assigned To</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($leads as $lead)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $lead->name }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $lead->company_name ?? '—' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $lead->mobile ?? '—' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $lead->assignedUser?->name ?? '—' }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusPill[$lead->status] }}">{{ ucfirst($lead->status) }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('leads.show', $lead) }}" class="text-[var(--brand-600)] hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Koi lead nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $leads->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
