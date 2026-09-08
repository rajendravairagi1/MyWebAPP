@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'faqs'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            Shown on the FAQ page (all of them) and the Home page (first 4). Editing one here updates it everywhere.
        </p>

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">+ New FAQ</a>
        </div>

        @if ($faqs->isEmpty())
            <p style="color: var(--color-ink-soft);">No FAQs yet.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($faqs as $faq)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                        <div>
                            <strong>{{ $faq->question }}</strong>
                            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-top: 4px;">{{ \Illuminate\Support\Str::limit($faq->answer, 140) }}</p>
                        </div>
                        <div style="display: flex; gap: 8px; flex-shrink: 0;">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ?')">
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
