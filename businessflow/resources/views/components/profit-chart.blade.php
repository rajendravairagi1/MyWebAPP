@props(['labels', 'values'])

@php
    $colors = collect($values)->map(fn ($v) => $v >= 0 ? '#16a34a' : '#dc2626')->values();
@endphp

<div
    x-data="profitChart(@js($labels), @js($values), @js($colors))"
    x-init="init($el.querySelector('canvas'))"
    @theme-changed.window="applyTheme($event.detail.dark)"
    class="relative" style="height: 260px;">
    <canvas></canvas>
</div>

@once
    @push('scripts')
        <script>
            function profitChart(labels, values, colors) {
                return {
                    chart: null,
                    init(canvas) {
                        const dark = document.documentElement.classList.contains('dark');
                        this.chart = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [
                                    { label: 'Profit / Loss', data: values, backgroundColor: colors, borderRadius: 4, maxBarThickness: 36 },
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: dark ? '#94a3b8' : '#6b7280' } },
                                    y: { grid: { color: dark ? '#334155' : '#e5e7eb' }, ticks: { color: dark ? '#94a3b8' : '#6b7280' } },
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
