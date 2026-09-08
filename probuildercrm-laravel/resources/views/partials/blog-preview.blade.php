@php $__previewPosts = \App\Models\BlogPost::orderByDesc('date')->take(3)->get(); @endphp
@if ($__previewPosts->isNotEmpty())
    <section class="section" style="background: var(--color-bg-soft);">
        <div class="container">
            <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
                <span class="tag">From the blog</span>
                <h2 style="margin-top: 12px;">Notes on actually running a builder's business</h2>
            </div>

            <div class="grid-3">
                @foreach ($__previewPosts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="card" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; gap: 10px; padding: 0; overflow: hidden;">
                        @if ($post->featured_image)
                            <img src="{{ asset($post->featured_image) }}" alt="{{ $post->featured_image_alt ?: $post->title }}" style="width: 100%; height: 160px; object-fit: cover;">
                        @endif
                        <div style="padding: {{ $post->featured_image ? '14px var(--space-lg) var(--space-lg)' : 'var(--space-lg)' }}; display: flex; flex-direction: column; gap: 10px;">
                            <span class="tag">{{ $post->category }}</span>
                            <h3 style="font-size: 1.1rem;">{{ $post->title }}</h3>
                            <p style="color: var(--color-ink-soft); font-size: 0.92rem;">{{ $post->excerpt }}</p>
                            <span style="font-size: 0.82rem; color: var(--color-ink-soft); margin-top: auto;">
                                {{ $post->date->format('F j, Y') }} &middot; {{ $post->read_time }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div style="text-align: center; margin-top: var(--space-xl);">
                <a href="{{ route('blog.index') }}" class="btn btn-secondary">Read More on the Blog</a>
            </div>
        </div>
    </section>
@endif
