<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        // 2 per row, 4 rows before pagination kicks in.
        $posts = BlogPost::orderByDesc('date')->paginate(8);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();

        return view('blog.show', compact('post'));
    }
}
