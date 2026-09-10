@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'theme'])

        <form method="POST" action="{{ route('admin.theme.update') }}">
            @csrf
            @method('PUT')

            <h2 style="font-size: 1.1rem; margin-bottom: 4px;">Website Theme</h2>
            <p style="color: var(--color-ink-soft); margin-bottom: var(--space-md);">
                Choose which look visitors see on the public website. Nothing else (content, pricing,
                blog posts) changes when you switch.
            </p>

            <div class="grid-2">
                <label class="card" style="cursor: pointer; display: flex; flex-direction: column; gap: 10px; {{ $activeTheme === 'dark' ? 'border-color: var(--color-primary); border-width: 2px;' : '' }}">
                    <input type="radio" name="theme" value="dark" {{ $activeTheme === 'dark' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <strong>Dark (default)</strong>
                    <div style="border-radius: 8px; overflow: hidden; border: 1px solid var(--color-border); background: #05070d; padding: 16px;">
                        <div style="height: 8px; width: 40%; background: #6366f1; border-radius: 4px; margin-bottom: 8px;"></div>
                        <div style="height: 6px; width: 70%; background: rgba(255,255,255,0.2); border-radius: 4px; margin-bottom: 6px;"></div>
                        <div style="height: 6px; width: 55%; background: rgba(255,255,255,0.12); border-radius: 4px;"></div>
                    </div>
                    <span style="color: var(--color-ink-soft); font-size: 0.85rem;">Dark background, bright accent buttons - modern SaaS look.</span>
                </label>

                <label class="card" style="cursor: pointer; display: flex; flex-direction: column; gap: 10px; {{ $activeTheme === 'light' ? 'border-color: var(--color-primary); border-width: 2px;' : '' }}">
                    <input type="radio" name="theme" value="light" {{ $activeTheme === 'light' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <strong>Light</strong>
                    <div style="border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; background: #ffffff; padding: 16px;">
                        <div style="height: 8px; width: 40%; background: #4f46e5; border-radius: 4px; margin-bottom: 8px;"></div>
                        <div style="height: 6px; width: 70%; background: #e2e8f0; border-radius: 4px; margin-bottom: 6px;"></div>
                        <div style="height: 6px; width: 55%; background: #f1f5f9; border-radius: 4px;"></div>
                    </div>
                    <span style="color: var(--color-ink-soft); font-size: 0.85rem;">Clean white background - original look.</span>
                </label>
            </div>

            <h2 style="font-size: 1.1rem; margin-top: var(--space-xl); margin-bottom: 4px;">Admin Panel Theme</h2>
            <p style="color: var(--color-ink-soft); margin-bottom: var(--space-md);">
                This is what you see while working here - it doesn't affect what visitors see on the
                website, and it doesn't affect what your CRM users see in app.probuildercrm.com either.
            </p>

            <div class="grid-2">
                <label class="card" style="cursor: pointer; display: flex; flex-direction: column; gap: 10px; {{ $activeAdminTheme === 'dark' ? 'border-color: var(--color-primary); border-width: 2px;' : '' }}">
                    <input type="radio" name="admin_theme" value="dark" {{ $activeAdminTheme === 'dark' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <strong>Dark (default)</strong>
                    <div style="border-radius: 8px; overflow: hidden; border: 1px solid var(--color-border); background: #05070d; padding: 16px;">
                        <div style="height: 8px; width: 40%; background: #6366f1; border-radius: 4px; margin-bottom: 8px;"></div>
                        <div style="height: 6px; width: 70%; background: rgba(255,255,255,0.2); border-radius: 4px; margin-bottom: 6px;"></div>
                        <div style="height: 6px; width: 55%; background: rgba(255,255,255,0.12); border-radius: 4px;"></div>
                    </div>
                    <span style="color: var(--color-ink-soft); font-size: 0.85rem;">Same dark look as the ProBuilderCRM app.</span>
                </label>

                <label class="card" style="cursor: pointer; display: flex; flex-direction: column; gap: 10px; {{ $activeAdminTheme === 'light' ? 'border-color: var(--color-primary); border-width: 2px;' : '' }}">
                    <input type="radio" name="admin_theme" value="light" {{ $activeAdminTheme === 'light' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <strong>Light</strong>
                    <div style="border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; background: #ffffff; padding: 16px;">
                        <div style="height: 8px; width: 40%; background: #4f46e5; border-radius: 4px; margin-bottom: 8px;"></div>
                        <div style="height: 6px; width: 70%; background: #e2e8f0; border-radius: 4px; margin-bottom: 6px;"></div>
                        <div style="height: 6px; width: 55%; background: #f1f5f9; border-radius: 4px;"></div>
                    </div>
                    <span style="color: var(--color-ink-soft); font-size: 0.85rem;">Clean white background - original admin look.</span>
                </label>
            </div>

            @if (session('status'))
                <p style="color: var(--color-success); margin-top: var(--space-md);">{{ session('status') }}</p>
            @endif

            <div style="margin-top: var(--space-lg);">
                <button type="submit" class="btn btn-primary">Save Themes</button>
            </div>
        </form>
    </div>
</div>
@endsection
