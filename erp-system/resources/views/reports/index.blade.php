<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Reports Center') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[var(--muted)] mb-1">From</label>
                            <input type="date" name="from" value="{{ $from }}" class="rounded-md border-[var(--border)] shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[var(--muted)] mb-1">To</label>
                            <input type="date" name="to" value="{{ $to }}" class="rounded-md border-[var(--border)] shadow-sm text-sm">
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Apply
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sales / Purchase Summary -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Invoiced (Sales)</p>
                    <p class="text-xl font-bold mt-1">₹{{ number_format($invoiceTotal, 0) }}</p>
                </div>
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Received (Sales)</p>
                    <p class="text-xl font-bold mt-1 text-green-600">₹{{ number_format($paymentTotal, 0) }}</p>
                </div>
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Purchased (Raw Material)</p>
                    <p class="text-xl font-bold mt-1">₹{{ number_format($purchaseTotal, 0) }}</p>
                </div>
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Paid (Purchase)</p>
                    <p class="text-xl font-bold mt-1 text-green-600">₹{{ number_format($purchasePaid, 0) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Outstanding Receivables (Customers owe us)</p>
                    <p class="text-xl font-bold mt-1 text-red-600">₹{{ number_format($outstandingReceivables, 0) }}</p>
                </div>
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Outstanding Payables (We owe suppliers)</p>
                    <p class="text-xl font-bold mt-1 text-red-600">₹{{ number_format($outstandingPayables, 0) }}</p>
                </div>
            </div>

            <!-- Job Status in range -->
            <div class="bg-[var(--card)] rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-[var(--text)] mb-4">Job Status (selected range, by received date)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach (['pending' => 'Pending', 'working' => 'Working', 'ready' => 'Ready', 'dispatched' => 'Dispatched'] as $key => $label)
                        <div class="rounded-lg p-4 bg-[var(--bg)]">
                            <p class="text-xs text-[var(--muted)] uppercase tracking-wide font-semibold">{{ $label }}</p>
                            <p class="text-xl font-bold mt-1">{{ $jobCounts[$key] ?? 0 }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-[var(--card)] rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-[var(--text)] mb-4">Low Stock Materials</h3>
                @forelse ($lowStockMaterials as $material)
                    <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
                        <span>{{ $material->name }}</span>
                        <span class="text-red-600 font-semibold">{{ number_format($material->current_stock, 1) }} {{ $material->unit }} left</span>
                    </div>
                @empty
                    <p class="text-sm text-[var(--muted)]">Sab materials theek stock me hain.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
