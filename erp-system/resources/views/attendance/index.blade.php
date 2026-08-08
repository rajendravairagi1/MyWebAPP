<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Attendance') }}</h2>
    </x-slot>

    @php
        $statuses = ['present' => 'Present', 'half_day' => 'Half Day', 'absent' => 'Absent', 'leave' => 'Leave'];
        $statusColor = ['present' => 'text-green-700', 'half_day' => 'text-amber-700', 'absent' => 'text-red-700', 'leave' => 'text-[var(--muted)]'];
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('attendance.index', ['date' => $date->copy()->subDay()->toDateString()]) }}"
                               class="px-2 py-1 rounded-md border border-[var(--border)] text-[var(--muted)] hover:bg-[var(--bg)]">&larr;</a>
                            <form method="GET" action="{{ route('attendance.index') }}">
                                <input type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()"
                                       class="rounded-md border-[var(--border)] shadow-sm text-sm">
                            </form>
                            <a href="{{ route('attendance.index', ['date' => $date->copy()->addDay()->toDateString()]) }}"
                               class="px-2 py-1 rounded-md border border-[var(--border)] text-[var(--muted)] hover:bg-[var(--bg)]">&rarr;</a>
                        </div>
                        <p class="text-sm text-[var(--muted)]">{{ $date->format('l, d M Y') }}</p>
                    </div>

                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date->toDateString() }}">

                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-[var(--border)] text-sm">
                                <thead class="bg-[var(--bg)]">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Employee</th>
                                        @foreach ($statuses as $key => $label)
                                            <th class="px-4 py-2 text-center font-semibold text-[var(--muted)]">{{ $label }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--border)]">
                                    @forelse ($employees as $employee)
                                        @php $current = $marked[$employee->id]->status ?? 'present'; @endphp
                                        <tr>
                                            <td class="px-4 py-2 font-medium">{{ $employee->name }}
                                                <span class="text-[var(--muted)] text-xs">{{ $employee->employee_code }}</span>
                                            </td>
                                            @foreach ($statuses as $key => $label)
                                                <td class="px-4 py-2 text-center">
                                                    <input type="radio" name="status[{{ $employee->id }}]" value="{{ $key }}"
                                                           @checked($current === $key)
                                                           class="{{ $statusColor[$key] }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-4 py-6 text-center text-[var(--muted)]">Koi active employee nahi hai.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($employees->isNotEmpty())
                            <button type="submit" class="mt-4 inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                Save Attendance
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
