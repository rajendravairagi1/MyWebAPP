@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'blog'])

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">&larr; Back to list</a>
        </div>

        <form method="POST"
              id="post-form"
              action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
              class="card"
              enctype="multipart/form-data"
              data-upload-progress
              style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @if ($post->exists) @method('PUT') @endif

            <div class="form-field">
                <label for="title">Title</label>
                <input id="title" name="title" required value="{{ old('title', $post->title) }}" class="form-input">
            </div>

            <div class="form-field">
                <label for="slug">URL slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $post->slug) }}" class="form-input" placeholder="Leave as-is to keep the current URL">
                <p style="font-size: 0.82rem; color: var(--color-ink-soft); margin-top: 4px;">
                    Shown at /blog/{{ old('slug', $post->slug) ?: '...' }} - only change this if you actually want the page URL to change.
                </p>
            </div>

            <div class="grid-2">
                <div class="form-field">
                    <label for="category">Category</label>
                    <input id="category" name="category" required value="{{ old('category', $post->category) }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="author">Author</label>
                    <input id="author" name="author" required value="{{ old('author', $post->author) }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="date">Date</label>
                    <input id="date" name="date" type="date" required value="{{ old('date', optional($post->date)->format('Y-m-d')) }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="read_time">Read time</label>
                    <input id="read_time" name="read_time" required value="{{ old('read_time', $post->read_time) }}" class="form-input">
                </div>
            </div>

            <div class="form-field">
                <label for="excerpt">Excerpt (shown on the blog list page)</label>
                <textarea id="excerpt" name="excerpt" required rows="2" class="form-textarea">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <div class="card" style="margin-bottom: var(--space-md);">
                <strong style="display: block; margin-bottom: var(--space-md);">SEO (optional)</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin-bottom: var(--space-md);">
                    Leave blank to use the Title / Excerpt above for search results - only fill these in if you want
                    the page's &lt;title&gt;, description or keywords to say something different.
                </p>

                <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                    <div class="form-field">
                        <label for="meta_title">Meta title</label>
                        <input id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="form-input" maxlength="255" placeholder="Defaults to the Title above">
                    </div>
                    <div class="form-field">
                        <label for="meta_description">Meta description</label>
                        <textarea id="meta_description" name="meta_description" rows="2" class="form-textarea" maxlength="500" placeholder="Defaults to the Excerpt above">{{ old('meta_description', $post->meta_description) }}</textarea>
                    </div>
                    <div class="form-field">
                        <label for="meta_keywords">Meta keywords</label>
                        <textarea id="meta_keywords" name="meta_keywords" rows="2" class="form-textarea" placeholder="Comma-separated">{{ old('meta_keywords', $post->meta_keywords) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label for="featured_image">Featured image</label>
                @if ($post->featured_image)
                    <img src="{{ asset($post->featured_image) }}" alt="" style="width: 220px; height: 130px; object-fit: cover; border-radius: 10px; margin-bottom: 8px; display: block;">
                @endif
                <input id="featured_image" name="featured_image" type="file" accept="image/*" class="form-input">
                <p style="font-size: 0.82rem; color: var(--color-ink-soft); margin-top: 4px;">PNG, JPG or WEBP, up to 4MB. Leave empty to keep the current image. Images are automatically resized &amp; compressed on upload.</p>

                <div data-upload-progress-wrap hidden>
                    <div class="upload-progress-track">
                        <div data-upload-progress-bar class="upload-progress-bar"></div>
                    </div>
                    <span data-upload-progress-label class="upload-progress-label">Uploading… 0%</span>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-field">
                    <label for="featured_image_alt">Image alt text (for SEO &amp; accessibility)</label>
                    <input id="featured_image_alt" name="featured_image_alt" value="{{ old('featured_image_alt', $post->featured_image_alt) }}" class="form-input" placeholder="e.g. Pro Builder CRM broker commission dashboard">
                </div>
                <div class="form-field">
                    <label for="featured_image_size">Image display size</label>
                    <select id="featured_image_size" name="featured_image_size" class="form-input">
                        @foreach ($sizes as $key => $px)
                            <option value="{{ $key }}" @selected(old('featured_image_size', $post->featured_image_size ?: 'lg') === $key)>
                                {{ ucfirst($key) }} ({{ $px }}px tall)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-field">
                <label for="featured_image_caption">Image caption (optional, shown under the image)</label>
                <input id="featured_image_caption" name="featured_image_caption" value="{{ old('featured_image_caption', $post->featured_image_caption) }}" class="form-input">
            </div>

            <div class="form-field" id="quill-wrap">
                <label for="quill-editor">Content</label>
                <div id="quill-editor" style="min-height: 320px;">{!! old('content', $post->content) !!}</div>
                <textarea id="content-input" name="content" hidden></textarea>
            </div>

            @if ($errors->any())
                <p class="form-error">{{ $errors->first() }}</p>
            @endif

            <div>
                <button type="submit" class="btn btn-primary">Save Post</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vendor/quill/quill.snow.css') }}">
    <style>
        /* The admin panel's dark theme text color otherwise cascades into
           the editor's white writing area, making typed text nearly
           invisible (light-on-white) - this pins the editor itself to a
           plain light theme regardless of admin dark mode. */
        #quill-wrap .ql-toolbar.ql-snow {
            background: #fff;
            border-color: #cbd5e1;
            border-radius: 8px 8px 0 0;
        }
        #quill-wrap .ql-container.ql-snow {
            border-color: #cbd5e1;
            border-radius: 0 0 8px 8px;
        }
        #quill-wrap .ql-editor {
            background: #fff;
            color: #1e293b;
            min-height: 300px;
        }
        #quill-wrap .ql-editor.ql-blank::before {
            color: #94a3b8;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/vendor/quill/quill.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ header: [2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['link', 'image'],
                            ['clean'],
                        ],
                        handlers: {
                            image: function () {
                                var input = document.createElement('input');
                                input.type = 'file';
                                input.accept = 'image/png,image/jpeg,image/webp';
                                input.onchange = function () {
                                    var file = input.files[0];
                                    if (!file) return;

                                    var range = quill.getSelection(true);
                                    var form = document.getElementById('post-form');
                                    var token = form.querySelector('input[name="_token"]').value;
                                    var body = new FormData();
                                    body.append('image', file);
                                    body.append('_token', token);

                                    quill.insertText(range.index, 'Uploading image…', { italic: true });

                                    fetch('{{ route('admin.posts.upload-image') }}', { method: 'POST', body: body })
                                        .then(function (res) { return res.json(); })
                                        .then(function (data) {
                                            quill.deleteText(range.index, 'Uploading image…'.length);
                                            quill.insertEmbed(range.index, 'image', data.url);
                                            quill.setSelection(range.index + 1);
                                        })
                                        .catch(function () {
                                            quill.deleteText(range.index, 'Uploading image…'.length);
                                            alert('Image upload failed — please try again.');
                                        });
                                };
                                input.click();
                            },
                        },
                    },
                },
            });

            document.getElementById('post-form').addEventListener('submit', function () {
                document.getElementById('content-input').value = quill.root.innerHTML;
            });
        });
    </script>
@endpush
@endsection
