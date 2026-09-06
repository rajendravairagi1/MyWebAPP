<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    public function sitemap()
    {
        $siteUrl = rtrim(config('site.url'), '/');

        $staticRoutes = ['', '/features', '/pricing', '/about', '/blog', '/faq', '/contact', '/privacy-policy', '/terms-of-service'];

        $urls = collect($staticRoutes)->map(fn ($route) => [
            'loc' => $siteUrl.$route,
            'lastmod' => now()->toAtomString(),
        ]);

        $blogUrls = BlogPost::orderByDesc('date')->get()->map(fn ($post) => [
            'loc' => "{$siteUrl}/blog/{$post->slug}",
            'lastmod' => $post->updated_at->toAtomString(),
        ]);

        $all = $urls->merge($blogUrls);

        $xml = view('seo.sitemap', ['urls' => $all])->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $siteUrl = rtrim(config('site.url'), '/');

        $content = "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: {$siteUrl}/sitemap.xml\n";

        return Response::make($content, 200, ['Content-Type' => 'text/plain']);
    }

    public function llms()
    {
        $siteUrl = rtrim(config('site.url'), '/');
        $site = config('site');
        $faqs = config('faqs');
        $posts = BlogPost::orderByDesc('date')->limit(20)->get();

        $lines = [
            "# {$site['name']}",
            '',
            "> {$site['short_description']}",
            '',
            "Website: {$siteUrl}",
            "Contact: {$site['email']}",
            '',
            '## Key pages',
            "- Features: {$siteUrl}/features",
            "- Pricing: {$siteUrl}/pricing",
            "- FAQ: {$siteUrl}/faq",
            "- About: {$siteUrl}/about",
            "- Blog: {$siteUrl}/blog",
            '',
            '## Frequently asked questions',
        ];

        foreach ($faqs as $faq) {
            $lines[] = "Q: {$faq['question']}\nA: {$faq['answer']}\n";
        }

        if ($posts->isNotEmpty()) {
            $lines[] = '## Recent blog posts';
            foreach ($posts as $post) {
                $lines[] = "- {$post->title}: {$siteUrl}/blog/{$post->slug}";
            }
        }

        return Response::make(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
