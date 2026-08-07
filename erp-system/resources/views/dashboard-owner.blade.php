<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Owner Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Financial Stat Tiles -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Invoiced</p>
                    <p class="text-2xl font-bold mt-1">₹{{ number_format($totalInvoiced, 0) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Payment Received</p>
                    <p class="text-2xl font-bold mt-1 text-green-600">₹{{ number_format($totalReceived, 0) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Payment Pending</p>
                    <p class="text-2xl font-bold mt-1 text-red-600">₹{{ number_format($totalPending, 0) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Customers</p>
                    <p class="text-2xl font-bold mt-1">{{ $totalCustomers }}</p>
                </div>
            </div>

            <!-- Job Status Breakdown -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach (['pending' => ['Pending', 'text-gray-600', 'bg-gray-100'], 'working' => ['Working', 'text-amber-700', 'bg-amber-100'], 'ready' => ['Ready', 'text-green-700', 'bg-green-100'], 'dispatched' => ['Dispatched', 'text-[var(--brand-700)]', 'bg-[var(--brand-100)]']] as $key => [$label, $textClass, $bgClass])
                    <a href="{{ route('jobs.index', ['status' => $key]) }}" class="rounded-lg p-4 {{ $bgClass }} hover:opacity-80">
                        <p class="text-xs {{ $textClass }} uppercase tracking-wide font-semibold">{{ $label }} Jobs</p>
                        <p class="text-xl font-bold mt-1 {{ $textClass }}">{{ $jobCounts[$key] ?? 0 }}</p>
                    </a>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Customers -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Top Customers (By Business)</h3>
                    @forelse ($topCustomers as $customer)
                        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
                            <span>{{ $customer->name }}</span>
                            <span class="font-semibold">₹{{ number_format($customer->total_business, 0) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Abhi koi invoice nahi bani.</p>
                    @endforelse
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Low Stock Alert</h3>
                    @forelse ($lowStockMaterials as $material)
                        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
                            <span>{{ $material->name }}</span>
                            <span class="text-red-600 font-semibold">{{ number_format($material->current_stock, 1) }} {{ $material->unit }} left</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Sab materials theek stock me hain.</p>
                    @endforelse
                    <a href="{{ route('raw-materials.index') }}" class="text-xs text-[var(--brand-600)] hover:underline mt-3 inline-block">View all &rarr;</a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Overdue Jobs -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Overdue Jobs</h3>
                    @forelse ($overdueJobs as $job)
                        <a href="{{ route('jobs.show', $job) }}" class="flex justify-between items-center py-2 border-b last:border-0 text-sm hover:bg-gray-50">
                            <span>#{{ $job->id }} — {{ $job->customer->name }}</span>
                            <span class="text-red-600 font-semibold">Due {{ $job->due_date->format('d M') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Koi overdue job nahi hai.</p>
                    @endforelse
                </div>

                <!-- Overdue Invoices -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Overdue Payments</h3>
                    @forelse ($overdueInvoices as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" class="flex justify-between items-center py-2 border-b last:border-0 text-sm hover:bg-gray-50">
                            <span>{{ $invoice->invoice_number }} — {{ $invoice->customer->name }}</span>
                            <span class="text-red-600 font-semibold">₹{{ number_format($invoice->balanceDue(), 0) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Koi overdue payment nahi hai.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Recent Jobs</h3>
                    <a href="{{ route('jobs.index') }}" class="text-xs text-[var(--brand-600)] hover:underline">View all &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 text-xs uppercase">
                                <th class="pb-2">Job</th>
                                <th class="pb-2">Customer</th>
                                <th class="pb-2">Received</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentJobs as $job)
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('jobs.show', $job) }}'">
                                    <td class="py-2">#{{ $job->id }}</td>
                                    <td class="py-2">{{ $job->customer->name }}</td>
                                    <td class="py-2 text-gray-500">{{ $job->received_date->format('d M Y') }}</td>
                                    <td class="py-2">
                                        @php
                                            $pill = match($job->status) {
                                                'pending' => 'bg-gray-100 text-gray-600',
                                                'working' => 'bg-amber-100 text-amber-700',
                                                'ready' => 'bg-green-100 text-green-700',
                                                'dispatched' => 'bg-[var(--brand-100)] text-[var(--brand-700)]',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }}">{{ ucfirst($job->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
