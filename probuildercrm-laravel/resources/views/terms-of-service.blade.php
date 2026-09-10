@extends('layouts.marketing')

@section('title', 'Terms of Service')

@section('content')

<section class="section">
    <div class="container" style="max-width: 760px;">
        <h1 style="font-size: 2rem;">Terms of Service</h1>
        <p style="color: var(--color-ink-soft);">Last updated: {{ now()->year }}</p>

        <div style="display: flex; flex-direction: column; gap: var(--space-md); color: var(--color-ink-soft); line-height: 1.7;">
            <p>By using {{ config('site.name') }}, provided by {{ config('site.legal_name') }}, you agree to these terms.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Using the service</h2>
            <p>Pro Builder CRM is provided on a subscription basis. You're responsible for the accuracy of the data you enter and for keeping your account credentials secure.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Payments</h2>
            <p>Subscription fees are billed in advance on the plan you choose, and are non-refundable except where required by law.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Your data</h2>
            <p>You own the business data you enter into Pro Builder CRM. We do not sell it or share it with third parties.</p>
            <p>Pro Builder CRM provides you tools — including an in-app backup/export feature — to make managing your data easier, but keeping your own copies of critical business data is your responsibility, not ours. We recommend downloading and safely storing a backup regularly, and always before a major change.</p>
            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Contact</h2>
            <p>Questions about these terms? Email us at <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>.</p>
        </div>
    </div>
</section>

@endsection
