@props(['labels', 'data', 'color' => '#6366f1', 'label' => ''])

<div
    x-data="lineChart(@js($labels), @js($data), @js($color), @js($label))"
    x-init="init($el.querySelector('canvas'))"
    @theme-changed.window="applyTheme($event.detail.dark)"
    class="relative" style="height: 220px;">
    <canvas></canvas>
</div>

@once
    @push('scripts')
        <script>
            function lineChart(labels, data, color, label) {
                return {
                    chart: null,
                    init(canvas) {
                        const dark = document.documentElement.classList.contains('dark');
                        const gradient = canvas.getContext('2d').createLinearGradient(0, 0, 0, 220);
                        gradient.addColorStop(0, color + '33');
                        gradient.addColorStop(1, color + '00');

                        this.chart = new Chart(canvas, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    label,
                                    data,
                                    borderColor: color,
                                    backgroundColor: gradient,
                                    fill: true,
                                    tension: 0.35,
                                    pointRadius: 3,
                                    pointBackgroundColor: color,
                                    pointBorderColor: color,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: dark ? '#94a3b8' : '#6b7280' } },
                                    y: { grid: { color: dark ? '#334155' : '#e5e7eb' }, ticks: { color: dark ? '#94a3b8' : '#6b7280' }, beginAtZero: true },
                                },
                            },
                        });
                    },
                    applyTheme(dark) {
                        if (!this.chart) return;
                        this.chart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#6b7280';
                        this.chart.options.scales.y.ticks.color = dark ? '#94a3b8' : '#6b7280';
                        this.chart.options.scales.y.grid.color = dark ? '#334155' : '#e5e7eb';
                        this.chart.update();
                    },
                };
            }
        </script>
    @endpush
@endonce
