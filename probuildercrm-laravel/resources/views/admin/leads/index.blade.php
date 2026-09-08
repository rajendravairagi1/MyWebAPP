@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 1000px;">
        @include('admin.partials.tabs', ['active' => 'leads'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            Everyone who has submitted the Contact / Book a Demo form. Set who gets emailed for each new one under
            <a href="{{ route('admin.integrations.index') }}">Integrations</a>.
        </p>

        @if ($leads->isEmpty())
            <p style="color: var(--color-ink-soft);">No demo requests yet.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($leads as $lead)
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                            <div>
                                <strong>{{ $lead->name }}</strong>
                                @if ($lead->plan)
                                    <span class="tag" style="margin-left: 8px;">{{ $lead->plan }}</span>
                                @endif
                                <div style="font-size: 0.88rem; color: var(--color-ink-soft); margin-top: 2px;">
                                    <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                                    @if ($lead->phone)
                                        &middot; <a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 0.8rem; color: var(--color-ink-soft);">{{ $lead->created_at->format('M j, Y g:i A') }}</span>
                                <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" onsubmit="return confirm('Delete this demo request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mini-btn" style="color: #dc2626;">Delete</button>
                                </form>
                            </div>
                        </div>
                        <p style="margin-top: 10px; color: var(--color-ink-soft); font-size: 0.92rem; white-space: pre-wrap;">{{ $lead->message }}</p>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: var(--space-lg);">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
