<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSeo;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        $existing = PageSeo::whereIn('page_key', array_keys(PageSeo::PAGES))->get()->keyBy('page_key');

        $pages = collect(PageSeo::PAGES)->map(fn ($label, $key) => [
            'key' => $key,
            'label' => $label,
            'meta_title' => $existing[$key]->meta_title ?? '',
            'meta_description' => $existing[$key]->meta_description ?? '',
            'meta_keywords' => $existing[$key]->meta_keywords ?? '',
        ])->values();

        return view('admin.seo.index', compact('pages'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'pages' => ['required', 'array'],
            'pages.*' => ['array'],
            'pages.*.meta_title' => ['nullable', 'string', 'max:255'],
            'pages.*.meta_description' => ['nullable', 'string', 'max:500'],
            'pages.*.meta_keywords' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach ($validated['pages'] as $pageKey => $fields) {
            if (! array_key_exists($pageKey, PageSeo::PAGES)) {
                continue;
            }

            PageSeo::updateOrCreate(['page_key' => $pageKey], [
                'meta_title' => $fields['meta_title'] ?? null,
                'meta_description' => $fields['meta_description'] ?? null,
                'meta_keywords' => $fields['meta_keywords'] ?? null,
            ]);
        }

        return redirect()->route('admin.seo.index')->with('status', 'SEO settings saved.');
    }
}
