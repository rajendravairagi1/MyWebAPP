<?php

namespace App\Support;

use App\Models\BlogPost;

class Seo
{
    public static function organizationSchema(): array
    {
        $site = config('site');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $site['name'],
            'url' => $site['url'],
            'email' => $site['email'],
            'telephone' => $site['phone_href'],
        ];
    }

    public static function softwareApplicationSchema(int $startingPrice): array
    {
        $site = config('site');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $site['name'],
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'description' => $site['short_description'],
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) $startingPrice,
                'priceCurrency' => 'INR',
                'url' => rtrim($site['url'], '/').'/pricing',
            ],
            'url' => $site['url'],
        ];
    }

    public static function faqSchema(array $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ], $faqs),
        ];
    }

    public static function breadcrumbSchema(array $items): array
    {
        $siteUrl = rtrim(config('site.url'), '/');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $siteUrl.$item['href'],
            ])->all(),
        ];
    }

    public static function articleSchema(BlogPost $post): array
    {
        $site = config('site');
        $siteUrl = rtrim($site['url'], '/');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'datePublished' => $post->date->toDateString(),
            'dateModified' => $post->updated_at->toDateString(),
            'author' => ['@type' => 'Person', 'name' => $post->author],
            'publisher' => ['@type' => 'Organization', 'name' => $site['name']],
            'mainEntityOfPage' => "{$siteUrl}/blog/{$post->slug}",
        ];
    }
}
