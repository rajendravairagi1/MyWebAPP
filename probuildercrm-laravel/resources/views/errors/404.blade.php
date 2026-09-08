@extends('layouts.marketing')

@section('title', 'Page Not Found')
@section('description', 'The page you were looking for could not be found.')

@section('content')

<section class="section-hero" style="min-height: 50vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 560px;">
        <p class="eyebrow">404 Page</p>
        <h1 style="font-size: clamp(2.2rem, 5vw, 3.2rem);">We couldn't find that page</h1>
        <p class="body-lg">
            The page you're looking for may have moved or no longer exists. Head back home, or book a free demo
            and we'll help you find what you need.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: var(--space-lg);">
            <a href="{{ route('home') }}" class="btn btn-secondary btn-lg btn-on-dark">Back to Home</a>
            <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Book a Free Demo</a>
        </div>
    </div>
</section>

@endsection
