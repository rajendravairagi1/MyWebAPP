@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'faqs'])

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">&larr; Back to list</a>
        </div>

        <form method="POST"
              action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}"
              class="card"
              style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @if ($faq->exists) @method('PUT') @endif

            <div class="form-field">
                <label for="question">Question</label>
                <input id="question" name="question" required value="{{ old('question', $faq->question) }}" class="form-input">
            </div>

            <div class="form-field">
                <label for="answer">Answer</label>
                <textarea id="answer" name="answer" required rows="5" class="form-textarea">{{ old('answer', $faq->answer) }}</textarea>
            </div>

            <div class="form-field">
                <label for="sort_order">Display order (lower shows first)</label>
                <input id="sort_order" name="sort_order" type="number" required value="{{ old('sort_order', $faq->sort_order) }}" class="form-input">
            </div>

            @if ($errors->any())
                <p class="form-error">{{ $errors->first() }}</p>
            @endif

            <div>
                <button type="submit" class="btn btn-primary">Save FAQ</button>
            </div>
        </form>
    </div>
</div>
@endsection
