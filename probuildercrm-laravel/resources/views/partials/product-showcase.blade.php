@php
    $__psTag = $tag ?? 'Product tour';
    $__psHeading = $heading ?? 'See Pro Builder CRM on desktop and mobile';
    $__psDescription = $description ?? 'Switch views to see exactly what you and your team will be working in.';

    $__desktopShots = [
        ['src' => asset('screenshots/dashboard.png'), 'alt' => 'Pro Builder CRM dashboard showing project revenue, collections and profit charts'],
        ['src' => asset('screenshots/analytics.png'), 'alt' => 'Pro Builder CRM analytics view with unit booking status and payment collection rate charts'],
        ['src' => asset('screenshots/brokers.png'), 'alt' => 'Pro Builder CRM brokers module showing commission tracking and statements'],
        ['src' => asset('screenshots/settings.png'), 'alt' => 'Pro Builder CRM business settings screen'],
    ];
    $__mobileShots = [
        ['src' => asset('screenshots/mobile-dashboard.png'), 'alt' => 'Pro Builder CRM dashboard open on a mobile phone'],
    ];
@endphp
<section class="section">
    <div class="container"
         x-data="{
            view: 'desktop',
            dIndex: 0,
            mIndex: 0,
            desktopCount: {{ count($__desktopShots) }},
            mobileCount: {{ count($__mobileShots) }},
            timer: null,
            startAuto() {
                clearInterval(this.timer);
                this.timer = setInterval(() => {
                    if (this.view === 'desktop' && this.desktopCount > 1) {
                        this.dIndex = (this.dIndex + 1) % this.desktopCount;
                    } else if (this.view === 'mobile' && this.mobileCount > 1) {
                        this.mIndex = (this.mIndex + 1) % this.mobileCount;
                    }
                }, 3800);
            },
            init() { this.startAuto(); }
         }">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">{{ $__psTag }}</span>
            <h2 style="margin-top: 12px;">{{ $__psHeading }}</h2>
            @if ($__psDescription)
                <p class="body-lg" style="margin-top: 12px;">{{ $__psDescription }}</p>
            @endif
        </div>

        <div class="pricing-cycle-toggle">
            <button type="button" class="pricing-cycle-btn" :class="{ active: view === 'desktop' }" @click="view = 'desktop'; dIndex = 0; startAuto()">Desktop Views</button>
            <button type="button" class="pricing-cycle-btn" :class="{ active: view === 'mobile' }" @click="view = 'mobile'; mIndex = 0; startAuto()">Mobile Views</button>
        </div>

        <div x-show="view === 'desktop'">
            <div class="hero-carousel shot-pan">
                <div class="hero-carousel-titlebar">
                    <span class="hero-carousel-dot-red"></span>
                    <span class="hero-carousel-dot-yellow"></span>
                    <span class="hero-carousel-dot-green"></span>
                    <span class="hero-carousel-url">app.probuildercrm.com</span>
                </div>
                <div class="hero-carousel-viewport">
                    @foreach ($__desktopShots as $i => $shot)
                        <img src="{{ $shot['src'] }}" alt="{{ $shot['alt'] }}"
                             :style="{ opacity: dIndex === {{ $i }} ? 1 : 0, transform: dIndex === {{ $i }} ? 'translateY(0)' : 'translateY(14px)' }">
                    @endforeach
                </div>
                @if (count($__desktopShots) > 1)
                    <div class="hero-carousel-nav">
                        @foreach ($__desktopShots as $i => $shot)
                            <button type="button" aria-label="Show desktop screen {{ $i + 1 }}" @click="dIndex = {{ $i }}; startAuto()"
                                    :class="{ active: dIndex === {{ $i }} }" :style="{ width: dIndex === {{ $i }} ? '22px' : '8px' }"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div x-show="view === 'mobile'" x-cloak>
            <div class="mobile-showcase shot-pan">
                <div class="mobile-showcase-notch"></div>
                @foreach ($__mobileShots as $i => $shot)
                    <img src="{{ $shot['src'] }}" alt="{{ $shot['alt'] }}"
                         x-show="mIndex === {{ $i }}">
                @endforeach
            </div>
            @if (count($__mobileShots) > 1)
                <div class="hero-carousel-nav" style="background: none;">
                    @foreach ($__mobileShots as $i => $shot)
                        <button type="button" aria-label="Show mobile screen {{ $i + 1 }}" @click="mIndex = {{ $i }}; startAuto()"
                                :class="{ active: mIndex === {{ $i }} }" :style="{ width: mIndex === {{ $i }} ? '22px' : '8px' }"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
