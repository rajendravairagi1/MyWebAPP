@php
    $__teamTag = $tag ?? 'Built for the whole team';
    $__teamHeading = $heading ?? 'Not just for the owner';
    $__iconColors = ['feature-icon-blue', 'feature-icon-orange', 'feature-icon-violet', 'feature-icon-green'];
@endphp
<section class="section" style="background: var(--color-bg-soft);">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">{{ $__teamTag }}</span>
            <h2 style="margin-top: 12px;">{{ $__teamHeading }}</h2>
        </div>
        <div class="grid-3">
            @foreach (config('features.team') as $i => $feature)
                <div class="card">
                    <span class="feature-icon {{ $__iconColors[$i % 4] }}" style="margin-bottom: 14px;">@include('partials.icon', ['name' => $feature['icon'] ?? null])</span>
                    <h3 style="font-size: 1.1rem; margin-bottom: 8px;">{{ $feature['title'] }}</h3>
                    <p style="color: var(--color-ink-soft); font-size: 0.95rem;">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
