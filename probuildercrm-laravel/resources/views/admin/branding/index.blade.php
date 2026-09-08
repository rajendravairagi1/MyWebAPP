@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'branding'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p class="form-error" style="margin-bottom: var(--space-md);">{{ $errors->first() }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            The logo shown in the navbar across the whole site. PNG, JPG, SVG or WEBP, up to 2MB —
            a wide/rectangular logo with a transparent background works best.
        </p>

        <div class="card" style="margin-bottom: var(--space-lg);">
            <strong style="display: block; margin-bottom: 12px;">Current logo</strong>
            @if ($logoPath)
                <img src="{{ asset($logoPath) }}?v={{ time() }}" alt="Current logo" style="max-height: 60px; max-width: 100%;">
            @else
                <span style="color: var(--color-ink-soft); font-size: 0.9rem;">No logo uploaded yet — the text logo ("Pro Builder CRM") is shown.</span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data" class="card" style="display: flex; flex-direction: column; gap: var(--space-md); margin-bottom: var(--space-lg);">
            @csrf
            <div class="form-field">
                <label for="logo">Upload new logo</label>
                <input type="file" id="logo" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Upload &amp; Use This Logo</button>
            </div>
        </form>

        @if ($logoPath)
            <form method="POST" action="{{ route('admin.branding.destroy') }}" onsubmit="return confirm('Remove the logo and go back to the text logo?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary">Remove Logo</button>
            </form>
        @endif
    </div>
</div>
@endsection
