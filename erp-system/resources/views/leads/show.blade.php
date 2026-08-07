<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $lead->name }}</h2>
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
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="font-semibold text-lg">{{ $lead->name }}</p>
                            <p class="text-sm text-gray-500">{{ $lead->company_name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusPill[$lead->status] }}">{{ ucfirst($lead->status) }}</span>
                            <p class="text-xs text-gray-500 mt-1">Assigned: {{ $lead->assignedUser?->name ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm border-t pt-4">
                        <p><span class="text-gray-500">Mobile:</span> {{ $lead->mobile ?? '—' }}</p>
                        <p><span class="text-gray-500">Email:</span> {{ $lead->email ?? '—' }}</p>
                        <p><span class="text-gray-500">Source:</span> {{ $lead->source ?? '—' }}</p>
                    </div>
                    @if ($lead->notes)
                        <p class="text-sm text-gray-600 border-t pt-3 mt-3">{{ $lead->notes }}</p>
                    @endif

                    <a href="{{ route('leads.edit', $lead) }}" class="inline-block mt-4 text-sm text-[var(--brand-600)] hover:underline">Edit Lead</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-gray-800 mb-4">Add Meeting / Follow-up Note</h3>
                    <form method="POST" action="{{ route('leads.meetings.store', $lead) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Meeting Date & Time *</label>
                                <input type="datetime-local" name="meeting_date" required value="{{ old('meeting_date', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Next Follow-up Date</label>
                                <input type="date" name="next_follow_up_date" value="{{ old('next_follow_up_date') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('notes') }}</textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Save Meeting
                        </button>
                    </form>
                </div>
            </div>

            @if ($lead->meetings->count())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-gray-800 mb-3">Meeting History</h3>
                        <div class="space-y-3">
                            @foreach ($lead->meetings as $meeting)
                                <div class="border-b last:border-0 pb-3 last:pb-0 text-sm">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ $meeting->meeting_date->format('d M Y, h:i A') }}</span>
                                        @if ($meeting->next_follow_up_date)
                                            <span class="text-xs text-amber-700">Next: {{ $meeting->next_follow_up_date->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                    @if ($meeting->notes)
                                        <p class="text-gray-500 mt-1">{{ $meeting->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ route('leads.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Back to Leads</a>
        </div>
    </div>
</x-app-layout>
