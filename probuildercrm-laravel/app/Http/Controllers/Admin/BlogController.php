<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::orderByDesc('date')->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $post = new BlogPost([
            'date' => now()->format('Y-m-d'),
            'read_time' => '5 min read',
            'content' => [['type' => 'paragraph', 'text' => '']],
        ]);

        return view('admin.posts.edit', compact('post'));
    }

    public function edit(BlogPost $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = BlogPost::uniqueSlugFrom(($data['slug'] ?? null) ?: $data['title']);

        BlogPost::create($data);

        return redirect()->route('admin.posts.index')->with('status', 'Post created.');
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $this->validated($request);
        $data['slug'] = BlogPost::uniqueSlugFrom($data['slug'] ?: $data['title'], $post->id);

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('status', 'Post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'category' => 'required|string|max:100',
            'author' => 'required|string|max:100',
            'date' => 'required|date',
            'read_time' => 'required|string|max:50',
            'content' => 'required|string',
        ]);

        $content = json_decode($validated['content'], true);
        if (! is_array($content)) {
            $content = [];
        }
        $validated['content'] = $content;

        return $validated;
    }
}
