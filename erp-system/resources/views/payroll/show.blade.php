<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">Salary Slip — {{ $slip->employee->name }} ({{ $slip->monthLabel() }})</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Employee</p>
                            <p class="font-semibold text-lg">{{ $slip->employee->name }}</p>
                            <p class="text-sm text-[var(--muted)]">{{ $slip->employee->employee_code }} · {{ $slip->employee->designation }}</p>
                        </div>
                        <div class="text-right text-sm">
                            <p><span class="text-[var(--muted)]">Month:</span> {{ $slip->monthLabel() }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $slip->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($slip->status) }}</span>
                        </div>
                    </div>

                    <div class="text-sm space-y-1 border rounded-lg p-4">
                        <div class="flex justify-between"><span class="text-[var(--muted)]">Present Days</span><span>{{ $slip->present_days }}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--muted)]">Per Day Salary</span><span>₹{{ number_format($slip->per_day_salary, 2) }}</span></div>
                        <div class="flex justify-between font-semibold"><span>Gross Salary</span><span>₹{{ number_format($slip->gross_salary, 2) }}</span></div>
                        <div class="flex justify-between text-red-600"><span>Advance Deduction</span><span>- ₹{{ number_format($slip->advance_deduction, 2) }}</span></div>
                        <div class="flex justify-between text-red-600"><span>Other Deduction</span><span>- ₹{{ number_format($slip->other_deduction, 2) }}</span></div>
                        <div class="flex justify-between font-bold text-base border-t pt-2 mt-1"><span>Net Salary</span><span>₹{{ number_format($slip->net_salary, 2) }}</span></div>
                        @if ($slip->paid_at)
                            <div class="flex justify-between text-[var(--muted)] text-xs pt-1"><span>Paid On</span><span>{{ $slip->paid_at->format('d M Y') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($slip->status !== 'paid')
                <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-[var(--text)]">
                        <h3 class="font-semibold text-[var(--text)] mb-4">Adjust Deductions</h3>
                        <form method="POST" action="{{ route('payroll.update', $slip) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-[var(--muted)] mb-1">Advance Deduction (₹)</label>
                                <input type="number" step="0.01" name="advance_deduction" value="{{ $slip->advance_deduction }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[var(--muted)] mb-1">Other Deduction (₹)</label>
                                <input type="number" step="0.01" name="other_deduction" value="{{ $slip->other_deduction }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                            </div>
                            <div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                    Update
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('payroll.mark-paid', $slip) }}" class="mt-4 pt-4 border-t">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Mark as Paid
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <a href="{{ route('payroll.index', ['month' => $slip->month, 'year' => $slip->year]) }}" class="text-sm text-[var(--muted)] hover:underline">&larr; Back to Payroll</a>
        </div>
    </div>
</x-app-layout>
