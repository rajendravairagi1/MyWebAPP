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

<section class="section" style="background: var(--color-bg-soft);">
    <div class="container">
        <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
            <span class="tag">Built for the whole team</span>
            <h2 style="margin-top: 12px;">Not just for you</h2>
        </div>
        <div class="grid-3">
            @foreach (config('features.team') as $feature)
                <div class="card">
                    <span class="feature-icon" style="margin-bottom: 14px;">@include('partials.icon', ['name' => $feature['icon']])</span>
                    <h3 style="margin-bottom: 8px;">{{ $feature['title'] }}</h3>
                    <p style="color: var(--color-ink-soft); font-size: 0.95rem;">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-tight" style="text-align: center;">
    <div class="container">
        <h2 style="margin-bottom: var(--space-md);">Let's set it up around your actual projects</h2>
        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Book a Free Demo</a>
    </div>
</section>

@endsection
