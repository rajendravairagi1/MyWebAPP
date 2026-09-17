<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\ImageOptimizer;
use App\Support\SitemapPing;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Featured images live under public/blog-images/ as plain files (same
     * pattern as the site logo) — no storage:link symlink to remember
     * after a zip-extract deploy on shared hosting.
     */
    private const IMAGE_DIR = 'blog-images';

    /**
     * Display heights (px) for the banner image on the full post page —
     * lets the admin pick how prominent a given post's image looks
     * without needing to touch code.
     */
    public const SIZES = [
        'sm' => 220,
        'md' => 320,
        'lg' => 420,
        'xl' => 520,
    ];

    public const DEFAULT_SIZE = 'lg';

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
            'content' => '',
            'featured_image_size' => self::DEFAULT_SIZE,
        ]);

        return view('admin.posts.edit', ['post' => $post, 'sizes' => self::SIZES]);
    }

    public function edit(BlogPost $post)
    {
        return view('admin.posts.edit', ['post' => $post, 'sizes' => self::SIZES]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = BlogPost::uniqueSlugFrom(($data['slug'] ?? null) ?: $data['title']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->storeImage($request->file('featured_image'), $data['slug']);
        }

        BlogPost::create($data);
        SitemapPing::ping();

        return redirect()->route('admin.posts.index')->with('status', 'Post created - sitemap submitted to search engines.');
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $this->validated($request);
        $data['slug'] = BlogPost::uniqueSlugFrom(($data['slug'] ?? null) ?: $data['title'], $post->id);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->storeImage($request->file('featured_image'), $data['slug']);
        }

        $post->update($data);
        SitemapPing::ping();

        return redirect()->route('admin.posts.index')->with('status', 'Post updated - sitemap submitted to search engines.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    /**
     * The rich-text editor's image button uploads straight from here
     * instead of embedding a base64 copy in the post's own HTML - keeps
     * post content small and reuses the same optimize-and-resize pipeline
     * as the featured image.
     */
    public function uploadContentImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        $dir = public_path(self::IMAGE_DIR);
        File::ensureDirectoryExists($dir);

        $filename = 'inline-'.time().'-'.Str::random(6).'.'.$request->file('image')->getClientOriginalExtension();
        ImageOptimizer::optimizeAndSave($request->file('image'), $dir.'/'.$filename);

        return response()->json(['url' => asset(self::IMAGE_DIR.'/'.$filename)]);
    }

    public static function pixelsFor(?string $size): int
    {
        return self::SIZES[$size] ?? self::SIZES[self::DEFAULT_SIZE];
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
            'content' => 'required|string|max:200000',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'featured_image_alt' => 'nullable|string|max:255',
            'featured_image_caption' => 'nullable|string|max:255',
            'featured_image_size' => 'nullable|in:'.implode(',', array_keys(self::SIZES)),
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:2000',
        ]);

        $validated['featured_image_size'] = $validated['featured_image_size'] ?? self::DEFAULT_SIZE;
        unset($validated['featured_image']);

        return $validated;
    }

    private function storeImage(UploadedFile $file, string $slug): string
    {
        $dir = public_path(self::IMAGE_DIR);
        File::ensureDirectoryExists($dir);

        $filename = $slug.'-'.time().'.'.$file->getClientOriginalExtension();
        ImageOptimizer::optimizeAndSave($file, $dir.'/'.$filename);

        return self::IMAGE_DIR.'/'.$filename;
    }
}
