@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'testimonials'])

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">&larr; Back to list</a>
        </div>

        <form method="POST"
              action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}"
              class="card"
              style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @if ($testimonial->exists) @method('PUT') @endif

            <div class="form-field">
                <label for="quote">Quote</label>
                <textarea id="quote" name="quote" required rows="4" class="form-textarea">{{ old('quote', $testimonial->quote) }}</textarea>
            </div>

            <div class="grid-2">
                <div class="form-field">
                    <label for="author_role">Author's role (e.g. "Real Estate Builder")</label>
                    <input id="author_role" name="author_role" required value="{{ old('author_role', $testimonial->author_role) }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="author_city">City (optional)</label>
                    <input id="author_city" name="author_city" value="{{ old('author_city', $testimonial->author_city) }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="rating">Star rating</label>
                    <select id="rating" name="rating" class="form-input">
                        @foreach ([5, 4, 3, 2, 1] as $r)
                            <option value="{{ $r }}" @selected(old('rating', $testimonial->rating) == $r)>{{ $r }} star{{ $r > 1 ? 's' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label for="sort_order">Display order (lower shows first)</label>
                    <input id="sort_order" name="sort_order" type="number" required value="{{ old('sort_order', $testimonial->sort_order) }}" class="form-input">
                </div>
            </div>

            @if ($errors->any())
                <p class="form-error">{{ $errors->first() }}</p>
            @endif

            <div>
                <button type="submit" class="btn btn-primary">Save Testimonial</button>
            </div>
        </form>
    </div>
</div>
@endsection
