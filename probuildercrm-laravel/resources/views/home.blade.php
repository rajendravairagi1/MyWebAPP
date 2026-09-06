@extends('layouts.marketing')

@section('title', 'Real Estate & Construction CRM Software for Builders')
@section('description', config('site.short_description'))

@section('content')

<section class="section-hero hero-glow-section">
    <div class="hero-glow hero-glow-1"></div>
    <div class="hero-glow hero-glow-2"></div>

    <div class="container">
        <p class="eyebrow">Built for Real Estate Builders &amp; Developers</p>
        <h1>Run your entire real estate business from one CRM — not five spreadsheets.</h1>
        <p class="body-lg">
            Projects, unit bookings, customer payments, loans, invoices, contractors and brokers — Pro Builder CRM keeps every
            number in one place, so you always know exactly where your business stands.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: var(--space-2xl);">
            <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Book a Free Demo</a>
            <a href="{{ route('features') }}" class="btn btn-secondary btn-lg btn-on-dark">See Features</a>
        </div>
    </div>

    <div class="container">
        <div class="hero-composition">
            <div class="floating-badge floating-badge-1">
                <span class="floating-badge-icon" style="background: rgba(52,211,153,0.15);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20V10M18 20V4M6 20v-4"/></svg>
                </span>
                <span>
                    <span class="floating-badge-label" style="display:block;">Collected this month</span>
                    <span class="floating-badge-value">₹6,11,105</span>
                </span>
            </div>

            <div class="floating-badge floating-badge-2">
                <span class="floating-badge-icon" style="background: rgba(99,102,241,0.15);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                </span>
                <span>
                    <span class="floating-badge-label" style="display:block;">Active projects</span>
                    <span class="floating-badge-value">12 sites</span>
                </span>
            </div>

            <div class="hero-carousel"
                 x-data="{
                    slides: [
                        { src: '{{ asset('screenshots/dashboard.png') }}', alt: 'Pro Builder CRM dashboard showing project revenue, collections and profit charts in the Nova dark theme' },
                        { src: '{{ asset('screenshots/analytics.png') }}', alt: 'Pro Builder CRM analytics view with unit booking status and payment collection rate charts' },
                        { src: '{{ asset('screenshots/brokers.png') }}', alt: 'Pro Builder CRM brokers module showing commission tracking and statements' }
                    ],
                    index: 0,
                    init() { setInterval(() => { this.index = (this.index + 1) % this.slides.length }, 3800) }
                 }">
                <div class="hero-carousel-titlebar">
                    <span class="hero-carousel-dot-red"></span>
                    <span class="hero-carousel-dot-yellow"></span>
                    <span class="hero-carousel-dot-green"></span>
                    <span class="hero-carousel-url">app.probuildercrm.com</span>
                </div>

                <div class="hero-carousel-viewport">
                    <template x-for="(slide, i) in slides" :key="slide.src">
                        <img :src="slide.src" :alt="slide.alt"
                             :style="{ opacity: i === index ? 1 : 0, transform: i === index ? 'translateY(0)' : 'translateY(14px)' }">
                    </template>
                </div>

                <div class="hero-carousel-nav">
                    <template x-for="(slide, i) in slides" :key="'dot-'+slide.src">
                        <button type="button" :aria-label="'Show slide ' + (i + 1)" @click="index = i"
                                :class="{ active: i === index }" :style="{ width: i === index ? '22px' : '8px' }"></button>
                    </template>
                </div>
            </div>

            <div class="hero-phone-corner">
                <div class="mobile-showcase">
                    <div class="mobile-showcase-notch"></div>
                    <img src="{{ asset('screenshots/mobile-dashboard.png') }}" alt="Pro Builder CRM dashboard on a mobile phone">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">See it in action</span>
            <h2 style="margin-top: 12px;">A real look inside Pro Builder CRM</h2>
        </div>

        <div x-data="{
                tabs: [
                    { key: 'dashboard', label: 'Dashboard & Analytics', title: 'See exactly where your business stands, today', description: 'Revenue trends, invoice status, unit booking status, deal pipeline, top projects by profit and payment collection rate — all in one dashboard, updated the moment a payment is recorded.', image: '{{ asset('screenshots/dashboard.png') }}', alt: 'Pro Builder CRM dashboard with revenue trend, invoice status and payment collection charts' },
                    { key: 'analytics', label: 'Projects & Units', title: 'Every project, every unit, always up to date', description: 'Track unit status, pricing and booking pipeline across every project — with the numbers that matter surfaced automatically, not buried in a spreadsheet.', image: '{{ asset('screenshots/analytics.png') }}', alt: 'Pro Builder CRM analytics view showing unit booking status and deal pipeline' },
                    { key: 'brokers', label: 'Brokers & Team', title: 'Commissions and access, handled properly', description: 'Track every broker\'s commission earned, paid and owed, and give your team exactly the access they need — without exposing prices or profit if you\'d rather they didn\'t see it.', image: '{{ asset('screenshots/brokers.png') }}', alt: 'Pro Builder CRM brokers module with commission tracking' },
                    { key: 'settings', label: 'Business Settings', title: 'Set it up the way your business actually runs', description: 'Currency, branding, multi-branch/company rollups, language — configure Pro Builder CRM around your business, not the other way around.', image: '{{ asset('screenshots/settings.png') }}', alt: 'Pro Builder CRM business settings screen' }
                ],
                active: 'dashboard',
                get activeTab() { return this.tabs.find(t => t.key === this.active) }
             }">
            <div class="feature-tabs-buttons">
                <template x-for="tab in tabs" :key="tab.key">
                    <button type="button" class="feature-tab-btn" :class="{ active: active === tab.key }" @click="active = tab.key" x-text="tab.label"></button>
                </template>
            </div>

            <div class="feature-tabs-grid">
                <div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 12px;" x-text="activeTab.title"></h3>
                    <p style="color: var(--color-ink-soft); font-size: 1.02rem;" x-text="activeTab.description"></p>
                </div>
                <div class="feature-tabs-image">
                    <img :src="activeTab.image" :alt="activeTab.alt">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">What it does</span>
            <h2 style="margin-top: 12px;">Everything a builder actually needs, nothing they don't</h2>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
            @foreach (config('features.core') as $feature)
                <div class="card" style="display: flex; gap: 16px;">
                    <span class="feature-icon">@include('partials.icon', ['name' => $feature['icon']])</span>
                    <div>
                        <h3 style="margin-bottom: 8px;">{{ $feature['title'] }}</h3>
                        <p style="color: var(--color-ink-soft);">{{ $feature['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section" style="background: var(--color-bg-soft);">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">Built for the whole team</span>
            <h2 style="margin-top: 12px;">Not just for the owner</h2>
        </div>
        <div class="grid-3">
            @foreach (config('features.team') as $feature)
                <div class="card">
                    <span class="feature-icon" style="margin-bottom: 14px;">@include('partials.icon', ['name' => $feature['icon']])</span>
                    <h3 style="font-size: 1.1rem; margin-bottom: 8px;">{{ $feature['title'] }}</h3>
                    <p style="color: var(--color-ink-soft); font-size: 0.95rem;">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2" style="align-items: center;">
        <div>
            <span class="tag">Mobile-ready</span>
            <h2 style="margin: 12px 0 var(--space-sm);">Check a customer's balance or record a payment — right from your phone</h2>
            <p style="color: var(--color-ink-soft); font-size: 1.02rem;">
                Pro Builder CRM works fully on mobile, no separate app needed. Whether you're at a site visit or
                on the move, every project, customer and payment is one tap away.
            </p>
        </div>
        <div class="mobile-showcase">
            <div class="mobile-showcase-notch"></div>
            <img src="{{ asset('screenshots/mobile-dashboard.png') }}" alt="Pro Builder CRM dashboard open on a mobile phone, showing project stats and payment collection charts">
        </div>
    </div>
</section>

<section class="section-tight" style="text-align: center;">
    <div class="container">
        <p style="color: var(--color-ink-soft); font-weight: 600;">Trusted by builders in</p>
        <div style="display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; margin-top: 12px; font-weight: 700; color: var(--color-ink);">
            @foreach (config('site.regions') as $region)
                <span>{{ $region }}</span>
            @endforeach
        </div>
    </div>
</section>

<section class="section" style="background: var(--color-primary); color: #fff; text-align: center;">
    <div class="container" style="max-width: 620px;">
        <h2 style="color: #fff; margin-bottom: var(--space-sm);">Let's set it up around your actual projects</h2>
        <p style="color: #e0e7ff; margin-bottom: var(--space-lg);">Book a free demo and we'll walk you through setting up your first project live.</p>
        <a href="{{ route('contact') }}" class="btn btn-lg" style="background: #fff; color: var(--color-primary);">Book a Free Demo</a>
    </div>
</section>

@endsection
