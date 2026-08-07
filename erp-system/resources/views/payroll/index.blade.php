<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payroll') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex items-center justify-between mb-6">
                        <form method="GET" action="{{ route('payroll.index') }}" class="flex items-center gap-2">
                            <select name="month" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                                @endforeach
                            </select>
                            <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm">
                                @foreach (range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </form>

                        <form method="POST" action="{{ route('payroll.generate') }}">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                Generate Salary Slips
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Employee</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Present Days</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Gross</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">Net Salary</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($employees as $employee)
                                    @php $slip = $slips[$employee->id] ?? null; @endphp
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $employee->name }}</td>
                                        <td class="px-4 py-2 text-right">{{ $slip?->present_days ?? '—' }}</td>
                                        <td class="px-4 py-2 text-right">{{ $slip ? '₹'.number_format($slip->gross_salary, 2) : '—' }}</td>
                                        <td class="px-4 py-2 text-right font-semibold">{{ $slip ? '₹'.number_format($slip->net_salary, 2) : '—' }}</td>
                                        <td class="px-4 py-2">
                                            @if ($slip)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $slip->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($slip->status) }}</span>
                                            @else
                                                <span class="text-gray-400 text-xs">Not generated</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            @if ($slip)
                                                <a href="{{ route('payroll.show', $slip) }}" class="text-[var(--brand-600)] hover:underline">View Slip</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Koi active employee nahi hai.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
