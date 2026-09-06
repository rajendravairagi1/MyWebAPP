@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'blog'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ New Post</a>
        </div>

        <div class="admin-list">
            @forelse ($posts as $post)
                <div class="admin-list-row">
                    <div>
                        <div style="font-weight: 600;">{{ $post->title }}</div>
                        <div style="font-size: 0.85rem; color: var(--color-ink-soft);">{{ $post->category }} &middot; {{ $post->date->format('Y-m-d') }}</div>
                    </div>
                    <div style="white-space: nowrap;">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-secondary" style="padding: 6px 14px; margin-right: 8px;">Edit</a>
                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" style="display: inline;" onsubmit="return confirm('Delete this post? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary" style="padding: 6px 14px; color: #dc2626; border-color: #fecaca;">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="padding: 20px; color: var(--color-ink-soft);">No posts yet — click "+ New Post" to add one.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
