<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Employees') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-[var(--muted)]">Total {{ $employees->total() }} employees.</p>
                        <a href="{{ route('employees.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            + Naya Employee
                        </a>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-[var(--border)] text-sm">
                            <thead class="bg-[var(--bg)]">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Code</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Department</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Mobile</th>
                                    <th class="px-4 py-2 text-right font-semibold text-[var(--muted)]">Per-day Salary</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                @forelse ($employees as $employee)
                                    <tr>
                                        <td class="px-4 py-2 font-mono text-xs">{{ $employee->employee_code }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $employee->name }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $employee->department ?? '—' }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $employee->mobile ?? '—' }}</td>
                                        <td class="px-4 py-2 text-right">₹{{ number_format($employee->per_day_salary, 2) }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $employee->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-[var(--bg)] text-[var(--muted)]' }}">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('employees.edit', $employee) }}" class="text-[var(--brand-600)] hover:underline">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-6 text-center text-[var(--muted)]">Koi employee nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $employees->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
