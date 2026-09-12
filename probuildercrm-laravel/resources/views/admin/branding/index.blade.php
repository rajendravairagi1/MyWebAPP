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
            The logo shown in the navbar across the whole site. PNG, JPG, SVG or WEBP, up to 2MB -
            a wide/rectangular logo with a transparent background works best.
        </p>

        <div class="card" style="margin-bottom: var(--space-lg); background: var(--color-bg-inverse);">
            <strong style="display: block; margin-bottom: 12px; color: #fff;">Current logo (as shown in the navbar)</strong>
            @if ($logoPath)
                <img src="{{ asset($logoPath) }}?v={{ time() }}" alt="Current logo" style="height: {{ \App\Http\Controllers\Admin\BrandingController::pixelsFor($logoSize) }}px; width: auto; display: block;">
            @else
                <span style="color: var(--color-ink-soft); font-size: 0.9rem;">No logo uploaded yet - the text logo ("Pro Builder CRM") is shown.</span>
            @endif
        </div>

        @if ($logoPath)
            <form method="POST" action="{{ route('admin.branding.size') }}" class="card" style="display: flex; align-items: flex-end; gap: var(--space-md); margin-bottom: var(--space-lg); flex-wrap: wrap;">
                @csrf
                @method('PUT')
                <div class="form-field" style="margin: 0;">
                    <label for="logo_size">Logo size</label>
                    <select id="logo_size" name="logo_size" class="form-select">
                        @foreach ($sizes as $key => $px)
                            <option value="{{ $key }}" {{ $logoSize === $key ? 'selected' : '' }}>
                                {{ ['xs' => 'Extra Small', 'sm' => 'Small', 'md' => 'Medium (default)', 'lg' => 'Large', 'xl' => 'Extra Large'][$key] }} - {{ $px }}px
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Size</button>
            </form>
        @endif

        <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data" data-upload-progress class="card" style="display: flex; flex-direction: column; gap: var(--space-md); margin-bottom: var(--space-lg);">
            @csrf
            <div class="form-field">
                <label for="logo">Upload new logo</label>
                <input type="file" id="logo" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp" required>
                <p style="font-size: 0.82rem; color: var(--color-ink-soft); margin-top: 4px;">Automatically resized &amp; compressed on upload (SVG uploads are kept as-is).</p>

                <div data-upload-progress-wrap hidden>
                    <div class="upload-progress-track">
                        <div data-upload-progress-bar class="upload-progress-bar"></div>
                    </div>
                    <span data-upload-progress-label class="upload-progress-label">Uploading… 0%</span>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Upload &amp; Use This Logo</button>
            </div>
        </form>

        @if ($logoPath)
            <div style="display: flex; gap: var(--space-sm); flex-wrap: wrap;">
                <a href="{{ route('admin.branding.download') }}" class="btn btn-secondary">Download Logo File</a>
                <form method="POST" action="{{ route('admin.branding.destroy') }}" onsubmit="return confirm('Remove the logo and go back to the text logo?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary">Remove Logo</button>
                </form>
            </div>
        @endif

        <hr style="border: none; border-top: 1px solid var(--color-border); margin: var(--space-2xl) 0;">

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            The favicon shown in the browser tab. PNG, JPG, WEBP or SVG, up to 1MB - a simple square icon works best
            (it gets auto-cropped to a square and resized to every size a browser needs).
        </p>

        <div class="card" style="margin-bottom: var(--space-lg); background: var(--color-bg-inverse); display: flex; align-items: center; gap: 14px;">
            <img src="{{ $favicons['png32'] }}?v={{ time() }}" alt="Current favicon" width="32" height="32" style="display: block; border-radius: 6px;">
            <strong style="color: #fff;">
                {{ $hasCustomFavicon ? 'Your custom favicon' : 'Default favicon' }}
            </strong>
        </div>

        <form method="POST" action="{{ route('admin.branding.favicon.update') }}" enctype="multipart/form-data" data-upload-progress class="card" style="display: flex; flex-direction: column; gap: var(--space-md); margin-bottom: var(--space-lg);">
            @csrf
            <div class="form-field">
                <label for="favicon">Upload new favicon</label>
                <input type="file" id="favicon" name="favicon" accept=".png,.jpg,.jpeg,.svg,.webp" required>

                <div data-upload-progress-wrap hidden>
                    <div class="upload-progress-track">
                        <div data-upload-progress-bar class="upload-progress-bar"></div>
                    </div>
                    <span data-upload-progress-label class="upload-progress-label">Uploading… 0%</span>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Upload &amp; Use This Favicon</button>
            </div>
        </form>

        @if ($hasCustomFavicon)
            <form method="POST" action="{{ route('admin.branding.favicon.destroy') }}" onsubmit="return confirm('Reset to the default favicon?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary">Reset to Default Favicon</button>
            </form>
        @endif
    </div>
</div>
@endsection
