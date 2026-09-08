@extends('layouts.marketing')

@section('title', 'Contact Us')
@section('description', 'Book a free demo of Pro Builder CRM, built around your actual projects.')

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 720px;">
        <p class="eyebrow">Contact</p>
        <h1>Let's talk about your business</h1>
        <p class="body-lg">Tell us a bit about your projects and we'll set up a walkthrough built around them — not a generic demo.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 960px; display: grid; grid-template-columns: 1fr 1.2fr; gap: var(--space-xl);">
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <a href="{{ config('site.whatsapp') }}" target="_blank" rel="noopener noreferrer" class="card" style="text-decoration: none;">
                <h3 style="margin-bottom: 4px;">WhatsApp</h3>
                <p style="color: var(--color-ink-soft);">Fastest way to reach us — message us directly.</p>
            </a>
            <a href="mailto:{{ config('site.email') }}" class="card" style="text-decoration: none;">
                <h3 style="margin-bottom: 4px;">Email</h3>
                <p style="color: var(--color-ink-soft);">{{ config('site.email') }}</p>
            </a>
            <div class="card">
                <h3 style="margin-bottom: 4px;">Book a Call</h3>
                <p style="color: var(--color-ink-soft);">Message us on WhatsApp or email with a good time, and we'll set up a call.</p>
            </div>
        </div>

        @if (session('status') === 'sent')
            <div class="card" style="text-align: center;">
                <h3>Thanks — we'll be in touch shortly.</h3>
                <p style="color: var(--color-ink-soft);">We usually reply within one business day.</p>
            </div>
        @else
            <form method="POST" action="{{ route('contact.store') }}" class="card" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div class="form-field">
                    <label for="name">Name</label>
                    <input id="name" name="name" required value="{{ old('name') }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="phone">Phone (optional)</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="message">Tell us about your business</label>
                    <textarea id="message" name="message" required rows="4" class="form-textarea">{{ old('message') }}</textarea>
                </div>

                @if ($recaptchaSiteKey ?? null)
                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                @endif

                @if ($errors->any())
                    <p class="form-error">{{ $errors->first() }}</p>
                @endif

                <button type="submit" class="btn btn-primary btn-lg" style="justify-content: center;">Book a Free Demo</button>
            </form>
        @endif
    </div>
</section>

@if ($recaptchaSiteKey ?? null)
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endif

@endsection
