@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'blog'])

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">&larr; Back to list</a>
        </div>

        <form method="POST"
              action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
              class="card"
              enctype="multipart/form-data"
              data-upload-progress
              style="display: flex; flex-direction: column; gap: 16px;"
              x-data="{
                blocks: {{ json_encode($post->content ?: [['type' => 'paragraph', 'text' => '']]) }},
                addBlock(type) { this.blocks.push(type === 'list' ? { type: 'list', items: [''] } : { type, text: '' }) },
                removeBlock(i) { this.blocks.splice(i, 1) },
                moveBlock(i, dir) { const t = i + dir; if (t < 0 || t >= this.blocks.length) return; const tmp = this.blocks[i]; this.blocks[i] = this.blocks[t]; this.blocks[t] = tmp; }
              }"
              @submit="$refs.contentInput.value = JSON.stringify(blocks)">
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

            <div class="form-field">
                <label>Content</label>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <template x-for="(block, index) in blocks" :key="index">
                        <div class="admin-block">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--color-ink-soft);" x-text="block.type"></span>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="mini-btn" @click="moveBlock(index, -1)">&uarr;</button>
                                    <button type="button" class="mini-btn" @click="moveBlock(index, 1)">&darr;</button>
                                    <button type="button" class="mini-btn" style="color: #dc2626;" @click="removeBlock(index)">Remove</button>
                                </div>
                            </div>

                            <template x-if="block.type === 'list'">
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <template x-for="(item, itemIndex) in block.items" :key="itemIndex">
                                        <div style="display: flex; gap: 6px;">
                                            <input class="form-input" x-model="block.items[itemIndex]">
                                            <button type="button" class="mini-btn" @click="block.items.splice(itemIndex, 1)">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" class="mini-btn" @click="block.items.push('')">+ List item</button>
                                </div>
                            </template>

                            <template x-if="block.type !== 'list'">
                                <textarea class="form-textarea" x-model="block.text" :rows="block.type === 'heading' ? 1 : 3"></textarea>
                            </template>
                        </div>
                    </template>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 12px;">
                    <button type="button" class="btn btn-secondary" @click="addBlock('paragraph')">+ Paragraph</button>
                    <button type="button" class="btn btn-secondary" @click="addBlock('heading')">+ Heading</button>
                    <button type="button" class="btn btn-secondary" @click="addBlock('list')">+ List</button>
                </div>
            </div>

            <input type="hidden" name="content" x-ref="contentInput">

            @if ($errors->any())
                <p class="form-error">{{ $errors->first() }}</p>
            @endif

            <div>
                <button type="submit" class="btn btn-primary">Save Post</button>
            </div>
        </form>
    </div>
</div>
@endsection
