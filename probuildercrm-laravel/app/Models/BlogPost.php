<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'excerpt', 'category', 'date', 'read_time', 'author', 'content',
        'featured_image', 'featured_image_alt', 'featured_image_caption', 'featured_image_size',
        'meta_title', 'meta_description', 'meta_keywords',
    ];

    /**
     * What actually goes in <title>/<meta description> for this post -
     * meta_title/meta_description are optional overrides (Admin > Blog
     * Posts > SEO section); most posts leave them blank and just reuse
     * the post's own title/excerpt, which read fine as SEO copy already.
     */
    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function seoDescription(): string
    {
        return $this->meta_description ?: $this->excerpt;
    }

    protected $casts = [
        'date' => 'date',
        'content' => 'array',
    ];

    public static function uniqueSlugFrom(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $suffix++;
            $slug = "{$base}-{$suffix}";
        }

        return $slug;
    }
}
