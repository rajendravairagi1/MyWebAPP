@extends('layouts.marketing')

@section('title', 'Data Security')
@section('description', "Straight answers about your data on Pro Builder CRM - do we sell it, share it, or look at it? Here's exactly what we do and don't do.")

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 720px;">
        <p class="eyebrow">Data Security</p>
        <h1>Your data, your business — always</h1>
        <p class="body-lg">Straight answers to the questions builders ask us most: do you sell our data? Share it? Look at it? Here's exactly what we do, and what we never do.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 760px;">
        <div class="card" style="border-left: 4px solid var(--color-primary); margin-bottom: var(--space-xl);">
            <strong style="color: var(--color-ink);">Short answer:</strong>
            <span style="color: var(--color-ink-soft);">We don't sell your data. We don't hand it to other companies. Our own team only opens your account when you ask us for help — never to browse or share.</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
            <div class="card" style="display: flex; gap: 16px;">
                <span class="feature-icon">@include('partials.icon', ['name' => 'shield-check'])</span>
                <div>
                    <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Do you sell our data?</h2>
                    <p style="color: var(--color-ink-soft);">No — not to advertisers, not to other builders, not to anyone. Selling customer data isn't part of how we make money; your subscription is. That's not going to change.</p>
                </div>
            </div>

            <div class="card" style="display: flex; gap: 16px;">
                <span class="feature-icon">@include('partials.icon', ['name' => 'handshake'])</span>
                <div>
                    <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Do you share it with anyone else?</h2>
                    <p style="color: var(--color-ink-soft);">We don't share your business data with third parties. The only exceptions are the ones any software needs to actually work — for example, a payment processor to record your payment, or a messaging provider to send a notification you've set up — and only the minimum needed for that one job, plus when the law requires it. Nothing beyond that, ever.</p>
                </div>
            </div>

            <div class="card" style="display: flex; gap: 16px;">
                <span class="feature-icon">@include('partials.icon', ['name' => 'database'])</span>
                <div>
                    <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Can your team see our data?</h2>
                    <p style="color: var(--color-ink-soft);">Only when you ask us for help — resetting a password, restoring a backup, fixing an issue you've reported. We don't browse into accounts otherwise. Support access is there to help you, not to look around.</p>
                </div>
            </div>

            <div class="card" style="display: flex; gap: 16px;">
                <span class="feature-icon">@include('partials.icon', ['name' => 'building-2'])</span>
                <div>
                    <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Is our data mixed up with other businesses'?</h2>
                    <p style="color: var(--color-ink-soft);">No. Every business on Pro Builder CRM is kept completely separate at the database level — one business's projects, customers and payments are never visible to another business, even another one we host.</p>
                </div>
            </div>

            <div class="card" style="display: flex; gap: 16px;">
                <span class="feature-icon">@include('partials.icon', ['name' => 'check'])</span>
                <div>
                    <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Can we keep our own copy of our data?</h2>
                    <p style="color: var(--color-ink-soft);">Yes, any time — the app has a built-in Backup feature that downloads everything (projects, customers, payments, documents) into one file you keep yourself. We recommend doing this regularly. It's your data, and having your own copy is always a good habit — not a sign we might lose it.</p>
                </div>
            </div>

            <div class="card" style="display: flex; gap: 16px;">
                <span class="feature-icon">@include('partials.icon', ['name' => 'wrench'])</span>
                <div>
                    <h2 style="font-size: 1.25rem; margin-bottom: 8px;">What about our login password?</h2>
                    <p style="color: var(--color-ink-soft);">Passwords are stored one-way hashed — not even our own team can look up what your password is. If you're ever locked out, we can only set a new one for you, the same way you'd expect from any secure app.</p>
                </div>
            </div>
        </div>

        <p style="color: var(--color-ink-soft); margin-top: var(--space-xl); line-height: 1.7;">
            This page is the plain-language version — the full legal terms are in our <a href="{{ route('privacy-policy') }}">Privacy Policy</a> and <a href="{{ route('terms-of-service') }}">Terms of Service</a>. Any question not answered here? Email us at <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>.
        </p>
    </div>
</section>

@endsection
