<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Faq;
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

        // Built as a plain string rather than a Blade view: the leading XML
        // declaration was being parsed as a PHP open tag on hosts with
        // short_open_tag enabled, and depending on a compiled Blade view
        // here also meant a stale cached copy in storage/framework/views
        // could keep serving the broken version after a fix was deployed.
        // This has no compiled-view step at all.
        $xml = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($all as $url) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . e($url['loc']) . '</loc>' . "\n";
            $xml .= '        <lastmod>' . e($url['lastmod']) . '</lastmod>' . "\n";
            $xml .= '    </url>' . "\n";
        }
        $xml .= '</urlset>';

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $siteUrl = rtrim(config('site.url'), '/');

        // Every crawler defaults to allowed via the wildcard block below,
        // so none of these named entries change access — they're listed
        // explicitly because several AI/answer-engine crawlers (and the
        // audits some of them publish) specifically check for their own
        // name rather than trusting the wildcard, and because it makes the
        // "yes, this is intentionally open to AI" reachable to a human
        // skimming the file too.
        $aiCrawlers = [
            'GPTBot', 'ChatGPT-User', 'OAI-SearchBot',        // OpenAI / ChatGPT
            'ClaudeBot', 'Claude-Web', 'anthropic-ai',        // Anthropic / Claude
            'Google-Extended', 'GoogleOther',                 // Google AI (Gemini/AI Overviews)
            'PerplexityBot', 'Perplexity-User',               // Perplexity
            'Meta-ExternalAgent', 'FacebookBot',              // Meta AI
            'Bytespider',                                     // ByteDance / TikTok
            'Amazonbot',                                      // Amazon
            'Applebot', 'Applebot-Extended',                  // Apple Intelligence / Siri
            'YouBot',                                         // You.com
            'cohere-ai',                                      // Cohere
            'Diffbot',                                        // Diffbot
            'DuckAssistBot',                                  // DuckDuckGo AI
            'CCBot',                                          // Common Crawl (feeds many LLMs' training data)
        ];

        $lines = ['User-agent: *', 'Allow: /', 'Disallow: /admin', ''];

        foreach ($aiCrawlers as $bot) {
            $lines[] = "User-agent: {$bot}";
            $lines[] = 'Allow: /';
            $lines[] = 'Disallow: /admin';
            $lines[] = '';
        }

        $lines[] = "Sitemap: {$siteUrl}/sitemap.xml";

        return Response::make(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }

    public function llms()
    {
        $siteUrl = rtrim(config('site.url'), '/');
        $site = config('site');
        $faqs = Faq::orderBy('sort_order')->get();
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
