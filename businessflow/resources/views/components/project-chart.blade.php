@props(['projects'])

@php
    $labels = $projects->pluck('name');
    $costs = $projects->map(fn ($p) => round($p->totalCost(), 2));
    $revenues = $projects->map(fn ($p) => round($p->totalRevenue(), 2));
@endphp

<div
    x-data="projectChart(@js($labels), @js($costs), @js($revenues))"
    x-init="init($el.querySelector('canvas'))"
    @theme-changed.window="applyTheme($event.detail.dark)"
    class="relative" style="height: 280px;">
    <canvas></canvas>
</div>

@once
    @push('scripts')
        <script>
            function projectChart(labels, costs, revenues) {
                return {
                    chart: null,
                    init(canvas) {
                        const dark = document.documentElement.classList.contains('dark');
                        this.chart = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [
                                    { label: 'Cost', data: costs, backgroundColor: '#f59e0b', borderRadius: 4, maxBarThickness: 28 },
                                    { label: 'Received', data: revenues, backgroundColor: '#6366f1', borderRadius: 4, maxBarThickness: 28 },
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: dark ? '#94a3b8' : '#6b7280' } },
                                    y: { grid: { color: dark ? '#334155' : '#e5e7eb' }, ticks: { color: dark ? '#94a3b8' : '#6b7280' }, beginAtZero: true },
                                },
                                plugins: {
                                    legend: { labels: { color: dark ? '#cbd5e1' : '#374151' } },
                                },
                            },
                        });
                    },
                    applyTheme(dark) {
                        if (!this.chart) return;
                        this.chart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#6b7280';
                        this.chart.options.scales.y.ticks.color = dark ? '#94a3b8' : '#6b7280';
                        this.chart.options.scales.y.grid.color = dark ? '#334155' : '#e5e7eb';
                        this.chart.options.plugins.legend.labels.color = dark ? '#cbd5e1' : '#374151';
                        this.chart.update();
                    },
                };
            }
        </script>
    @endpush
@endonce
