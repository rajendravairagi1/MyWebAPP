@extends('layouts.marketing')

@section('title', $post->title)
@section('description', $post->excerpt)

@push('head')
@include('partials.json-ld', ['schemas' => [
    \App\Support\Seo::articleSchema($post),
    \App\Support\Seo::breadcrumbSchema([
        ['name' => 'Home', 'href' => '/'],
        ['name' => 'Blog', 'href' => '/blog'],
        ['name' => $post->title, 'href' => '/blog/'.$post->slug],
    ]),
]])
@endpush

@section('content')

<article>
    <section class="section-hero">
        <div class="container" style="max-width: 760px;">
            <div style="display: flex; justify-content: center; margin-bottom: 16px;">
                <span class="tag">{{ $post->category }}</span>
            </div>
            <h1 style="font-size: clamp(1.8rem, 3.5vw, 2.6rem);">{{ $post->title }}</h1>
            <p style="color: var(--gray-300);">{{ $post->author }} &middot; {{ $post->date->format('F j, Y') }} &middot; {{ $post->read_time }}</p>
        </div>
    </section>

    @if ($post->featured_image)
        <div class="container" style="max-width: 900px; margin-top: -40px;">
            <img src="{{ asset($post->featured_image) }}"
                 alt="{{ $post->featured_image_alt ?: $post->title }}"
                 style="width: 100%; height: {{ \App\Http\Controllers\Admin\BlogController::pixelsFor($post->featured_image_size) }}px; object-fit: cover; border-radius: var(--radius-lg); display: block;">
            @if ($post->featured_image_caption)
                <p style="text-align: center; font-size: 0.85rem; color: var(--color-ink-soft); margin-top: 8px;">{{ $post->featured_image_caption }}</p>
            @endif
        </div>
    @endif

    <section class="section">
        <div class="container" style="max-width: 720px; display: flex; flex-direction: column; gap: var(--space-md);">
            @foreach ($post->content as $block)
                @if (($block['type'] ?? 'paragraph') === 'heading')
                    <h2 style="font-size: 1.5rem; margin-top: var(--space-sm);">{{ $block['text'] }}</h2>
                @elseif (($block['type'] ?? '') === 'list')
                    <ul style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 6px;">
                        @foreach ($block['items'] ?? [] as $item)
                            <li style="color: var(--color-ink-soft); font-size: 1.02rem; line-height: 1.7;">{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: var(--color-ink-soft); font-size: 1.05rem; line-height: 1.75;">{{ $block['text'] ?? '' }}</p>
                @endif
            @endforeach
        </div>
    </section>
</article>

@endsection
