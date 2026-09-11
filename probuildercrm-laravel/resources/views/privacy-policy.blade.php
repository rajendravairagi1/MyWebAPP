@extends('layouts.marketing')

@section('title', 'Privacy Policy')

@section('content')

<section class="section">
    <div class="container" style="max-width: 760px;">
        <h1 style="font-size: 2rem;">Privacy Policy</h1>
        <p style="color: var(--color-ink-soft);">Last updated: {{ now()->year }}</p>

        <div style="display: flex; flex-direction: column; gap: var(--space-md); color: var(--color-ink-soft); line-height: 1.7;">
            <p>{{ config('site.legal_name') }} ("we", "us") operates {{ config('site.name') }}. This policy explains what information we collect when you use our website and product, and how we use it.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Information we collect</h2>
            <p>When you fill out our contact form or book a demo, we collect your name, email address, phone number and any message you send us. When you use Pro Builder CRM itself, the business data you enter (projects, customers, payments, etc.) is stored to provide the service to you.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">How we use it</h2>
            <p>We use this information to respond to your enquiries, provide the CRM service, and improve our product. We do not sell your information to third parties.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Data security</h2>
            <p>Every business's data on Pro Builder CRM is isolated from every other business, and passwords are stored using one-way industry-standard hashing. For plain-language answers to common questions — do we sell your data, share it, or look at it — see our <a href="{{ route('data-security') }}">Data Security</a> page.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Contact</h2>
            <p>Questions about this policy? Email us at <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>.</p>
        </div>
    </div>
</section>

@endsection
