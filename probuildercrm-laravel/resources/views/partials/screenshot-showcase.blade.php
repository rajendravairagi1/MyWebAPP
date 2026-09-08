@php
    $__tag = $tag ?? 'See it in action';
    $__heading = $heading ?? 'A real look inside Pro Builder CRM';
    $__description = $description ?? null;
    $__desktopImage = $desktopImage ?? asset('screenshots/dashboard.png');
    $__desktopAlt = $desktopAlt ?? 'Pro Builder CRM dashboard on desktop';
    $__mobileImage = $mobileImage ?? asset('screenshots/mobile-dashboard.png');
    $__mobileAlt = $mobileAlt ?? 'Pro Builder CRM dashboard on mobile';
@endphp
<section class="section">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">{{ $__tag }}</span>
            <h2 style="margin-top: 12px;">{{ $__heading }}</h2>
            @if ($__description)
                <p class="body-lg" style="margin-top: 12px;">{{ $__description }}</p>
            @endif
        </div>

        <div class="screenshot-showcase">
            <div class="hero-carousel shot-pan">
                <div class="hero-carousel-titlebar">
                    <span class="hero-carousel-dot-red"></span>
                    <span class="hero-carousel-dot-yellow"></span>
                    <span class="hero-carousel-dot-green"></span>
                    <span class="hero-carousel-url">app.probuildercrm.com</span>
                </div>
                <div class="hero-carousel-viewport">
                    <img src="{{ $__desktopImage }}" alt="{{ $__desktopAlt }}">
                </div>
            </div>

            <div class="mobile-showcase shot-pan" style="width: 220px;">
                <div class="mobile-showcase-notch"></div>
                <img src="{{ $__mobileImage }}" alt="{{ $__mobileAlt }}">
            </div>
        </div>
    </div>
</section>
