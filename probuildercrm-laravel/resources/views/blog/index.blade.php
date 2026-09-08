@extends('layouts.marketing')

@section('title', 'Blog')
@section('description', 'Guides and insights for real estate builders and developers — project management, collections, and growing your business.')

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 720px;">
        <p class="eyebrow">Insights</p>
        <h1>The Pro Builder CRM Blog</h1>
        <p class="body-lg">Practical notes on running a real estate business — bookings, collections, and growth.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        @if ($posts->isEmpty())
            <p style="text-align: center; color: var(--color-ink-soft);">No posts yet — check back soon.</p>
        @else
            <div class="grid-2">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="card" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; gap: 10px; padding: 0; overflow: hidden;">
                        @if ($post->featured_image)
                            <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; height: 190px; object-fit: cover;">
                        @endif
                        <div style="padding: {{ $post->featured_image ? '16px var(--space-lg) var(--space-lg)' : 'var(--space-lg)' }}; display: flex; flex-direction: column; gap: 10px;">
                            <span class="tag">{{ $post->category }}</span>
                            <h2 style="font-size: 1.2rem;">{{ $post->title }}</h2>
                            <p style="color: var(--color-ink-soft); font-size: 0.95rem;">{{ $post->excerpt }}</p>
                            <span style="font-size: 0.85rem; color: var(--color-ink-soft); margin-top: auto;">
                                {{ $post->date->format('F j, Y') }} &middot; {{ $post->read_time }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection
