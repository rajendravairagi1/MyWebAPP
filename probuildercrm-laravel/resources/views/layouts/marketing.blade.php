<!DOCTYPE html>
<html lang="en" data-theme="{{ \App\Models\SiteSetting::theme() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if ($__gaScript = \App\Models\SiteSetting::get('analytics_script'))
        {!! $__gaScript !!}
    @endif
    @php
        // Computed once so the <title>/description and the social-share
        // (Open Graph/Twitter) tags below always say the exact same thing
        // for a given page, instead of yielding the same section twice
        // and risking them drifting apart.
        $__pageTitle = trim($__env->yieldContent('title', config('site.name').' - Real Estate & Construction CRM Software for Builders'));
        $__pageDescription = trim($__env->yieldContent('description', config('site.short_description')));
        $__pageKeywords = trim($__env->yieldContent('keywords', ''));

        // Admin > SEO lets the title/description/keywords above be
        // overridden per page without touching code - a blank field there
        // means "keep the built-in default", so only non-empty overrides
        // win here.
        if ($__pageSeo = \App\Models\PageSeo::forRoute(request()->route()?->getName())) {
            // e() here (not raw) so this matches yieldContent()'s own
            // escaping - the {!! !!} below only skips a *second* escape,
            // it isn't a license to print unescaped input.
            $__pageTitle = filled($__pageSeo->meta_title) ? e($__pageSeo->meta_title) : $__pageTitle;
            $__pageDescription = filled($__pageSeo->meta_description) ? e($__pageSeo->meta_description) : $__pageDescription;
            $__pageKeywords = filled($__pageSeo->meta_keywords) ? e($__pageSeo->meta_keywords) : $__pageKeywords;
        }

        $__ogImage = asset('images/og-image.jpg');
        $__canonicalUrl = config('site.url').request()->getPathInfo();
    @endphp
    {{-- $__pageTitle/$__pageDescription/$__pageKeywords come out of
         yieldContent() already HTML-escaped (Blade's @section('title', '...')
         short form and yieldContent's own default both call e() internally)
         - {!! !!} here, not {{ }}, so they aren't escaped a second time
         (which previously turned "Builders & Developers" into
         "Builders &amp;amp; Developers"). --}}
    <title>{!! $__pageTitle !!}</title>
    <meta name="description" content="{!! $__pageDescription !!}">
    @if ($__pageKeywords)
        <meta name="keywords" content="{!! $__pageKeywords !!}">
    @endif
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $__canonicalUrl }}">
    @include('partials.favicon-links')

    {{-- Social share preview (WhatsApp, Facebook, LinkedIn, iMessage, ...) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:url" content="{{ $__canonicalUrl }}">
    <meta property="og:title" content="{!! $__pageTitle !!}">
    <meta property="og:description" content="{!! $__pageDescription !!}">
    <meta property="og:image" content="{{ $__ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ config('site.name') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{!! $__pageTitle !!}">
    <meta name="twitter:description" content="{!! $__pageDescription !!}">
    <meta name="twitter:image" content="{{ $__ogImage }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ @filemtime(public_path('css/app.css')) ?: '1' }}">
    @php
        $__startingPrice = (int) (\App\Models\PricingPlan::min('monthly_price') ?? 999);
    @endphp
    @include('partials.json-ld', ['schemas' => [
        \App\Support\Seo::organizationSchema(),
        \App\Support\Seo::softwareApplicationSchema($__startingPrice),
    ]])
    @stack('head')
</head>
<body @unless(request()->routeIs('contact')) class="has-sticky-cta" @endunless>
    @include('partials.navbar')

    @yield('content')

    @unless (request()->routeIs('blog.index') || request()->routeIs('faq') || request()->routeIs('about'))
        @include('partials.blog-preview')
    @endunless

    @include('partials.footer')

    @unless (request()->routeIs('contact'))
        @include('partials.sticky-cta')
    @endunless

    <a href="{{ config('site.whatsapp') }}" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp" class="floating-whatsapp">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.86 9.86 0 0012.04 2zm5.78 14.13c-.24.68-1.4 1.3-1.93 1.38-.49.08-1.11.11-1.79-.11-.41-.13-.94-.31-1.62-.6-2.85-1.23-4.71-4.1-4.85-4.29-.14-.19-1.16-1.54-1.16-2.93 0-1.4.73-2.08.99-2.36.26-.28.57-.35.76-.35.19 0 .38 0 .55.01.18.01.41-.07.64.49.24.57.81 1.98.88 2.12.07.14.12.31.02.5-.09.19-.14.31-.28.48-.14.17-.29.37-.42.5-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.61-.07.16-.19.7-.82.89-1.1.19-.28.38-.23.63-.14.26.09 1.64.77 1.92.91.28.14.47.21.54.33.07.12.07.68-.17 1.36z"/></svg>
    </a>

    <button type="button" id="scroll-top-btn" class="scroll-top-btn" aria-label="Back to top">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7"/></svg>
    </button>

    <script src="{{ asset('js/alpine.min.js') }}?v={{ @filemtime(public_path('js/alpine.min.js')) ?: '1' }}" defer></script>
    <script src="{{ asset('js/reveal.js') }}?v={{ @filemtime(public_path('js/reveal.js')) ?: '1' }}" defer></script>
    <script src="{{ asset('js/scroll-top.js') }}?v={{ @filemtime(public_path('js/scroll-top.js')) ?: '1' }}" defer></script>
    @stack('scripts')
</body>
</html>
