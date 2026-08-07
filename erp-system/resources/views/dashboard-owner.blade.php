<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Owner Dashboard') }}</h2>
    </x-slot>

    @php
        $statusMeta = [
            'pending' => ['Pending', '#94a3b8'],
            'working' => ['Working', '#d97706'],
            'ready' => ['Ready', '#16a34a'],
            'dispatched' => ['Dispatched', 'var(--brand)'],
        ];
        $donutTotal = array_sum(array_map(fn ($k) => $jobCounts[$k] ?? 0, array_keys($statusMeta)));
        $radius = 60;
        $circumference = 2 * M_PI * $radius;
        $offset = 0;
        $donutSegments = [];
        foreach ($statusMeta as $key => [$label, $color]) {
            $count = $jobCounts[$key] ?? 0;
            $fraction = $donutTotal > 0 ? $count / $donutTotal : 0;
            $length = $fraction * $circumference;
            $donutSegments[] = compact('key', 'label', 'color', 'count', 'fraction', 'length', 'offset');
            $offset += $length;
        }
        $maxBusiness = $topCustomers->max('total_business') ?: 1;
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Financial Stat Tiles -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5 border border-[var(--border)]">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Total Invoiced</p>
                    <p class="text-2xl font-bold mt-1 text-[var(--text)]">₹{{ number_format($totalInvoiced, 0) }}</p>
                </div>
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5 border border-[var(--border)]">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Payment Received</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--success)">₹{{ number_format($totalReceived, 0) }}</p>
                </div>
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5 border border-[var(--border)]">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Payment Pending</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--danger)">₹{{ number_format($totalPending, 0) }}</p>
                </div>
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-5 border border-[var(--border)]">
                    <p class="text-xs text-[var(--muted)] uppercase tracking-wide">Total Customers</p>
                    <p class="text-2xl font-bold mt-1 text-[var(--text)]">{{ $totalCustomers }}</p>
                </div>
            </div>

            <!-- Job Status Breakdown -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach (['pending' => ['Pending', 'text-[var(--muted)]', 'bg-[var(--bg)]'], 'working' => ['Working', 'text-amber-700', 'bg-amber-100'], 'ready' => ['Ready', 'text-green-700', 'bg-green-100'], 'dispatched' => ['Dispatched', 'text-[var(--brand-700)]', 'bg-[var(--brand-100)]']] as $key => [$label, $textClass, $bgClass])
                    <a href="{{ route('jobs.index', ['status' => $key]) }}" class="rounded-lg p-4 {{ $bgClass }} hover:opacity-80">
                        <p class="text-xs {{ $textClass }} uppercase tracking-wide font-semibold">{{ $label }} Jobs</p>
                        <p class="text-xl font-bold mt-1 {{ $textClass }}">{{ $jobCounts[$key] ?? 0 }}</p>
                    </a>
                @endforeach
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Job Status Donut Chart -->
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-6 border border-[var(--border)]">
                    <h3 class="font-semibold text-[var(--text)] mb-4">Job Status Distribution</h3>
                    @if ($donutTotal > 0)
                        <div class="flex items-center gap-6">
                            <svg width="160" height="160" viewBox="0 0 160 160" class="shrink-0 -rotate-90">
                                <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="var(--border)" stroke-width="20" />
                                @foreach ($donutSegments as $seg)
                                    @if ($seg['count'] > 0)
                                        <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="{{ $seg['color'] }}"
                                                stroke-width="20"
                                                stroke-dasharray="{{ $seg['length'] }} {{ $circumference - $seg['length'] }}"
                                                stroke-dashoffset="{{ -$seg['offset'] }}"
                                                stroke-linecap="butt" />
                                    @endif
                                @endforeach
                                <text x="80" y="80" transform="rotate(90 80 80)" text-anchor="middle" dominant-baseline="middle"
                                      class="fill-[var(--text)]" style="font-size: 26px; font-weight: 700;">{{ $donutTotal }}</text>
                            </svg>
                            <div class="space-y-2 text-sm">
                                @foreach ($donutSegments as $seg)
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background: {{ $seg['color'] }}"></span>
                                        <span class="text-[var(--muted)]">{{ $seg['label'] }}</span>
                                        <span class="font-semibold text-[var(--text)]">{{ $seg['count'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-[var(--muted)]">Abhi koi job nahi hai.</p>
                    @endif
                </div>

                <!-- Top Customers Bar Chart -->
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-6 border border-[var(--border)]">
                    <h3 class="font-semibold text-[var(--text)] mb-4">Top Customers (By Business)</h3>
                    @forelse ($topCustomers as $customer)
                        <div class="mb-3 last:mb-0">
                            <div class="flex justify-between items-center text-sm mb-1">
                                <span class="text-[var(--text)] truncate">{{ $customer->name }}</span>
                                <span class="font-semibold text-[var(--text)] shrink-0 ml-2">₹{{ number_format($customer->total_business, 0) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-[var(--bg)] overflow-hidden">
                                <div class="h-full rounded-full bg-[image:var(--brand-grad)]" style="width: {{ max(4, round($customer->total_business / $maxBusiness * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--muted)]">Abhi koi invoice nahi bani.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Low Stock Alert -->
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-6 border border-[var(--border)]">
                    <h3 class="font-semibold text-[var(--text)] mb-4">Low Stock Alert</h3>
                    @forelse ($lowStockMaterials as $material)
                        <div class="flex justify-between items-center py-2 border-b border-[var(--border)] last:border-0 text-sm">
                            <span class="text-[var(--text)]">{{ $material->name }}</span>
                            <span class="font-semibold" style="color: var(--danger)">{{ number_format($material->current_stock, 1) }} {{ $material->unit }} left</span>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--muted)]">Sab materials theek stock me hain.</p>
                    @endforelse
                    <a href="{{ route('raw-materials.index') }}" class="text-xs text-[var(--brand-600)] hover:underline mt-3 inline-block">View all &rarr;</a>
                </div>

                <!-- Overdue Jobs -->
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-6 border border-[var(--border)]">
                    <h3 class="font-semibold text-[var(--text)] mb-4">Overdue Jobs</h3>
                    @forelse ($overdueJobs as $job)
                        <a href="{{ route('jobs.show', $job) }}" class="flex justify-between items-center py-2 border-b border-[var(--border)] last:border-0 text-sm hover:bg-[var(--bg)]">
                            <span class="text-[var(--text)]">#{{ $job->id }} — {{ $job->customer->name }}</span>
                            <span class="font-semibold" style="color: var(--danger)">Due {{ $job->due_date->format('d M') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-[var(--muted)]">Koi overdue job nahi hai.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Overdue Invoices -->
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-6 border border-[var(--border)]">
                    <h3 class="font-semibold text-[var(--text)] mb-4">Overdue Payments</h3>
                    @forelse ($overdueInvoices as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" class="flex justify-between items-center py-2 border-b border-[var(--border)] last:border-0 text-sm hover:bg-[var(--bg)]">
                            <span class="text-[var(--text)]">{{ $invoice->invoice_number }} — {{ $invoice->customer->name }}</span>
                            <span class="font-semibold" style="color: var(--danger)">₹{{ number_format($invoice->balanceDue(), 0) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-[var(--muted)]">Koi overdue payment nahi hai.</p>
                    @endforelse
                </div>

                <!-- Recent Jobs -->
                <div class="bg-[var(--card)] rounded-lg shadow-sm p-6 border border-[var(--border)]">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-[var(--text)]">Recent Jobs</h3>
                        <a href="{{ route('jobs.index') }}" class="text-xs text-[var(--brand-600)] hover:underline">View all &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-[var(--muted)] text-xs uppercase">
                                    <th class="pb-2">Job</th>
                                    <th class="pb-2">Customer</th>
                                    <th class="pb-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                @foreach ($recentJobs as $job)
                                    <tr class="hover:bg-[var(--bg)] cursor-pointer" onclick="window.location='{{ route('jobs.show', $job) }}'">
                                        <td class="py-2 text-[var(--text)]">#{{ $job->id }}</td>
                                        <td class="py-2 text-[var(--text)]">{{ $job->customer->name }}</td>
                                        <td class="py-2">
                                            @php
                                                $pill = match($job->status) {
                                                    'pending' => 'bg-[var(--bg)] text-[var(--muted)]',
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
    </div>
</x-app-layout>
