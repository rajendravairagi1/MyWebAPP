@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'testimonials'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            Shown on the Home, Features, Pricing and FAQ pages. Editing one here updates it everywhere immediately.
        </p>

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">+ New Testimonial</a>
        </div>

        @if ($testimonials->isEmpty())
            <p style="color: var(--color-ink-soft);">No testimonials yet - none will show on the site until you add one.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($testimonials as $t)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                        <div>
                            <strong>{{ $t->author_role }}</strong>@if ($t->author_city), {{ $t->author_city }}@endif
                            <span style="color: #f59e0b;">{{ str_repeat('★', $t->rating) }}</span>
                            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-top: 4px;">{{ \Illuminate\Support\Str::limit($t->quote, 140) }}</p>
                        </div>
                        <div style="display: flex; gap: 8px; flex-shrink: 0;">
                            <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" onsubmit="return confirm('Delete this testimonial?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mini-btn" style="color: #dc2626;">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
