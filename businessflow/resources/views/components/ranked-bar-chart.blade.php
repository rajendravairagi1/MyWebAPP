@props(['labels', 'data', 'colors' => '#6366f1', 'label' => ''])

<div
    x-data="rankedBarChart(@js($labels), @js($data), @js($colors), @js($label))"
    x-init="init($el.querySelector('canvas'))"
    @theme-changed.window="applyTheme($event.detail.dark)"
    class="relative" style="height: 220px;">
    <canvas></canvas>
</div>

@once
    @push('scripts')
        <script>
            function rankedBarChart(labels, data, colors, label) {
                return {
                    chart: null,
                    init(canvas) {
                        const dark = document.documentElement.classList.contains('dark');
                        this.chart = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [{
                                    label,
                                    data,
                                    backgroundColor: colors,
                                    borderRadius: 4,
                                    maxBarThickness: 36,
                                }],
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { grid: { color: dark ? '#334155' : '#e5e7eb' }, ticks: { color: dark ? '#94a3b8' : '#6b7280' }, beginAtZero: true },
                                    y: { grid: { display: false }, ticks: { color: dark ? '#cbd5e1' : '#374151' } },
                                },
                            },
                        });
                    },
                    applyTheme(dark) {
                        if (!this.chart) return;
                        this.chart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#6b7280';
                        this.chart.options.scales.x.grid.color = dark ? '#334155' : '#e5e7eb';
                        this.chart.options.scales.y.ticks.color = dark ? '#cbd5e1' : '#374151';
                        this.chart.update();
                    },
                };
            }
        </script>
    @endpush
@endonce
