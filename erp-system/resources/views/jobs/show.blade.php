<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">Job #{{ $job->id }} — {{ $job->customer->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="px-4 py-2 rounded bg-red-50 text-red-700 text-sm">{{ $errors->first() }}</div>
            @endif

            <!-- Summary -->
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <div class="flex justify-between items-start mb-4">
                        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                            <div><span class="text-[var(--muted)]">Challan:</span> {{ $job->challan_number ?? '—' }}</div>
                            <div><span class="text-[var(--muted)]">Vehicle:</span> {{ $job->vehicle_number ?? '—' }}</div>
                            <div><span class="text-[var(--muted)]">Received:</span> {{ $job->received_date->format('d M Y') }}</div>
                            <div class="{{ $job->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                <span class="text-[var(--muted)]">Due:</span> {{ $job->due_date?->format('d M Y') ?? '—' }}
                                @if ($job->isOverdue()) (Overdue) @endif
                            </div>
                        </div>
                        <div class="text-right space-y-1">
                            @php
                                $pill = match($job->status) {
                                    'pending' => 'bg-[var(--bg)] text-[var(--muted)]',
                                    'working' => 'bg-amber-100 text-amber-700',
                                    'ready' => 'bg-green-100 text-green-700',
                                    'dispatched' => 'bg-[var(--brand-100)] text-[var(--brand-700)]',
                                };
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }}">{{ ucfirst($job->status) }}</span><br>
                            @if ($job->qc_status !== 'pending')
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $job->qc_status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    QC {{ ucfirst($job->qc_status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($job->photo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($job->photo_path) }}" class="h-32 rounded border mb-4">
                    @endif

                    <table class="min-w-full text-sm border rounded-lg">
                        <thead class="bg-[var(--bg)]">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Product</th>
                                <th class="px-3 py-2 text-right font-semibold text-[var(--muted)]">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border)]">
                            @foreach ($job->items as $item)
                                <tr>
                                    <td class="px-3 py-2">{{ $item->product->name }}</td>
                                    <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($job->notes)
                        <p class="text-sm text-[var(--muted)] mt-3">{{ $job->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Production Stages -->
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <h3 class="font-semibold text-[var(--text)] mb-4">Production Stages</h3>

                    <ol class="space-y-2 mb-6">
                        @foreach ($stages as $stage)
                            @php $log = $job->stageLogs->firstWhere('stage', $stage); @endphp
                            <li class="flex items-center justify-between text-sm border rounded-lg px-3 py-2 {{ $log ? 'bg-green-50 border-green-200' : ($stage === $currentStage ? 'bg-amber-50 border-amber-200' : 'bg-[var(--bg)] border-[var(--border)]') }}">
                                <span class="font-medium">{{ $stage }}</span>
                                @if ($log)
                                    <span class="text-xs text-[var(--muted)]">
                                        Done {{ $log->completed_at->format('d M, H:i') }}
                                        @if ($log->employee_count) · {{ $log->employee_count }} employees @endif
                                    </span>
                                @elseif ($stage === $currentStage)
                                    <span class="text-xs font-semibold text-amber-700">In Progress</span>
                                @else
                                    <span class="text-xs text-[var(--muted)]">Pending</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                    @if ($currentStage)
                        <form method="POST" action="{{ route('jobs.advance-stage', $job) }}"
                              x-data="{ isQc: {{ $currentStage === 'Quality Check' ? 'true' : 'false' }} }"
                              class="border-t pt-4">
                            @csrf
                            <p class="text-sm font-semibold text-[var(--muted)] mb-2">Mark "{{ $currentStage }}" Complete</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-[var(--muted)] mb-1">Employees Involved</label>
                                    <input type="number" name="employee_count" min="1" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[var(--muted)] mb-1">Remarks</label>
                                    <input type="text" name="remarks" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                </div>
                            </div>

                            <template x-if="isQc">
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-[var(--muted)] mb-1">QC Decision *</label>
                                    <input type="hidden" name="stage_is_qc" value="1">
                                    <div class="flex gap-4 text-sm">
                                        <label class="flex items-center gap-1"><input type="radio" name="qc_decision" value="approved" required> Approved</label>
                                        <label class="flex items-center gap-1"><input type="radio" name="qc_decision" value="rejected" required> Rejected (Rework)</label>
                                    </div>
                                </div>
                            </template>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                Submit
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-green-700 font-medium border-t pt-4">Production complete — Ready to Dispatch.</p>
                    @endif
                </div>
            </div>

            <!-- Material Issue -->
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">
                    <h3 class="font-semibold text-[var(--text)] mb-4">Store — Material Issue / Return</h3>

                    <form method="POST" action="{{ route('jobs.issue-material', $job) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-6 items-end">
                        @csrf
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-[var(--muted)] mb-1">Material</label>
                            <select name="raw_material_id" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                @foreach ($rawMaterials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }} ({{ number_format($material->current_stock, 1) }} {{ $material->unit }} left)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[var(--muted)] mb-1">Quantity</label>
                            <input type="number" step="0.01" name="quantity" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[var(--muted)] mb-1">Type</label>
                            <select name="type" class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                <option value="issue">Issue (out)</option>
                                <option value="return">Return (in)</option>
                            </select>
                        </div>
                        <div class="sm:col-span-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                Save Entry
                            </button>
                        </div>
                    </form>

                    <table class="min-w-full text-sm border rounded-lg">
                        <thead class="bg-[var(--bg)]">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Material</th>
                                <th class="px-3 py-2 text-right font-semibold text-[var(--muted)]">Qty</th>
                                <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Type</th>
                                <th class="px-3 py-2 text-left font-semibold text-[var(--muted)]">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border)]">
                            @forelse ($job->materialIssues as $mi)
                                <tr>
                                    <td class="px-3 py-2">{{ $mi->rawMaterial->name }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($mi->quantity, 2) }} {{ $mi->rawMaterial->unit }}</td>
                                    <td class="px-3 py-2">
                                        <span class="{{ $mi->type === 'issue' ? 'text-red-600' : 'text-green-600' }}">{{ ucfirst($mi->type) }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-[var(--muted)]">{{ $mi->issued_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-4 text-center text-[var(--muted)]">Abhi tak koi material issue nahi hua.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dispatch: Invoice + Security Gate -->
            @if (in_array($job->status, ['ready', 'dispatched']))
                <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-[var(--text)]">
                        <h3 class="font-semibold text-[var(--text)] mb-4">Dispatch</h3>

                        @if (! $job->invoice)
                            @can('module.invoice-accounts')
                                <a href="{{ route('invoices.create', $job) }}"
                                   class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                    Generate Invoice
                                </a>
                            @else
                                <p class="text-sm text-[var(--muted)]">Account role invoice banayega, tabhi dispatch aage badhega.</p>
                            @endcan
                        @else
                            <div class="flex items-center justify-between border rounded-lg px-4 py-3 bg-green-50 border-green-200 mb-4">
                                <span class="text-sm">Invoice <strong>{{ $job->invoice->invoice_number }}</strong> ban chuki hai — ₹{{ number_format($job->invoice->total, 2) }}</span>
                                <a href="{{ route('invoices.show', $job->invoice) }}" class="text-sm text-[var(--brand-600)] hover:underline">View Invoice</a>
                            </div>

                            @if ($job->securityGateLog)
                                <div class="border rounded-lg px-4 py-3 bg-[var(--brand-50)] border-[var(--brand-200)] text-sm">
                                    <strong>Gate Verified</strong> — {{ $job->securityGateLog->verified_by }} ne
                                    {{ $job->securityGateLog->verified_at->format('d M Y, H:i') }} pe confirm kiya
                                    (Vehicle: {{ $job->securityGateLog->vehicle_number_confirmed }})
                                </div>
                            @else
                                @can('module.security-gate')
                                    <form method="POST" action="{{ route('jobs.verify-gate', $job) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-medium text-[var(--muted)] mb-1">Verified By (Guard Name)</label>
                                            <input type="text" name="verified_by" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-[var(--muted)] mb-1">Vehicle Number Confirm</label>
                                            <input type="text" name="vehicle_number_confirmed" value="{{ $job->invoice->vehicle_number }}" required class="w-full rounded-md border-[var(--border)] shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                                Verify &amp; Dispatch
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <p class="text-sm text-[var(--muted)]">Security Gate se verify hone ka wait hai.</p>
                                @endcan
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <a href="{{ route('jobs.index') }}" class="text-sm text-[var(--muted)] hover:underline">&larr; Back to Job Work</a>
        </div>
    </div>
</x-app-layout>
