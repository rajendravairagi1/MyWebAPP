<section class="section" style="background: var(--color-bg-soft);">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">Get started</span>
            <h2 style="margin-top: 12px;">Live with your first project in minutes</h2>
        </div>

        <div class="grid-2" style="align-items: center;">
            <div class="steps-list">
                <div class="step-item">
                    <span class="step-number">@include('partials.icon', ['name' => 'user-plus', 'size' => 18])</span>
                    <div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 4px;">Create your account</h3>
                        <p style="color: var(--color-ink-soft); font-size: 0.95rem;">Sign up and set your business name, currency and branding - no lengthy onboarding call required.</p>
                    </div>
                </div>
                <div class="step-item">
                    <span class="step-number">@include('partials.icon', ['name' => 'layout-grid', 'size' => 18])</span>
                    <div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 4px;">Add your first project &amp; units</h3>
                        <p style="color: var(--color-ink-soft); font-size: 0.95rem;">Bring in your existing projects, units and customer bookings - or start fresh with a new one.</p>
                    </div>
                </div>
                <div class="step-item">
                    <span class="step-number">@include('partials.icon', ['name' => 'rocket', 'size' => 18])</span>
                    <div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 4px;">Start recording payments</h3>
                        <p style="color: var(--color-ink-soft); font-size: 0.95rem;">Every installment, receipt and reminder from here on is automatic. Most builders are live the same day.</p>
                    </div>
                </div>
            </div>

            <div class="hero-carousel-viewport shot-pan" style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg);">
                <img src="{{ asset('screenshots/dashboard.png') }}" alt="Pro Builder CRM dashboard, ready right after setup" style="width: 100%; display: block;">
            </div>
        </div>
    </div>
</section>
