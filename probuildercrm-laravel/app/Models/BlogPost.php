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
    ];

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
