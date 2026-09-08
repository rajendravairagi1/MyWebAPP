@php
    $__adminTitles = [
        'blog' => 'Blog Posts',
        'leads' => 'Demo Requests',
        'pricing' => 'Pricing',
        'testimonials' => 'Testimonials',
        'faqs' => 'FAQs',
        'theme' => 'Theme',
        'branding' => 'Branding',
        'social' => 'Social & Contact',
        'integrations' => 'Integrations',
        'maintenance' => 'Maintenance',
    ];
@endphp

<div class="admin-header">
    <h1 style="font-size: 1.6rem;">{{ $__adminTitles[$active] ?? 'Admin' }}</h1>
</div>
