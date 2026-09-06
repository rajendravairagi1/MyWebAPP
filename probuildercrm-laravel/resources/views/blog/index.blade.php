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
                    <a href="{{ route('blog.show', $post->slug) }}" class="card" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; gap: 10px;">
                        <span class="tag">{{ $post->category }}</span>
                        <h2 style="font-size: 1.2rem;">{{ $post->title }}</h2>
                        <p style="color: var(--color-ink-soft); font-size: 0.95rem;">{{ $post->excerpt }}</p>
                        <span style="font-size: 0.85rem; color: var(--color-ink-soft); margin-top: auto;">
                            {{ $post->date->format('F j, Y') }} &middot; {{ $post->read_time }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection
