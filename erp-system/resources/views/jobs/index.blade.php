<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--text)] leading-tight">{{ __('Job Work') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--card)] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-[var(--text)]">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <div class="flex gap-2">
                            <a href="{{ route('jobs.index') }}"
                               class="px-3 py-1.5 rounded text-xs font-semibold {{ ! $status ? 'bg-[var(--text)] text-[var(--card)]' : 'bg-[var(--bg)] text-[var(--muted)]' }}">All</a>
                            @foreach (['pending' => 'Pending', 'working' => 'Working', 'ready' => 'Ready', 'dispatched' => 'Dispatched'] as $key => $label)
                                <a href="{{ route('jobs.index', ['status' => $key]) }}"
                                   class="px-3 py-1.5 rounded text-xs font-semibold {{ $status === $key ? 'bg-[var(--text)] text-[var(--card)]' : 'bg-[var(--bg)] text-[var(--muted)]' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                        @can('module.job-work')
                            <a href="{{ route('jobs.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                + Naya Job
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-[var(--border)] text-sm">
                            <thead class="bg-[var(--bg)]">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Job ID</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Customer</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Challan</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Due Date</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Stage</th>
                                    <th class="px-4 py-2 text-left font-semibold text-[var(--muted)]">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                @forelse ($jobs as $job)
                                    <tr class="hover:bg-[var(--bg)] cursor-pointer" onclick="window.location='{{ route('jobs.show', $job) }}'">
                                        <td class="px-4 py-2 font-mono text-xs">#{{ $job->id }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $job->customer->name }}</td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $job->challan_number ?? '—' }}</td>
                                        <td class="px-4 py-2 {{ $job->isOverdue() ? 'text-red-600 font-semibold' : 'text-[var(--muted)]' }}">
                                            {{ $job->due_date?->format('d M Y') ?? '—' }}
                                            @if ($job->isOverdue()) <span class="text-xs">(Overdue)</span> @endif
                                        </td>
                                        <td class="px-4 py-2 text-[var(--muted)]">{{ $job->currentStage() ?? 'Complete' }}</td>
                                        <td class="px-4 py-2">
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
                                @empty
                                    <tr><td colspan="6" class="px-4 py-6 text-center text-[var(--muted)]">Koi job nahi hai abhi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $jobs->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
