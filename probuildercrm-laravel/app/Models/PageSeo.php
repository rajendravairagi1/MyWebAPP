<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Admin-editable title/description/keywords override for one static
 * marketing page, keyed by that page's route name. A blank field means
 * "keep the page's built-in default" (set in the page's own
 * @section('title', ...) etc.) rather than an empty tag - see
 * layouts/marketing.blade.php, which only overrides a field when this
 * row exists AND that specific field is non-empty.
 */
class PageSeo extends Model
{
    protected $fillable = [
        'page_key',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Every static page an admin can customize, in the order shown on the
     * SEO admin screen. Blog posts are excluded - each post has its own
     * meta_title/meta_description/meta_keywords columns instead, since
     * they're per-post content, not a fixed set of pages.
     */
    public const PAGES = [
        'home' => 'Home',
        'features' => 'Features',
        'pricing' => 'Pricing',
        'about' => 'About Us',
        'faq' => 'FAQ',
        'contact' => 'Contact',
        'blog.index' => 'Blog (listing page)',
        'privacy-policy' => 'Privacy Policy',
        'terms-of-service' => 'Terms of Service',
        'data-security' => 'Data Security',
    ];

    public static function forRoute(?string $routeName): ?self
    {
        if (! $routeName || ! array_key_exists($routeName, self::PAGES)) {
            return null;
        }

        return self::where('page_key', $routeName)->first();
    }
}
