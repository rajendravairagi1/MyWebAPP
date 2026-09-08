@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'maintenance'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            After uploading new code via cPanel, use these instead of visiting the <code>/migrate?token=...</code> link by hand —
            handy if you forget the token or the exact URL.
        </p>

        <div class="card" style="margin-bottom: var(--space-lg); display: flex; flex-direction: column; gap: 10px;">
            <strong>Run migrations &amp; seed pricing</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin: 0;">
                Applies any new database changes from the latest deploy, seeds default pricing plans (only what's missing —
                never overwrites prices you've already changed), and clears caches. Run this after every code upload.
            </p>
            <form method="POST" action="{{ route('admin.maintenance.migrate') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Run Migrations &amp; Seed</button>
            </form>
        </div>

        <div class="card" style="margin-bottom: var(--space-lg); display: flex; flex-direction: column; gap: 10px;">
            <strong>Clear caches</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin: 0;">
                Clears view, config and route caches — use this if a page still shows old content after a deploy.
            </p>
            <form method="POST" action="{{ route('admin.maintenance.clear-cache') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Clear Caches</button>
            </form>
        </div>

        @if (session('output'))
            <div class="card">
                <strong style="display: block; margin-bottom: 8px;">Last migration output</strong>
                <pre style="white-space: pre-wrap; font-size: 0.82rem; color: var(--color-ink-soft); margin: 0;">{{ session('output') }}</pre>
            </div>
        @endif
    </div>
</div>
@endsection
