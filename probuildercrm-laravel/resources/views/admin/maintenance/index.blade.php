@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'maintenance'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            After uploading new code via cPanel, use these instead of visiting the <code>/migrate?token=...</code> link by hand -
            handy if you forget the token or the exact URL.
        </p>

        <div class="card" style="margin-bottom: var(--space-lg); display: flex; flex-direction: column; gap: 10px;">
            <strong>Run migrations &amp; seed pricing</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin: 0;">
                Applies any new database changes from the latest deploy, seeds default pricing plans (only what's missing -
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
                Clears view, config and route caches - use this if a page still shows old content after a deploy.
            </p>
            <form method="POST" action="{{ route('admin.maintenance.clear-cache') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Clear Caches</button>
            </form>
        </div>

        @if (session('output'))
            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Last migration output</strong>
                <pre style="white-space: pre-wrap; font-size: 0.82rem; color: var(--color-ink-soft); margin: 0;">{{ session('output') }}</pre>
            </div>
        @endif

        <div class="card" style="margin-bottom: var(--space-lg); display: flex; flex-direction: column; gap: 10px;">
            <strong>Download database backup</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin: 0;">
                The entire live database as one file - every blog post, testimonial, FAQ, pricing plan, demo request and
                setting currently on the site. Takes a few seconds. Keep a recent copy somewhere safe.
            </p>
            <a href="{{ route('admin.maintenance.backup.database') }}" class="btn btn-secondary" style="align-self: flex-start;">Download Database (.sqlite)</a>
        </div>

        <div class="card" style="display: flex; flex-direction: column; gap: 10px;">
            <strong>Download full site backup</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin: 0;">
                Everything needed to rebuild the site from scratch: all the application code, the live database, and every
                file ever uploaded from Admin - logo, favicon, blog post images. This can take a minute or two and the
                file can be sizeable, depending on how many images have been uploaded.
            </p>
            <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin: 0;">
                Doesn't include the <code>vendor</code> folder (third-party libraries the site depends on, not your
                content) - if this site is ever rebuilt from zero, that needs a separate one-time setup.
            </p>
            <a href="{{ route('admin.maintenance.backup.full') }}" class="btn btn-secondary" style="align-self: flex-start;">Download Full Backup (.zip)</a>
        </div>
    </div>
</div>
@endsection
