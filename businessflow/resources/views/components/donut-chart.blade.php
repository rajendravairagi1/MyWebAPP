@props(['labels', 'data', 'colors', 'centerLabel' => null, 'centerValue' => null])

<div
    x-data="donutChart(@js($labels), @js($data), @js($colors))"
    x-init="init($el.querySelector('canvas'))"
    @theme-changed.window="applyTheme($event.detail.dark)"
    class="relative" style="height: 220px;">
    <canvas></canvas>
    @if ($centerValue !== null)
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $centerValue }}</div>
            @if ($centerLabel)
                <div class="text-xs text-gray-400 mt-0.5">{{ $centerLabel }}</div>
            @endif
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            function donutChart(labels, data, colors) {
                return {
                    chart: null,
                    init(canvas) {
                        const dark = document.documentElement.classList.contains('dark');
                        this.chart = new Chart(canvas, {
                            type: 'doughnut',
                            data: {
                                labels,
                                datasets: [{ data, backgroundColor: colors, borderWidth: 0, hoverOffset: 4 }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '72%',
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: dark ? '#cbd5e1' : '#374151', boxWidth: 10, padding: 12, font: { size: 11 } },
                                    },
                                },
                            },
                        });
                    },
                    applyTheme(dark) {
                        if (!this.chart) return;
                        this.chart.options.plugins.legend.labels.color = dark ? '#cbd5e1' : '#374151';
                        this.chart.update();
                    },
                };
            }
        </script>
    @endpush
@endonce
