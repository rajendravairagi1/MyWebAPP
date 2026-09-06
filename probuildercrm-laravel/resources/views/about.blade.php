@extends('layouts.marketing')

@section('title', 'About Us')
@section('description', "Why Pro Builder CRM exists, and who it's built for.")

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 760px;">
        <p class="eyebrow">About Us</p>
        <h1>Built by people who've watched builders fight with Excel for years</h1>
        <p class="body-lg">
            Pro Builder CRM exists because real estate builders deserve software built around how they actually work — not a
            generic CRM stretched to fit.
        </p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 760px; display: flex; flex-direction: column; gap: var(--space-md);">
        <h2>Why we built this</h2>
        <p style="color: var(--color-ink-soft); font-size: 1.05rem;">
            Every builder we spoke to had the same story: bookings tracked in one Excel file, payments in another, site
            photos scattered across phones, and a WhatsApp group standing in for a proper follow-up system. One missed
            installment or one lost paper cost real money — and a customer's trust.
        </p>
        <p style="color: var(--color-ink-soft); font-size: 1.05rem;">
            Pro Builder CRM was built to be the single place all of that lives — projects, units, customers, payments,
            loans, invoices, contractors, brokers and investors — simple enough to set up in a day, and built specifically
            around a builder's actual day-to-day.
        </p>
    </div>
</section>

<section class="section" style="background: var(--color-primary); color: #fff; text-align: center;">
    <div class="container" style="max-width: 620px;">
        <h2 style="color: #fff; margin-bottom: var(--space-sm);">Let's talk about your business</h2>
        <p style="color: #e0e7ff; margin-bottom: var(--space-lg);">Tell us a bit about your projects and we'll set up a walkthrough built around them — not a generic demo.</p>
        <a href="{{ route('contact') }}" class="btn btn-lg" style="background: #fff; color: var(--color-primary);">Get in Touch</a>
    </div>
</section>

@endsection
