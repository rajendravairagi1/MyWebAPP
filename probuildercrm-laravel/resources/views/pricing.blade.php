@extends('layouts.marketing')

@section('title', 'Pricing')
@section('description', 'Simple, transparent pricing for Pro Builder CRM - plans for solo builders, teams, and multi-branch companies.')

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 720px;">
        <p class="eyebrow">Pricing</p>
        <h1>Simple pricing, no surprises</h1>
        <p class="body-lg">Pick the plan that matches how your business runs today - upgrade any time as your team grows.</p>
    </div>
</section>

<section class="section">
    <div class="container"
         x-data="{
            cycles: [
                { key: 'yearly', label: 'Yearly', months: 12, bonusMonths: 2 },
                { key: 'half_yearly', label: '6 Months', months: 6, bonusMonths: 1 },
                { key: 'monthly', label: 'Monthly', months: 1, bonusMonths: 0 }
            ],
            cycleKey: 'yearly',
            get cycle() { return this.cycles.find(c => c.key === this.cycleKey) },
            pricing(monthlyPrice) {
                const price = monthlyPrice * this.cycle.months;
                const originalPrice = Math.round(price / (1 - {{ $discountPercent }} / 100));
                return { price, originalPrice };
            }
         }">
        <div style="text-align: center; max-width: 560px; margin: 0 auto var(--space-lg);">
            <span class="tag">Plans</span>
            <h2 style="margin-top: 12px;">Straightforward pricing, built to grow with you</h2>
        </div>

        <div class="pricing-cycle-toggle">
            <template x-for="c in cycles" :key="c.key">
                <button type="button" class="pricing-cycle-btn" :class="{ active: cycleKey === c.key }" @click="cycleKey = c.key">
                    <span x-text="c.label"></span>
                    <span class="pricing-cycle-bonus" x-show="c.bonusMonths > 0" x-text="'+' + c.bonusMonths + ' month' + (c.bonusMonths > 1 ? 's' : '') + ' free'"></span>
                </button>
            </template>
        </div>

        <div class="grid-3">
            @foreach ($plans as $plan)
                <div class="card pricing-card {{ $plan->highlighted ? 'highlighted' : '' }}">
                    @if ($plan->highlighted)
                        <span class="pricing-badge-popular">Most Popular</span>
                    @endif
                    @if ($discountPercent > 0)
                        <span class="pricing-badge-off">{{ $discountPercent }}% OFF</span>
                    @endif

                    <div>
                        <h3 style="font-size: 1.3rem; margin-bottom: 4px;">{{ $plan->name }}</h3>
                        <p style="color: var(--color-ink-soft); font-size: 0.9rem;">{{ $plan->description }}</p>
                    </div>

                    <div>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            @if ($discountPercent > 0)
                                <span class="pricing-original" x-text="'₹' + pricing({{ $plan->monthly_price }}).originalPrice.toLocaleString('en-IN')"></span>
                            @endif
                            <span class="pricing-real" x-text="'₹' + pricing({{ $plan->monthly_price }}).price.toLocaleString('en-IN')"></span>
                        </div>
                        <div class="pricing-cycle-note">
                            <span x-show="cycle.bonusMonths > 0" x-text="'for ' + cycle.months + ' months - ' + (cycle.months + cycle.bonusMonths) + ' months of service'"></span>
                            <span x-show="cycle.bonusMonths === 0">per month</span>
                        </div>
                    </div>

                    <ul class="pricing-features">
                        @foreach ($plan->features as $feature)
                            <li>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2.5" style="flex-shrink: 0; margin-top: 2px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('contact') }}" class="btn {{ $plan->highlighted ? 'btn-primary' : 'btn-secondary' }}" style="margin-top: auto; justify-content: center;">
                        Book a Demo
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

@include('partials.product-showcase')

@include('partials.testimonials')

@include('partials.dashboard-tabs-showcase')

@include('partials.team-grid')

@include('partials.onboarding-steps')

<section class="section-tight" style="text-align: center;">
    <div class="container">
        <h2 style="margin-bottom: var(--space-md);">Let's set it up around your actual projects</h2>
        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Book a Free Demo</a>
    </div>
</section>

@endsection
