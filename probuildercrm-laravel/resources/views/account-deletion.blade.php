@extends('layouts.marketing')

@section('title', 'Delete Your Account')

@section('content')

<section class="section">
    <div class="container" style="max-width: 760px;">
        <h1 style="font-size: 2rem;">Delete Your Account</h1>
        <p style="color: var(--color-ink-soft);">Last updated: {{ now()->year }}</p>

        <div style="display: flex; flex-direction: column; gap: var(--space-md); color: var(--color-ink-soft); line-height: 1.7;">
            <p>You can request deletion of your {{ config('site.name') }} account and the personal data tied to it at any time, whether or not you still have the app installed.</p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Option 1: Delete it yourself, in the app</h2>
            <p>
                <a href="{{ config('site.app_login_url') }}">Log in to {{ config('site.name') }}</a>, open <strong>Profile</strong> from the menu, and scroll to <strong>Delete Account</strong>. Confirm with your password and your account is deleted immediately — this removes your login, name, email, phone number and photo.
            </p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">Option 2: Email us</h2>
            <p>
                Can't log in, or want your business's project, customer and payment records removed as well? Email <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a> from the email address on your account and tell us what to delete. We'll confirm with you and complete it within 30 days.
            </p>

            <h2 style="font-size: 1.25rem; color: var(--color-ink);">What gets deleted</h2>
            <p>Deleting your personal account removes your login and profile details. Because {{ config('site.name') }} is used by teams, the business records you worked on (projects, customers, invoices, payments) stay with the business account unless you specifically ask us to remove them too — in that case, use Option 2.</p>
            <p>Some information may be kept longer where we're required to for accounting, tax or legal reasons.</p>
        </div>
    </div>
</section>

@endsection
