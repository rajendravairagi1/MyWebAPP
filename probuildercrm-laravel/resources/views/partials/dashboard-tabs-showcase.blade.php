<section class="section">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">See it in action</span>
            <h2 style="margin-top: 12px;">A real look inside Pro Builder CRM</h2>
        </div>

        <div x-data="{
                tabs: [
                    { key: 'dashboard', label: 'Dashboard & Analytics', title: 'See exactly where your business stands, today', description: 'Revenue trends, invoice status, unit booking status, deal pipeline, top projects by profit and payment collection rate - all in one dashboard, updated the moment a payment is recorded.', image: '{{ asset('screenshots/dashboard.png') }}', alt: 'Pro Builder CRM dashboard with revenue trend, invoice status and payment collection charts' },
                    { key: 'analytics', label: 'Projects & Units', title: 'Every project, every unit, always up to date', description: 'Track unit status, pricing and booking pipeline across every project - with the numbers that matter surfaced automatically, not buried in a spreadsheet.', image: '{{ asset('screenshots/analytics.png') }}', alt: 'Pro Builder CRM analytics view showing unit booking status and deal pipeline' },
                    { key: 'brokers', label: 'Brokers & Team', title: 'Commissions and access, handled properly', description: 'Track every broker\'s commission earned, paid and owed, and give your team exactly the access they need - without exposing prices or profit if you\'d rather they didn\'t see it.', image: '{{ asset('screenshots/brokers.png') }}', alt: 'Pro Builder CRM brokers module with commission tracking' },
                    { key: 'settings', label: 'Business Settings', title: 'Set it up the way your business actually runs', description: 'Currency, branding, multi-branch/company rollups, language - configure Pro Builder CRM around your business, not the other way around.', image: '{{ asset('screenshots/settings.png') }}', alt: 'Pro Builder CRM business settings screen' }
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
                <div class="feature-tabs-image shot-pan">
                    <img :src="activeTab.image" :alt="activeTab.alt">
                </div>
            </div>
        </div>
    </div>
</section>
