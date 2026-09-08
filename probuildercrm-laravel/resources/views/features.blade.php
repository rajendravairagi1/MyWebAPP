@extends('layouts.marketing')

@section('title', 'Features')
@section('description', "See everything Pro Builder CRM handles - project & unit management, customer payments, loan disbursements, invoices, contractors, brokers, investors and more.")

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 720px;">
        <p class="eyebrow">Features</p>
        <h1>Every project, every unit, always up to date</h1>
        <p class="body-lg">One system for the whole real estate business - not a spreadsheet for each piece of it.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="display: flex; flex-direction: column; gap: var(--space-lg);">
        @foreach (config('features.core') as $feature)
            <div class="card" style="display: flex; gap: 16px;">
                <span class="feature-icon">@include('partials.icon', ['name' => $feature['icon']])</span>
                <div>
                    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">{{ $feature['title'] }}</h2>
                    <p style="color: var(--color-ink-soft);">{{ $feature['description'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

@include('partials.team-grid', ['heading' => 'Not just for you'])

@include('partials.product-showcase')

@include('partials.testimonials')

@include('partials.dashboard-tabs-showcase')

<section class="section-tight" style="text-align: center;">
    <div class="container">
        <h2 style="margin-bottom: var(--space-md);">Let's set it up around your actual projects</h2>
        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Book a Free Demo</a>
    </div>
</section>

@endsection
