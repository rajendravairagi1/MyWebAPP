@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'seo'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p class="form-error" style="margin-bottom: var(--space-md);">{{ $errors->first() }}</p>
        @endif

        <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-lg);">
            Title, description &amp; keywords for every static page on the site - what shows up in Google search
            results and browser tabs. Leave any field blank to keep the site's built-in default for that page.
            Blog posts have their own SEO fields on each post's edit screen instead of here.
        </p>

        <form method="POST" action="{{ route('admin.seo.update') }}">
            @csrf
            @method('PUT')

            @foreach ($pages as $page)
                <div class="card" style="margin-bottom: var(--space-lg);">
                    <strong style="display: block; margin-bottom: var(--space-md);">{{ $page['label'] }}</strong>

                    <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                        <div class="form-field">
                            <label for="title-{{ $page['key'] }}">Meta Title</label>
                            <input
                                type="text"
                                id="title-{{ $page['key'] }}"
                                name="pages[{{ $page['key'] }}][meta_title]"
                                value="{{ old('pages.'.$page['key'].'.meta_title', $page['meta_title']) }}"
                                class="form-input"
                                maxlength="255"
                                placeholder="Leave blank to use the default title"
                            >
                        </div>
                        <div class="form-field">
                            <label for="description-{{ $page['key'] }}">Meta Description</label>
                            <textarea
                                id="description-{{ $page['key'] }}"
                                name="pages[{{ $page['key'] }}][meta_description]"
                                rows="2"
                                class="form-textarea"
                                maxlength="500"
                                placeholder="Leave blank to use the default description"
                            >{{ old('pages.'.$page['key'].'.meta_description', $page['meta_description']) }}</textarea>
                        </div>
                        <div class="form-field">
                            <label for="keywords-{{ $page['key'] }}">Meta Keywords</label>
                            <textarea
                                id="keywords-{{ $page['key'] }}"
                                name="pages[{{ $page['key'] }}][meta_keywords]"
                                rows="2"
                                class="form-textarea"
                                placeholder="Comma-separated, e.g. real estate CRM software, builder CRM software"
                            >{{ old('pages.'.$page['key'].'.meta_keywords', $page['meta_keywords']) }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach

            <div style="margin-bottom: var(--space-lg);">
                <button type="submit" class="btn btn-primary">Save SEO Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
