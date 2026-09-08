<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProBuilderCRM - Admin</title>
    <meta name="robots" content="noindex, nofollow">
    @include('partials.favicon-links')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-body">
    @php
        $__adminNav = [
            ['route' => 'admin.posts.index', 'match' => 'admin.posts.*', 'label' => 'Blog Posts', 'icon' => 'file-text'],
            ['route' => 'admin.leads.index', 'match' => 'admin.leads.*', 'label' => 'Demo Requests', 'icon' => 'user-plus'],
            ['route' => 'admin.pricing.index', 'match' => 'admin.pricing.*', 'label' => 'Pricing', 'icon' => 'receipt'],
            ['route' => 'admin.testimonials.index', 'match' => 'admin.testimonials.*', 'label' => 'Testimonials', 'icon' => 'star'],
            ['route' => 'admin.faqs.index', 'match' => 'admin.faqs.*', 'label' => 'FAQs', 'icon' => 'help-circle'],
            ['route' => 'admin.theme.index', 'match' => 'admin.theme.*', 'label' => 'Theme', 'icon' => 'sliders'],
            ['route' => 'admin.branding.index', 'match' => 'admin.branding.*', 'label' => 'Branding', 'icon' => 'image'],
            ['route' => 'admin.social.index', 'match' => 'admin.social.*', 'label' => 'Social & Contact', 'icon' => 'share-2'],
            ['route' => 'admin.integrations.index', 'match' => 'admin.integrations.*', 'label' => 'Integrations', 'icon' => 'plug'],
            ['route' => 'admin.maintenance.index', 'match' => 'admin.maintenance.*', 'label' => 'Maintenance', 'icon' => 'wrench'],
        ];
        $__adminLogoPath = \App\Models\SiteSetting::get('logo_path');
    @endphp

    <aside class="admin-sidebar">
        <a href="{{ route('admin.posts.index') }}" class="admin-sidebar-logo">
            @if ($__adminLogoPath)
                <img src="{{ asset($__adminLogoPath) }}" alt="ProBuilderCRM" class="admin-sidebar-logo-image">
            @else
                <span class="logo-mark">P</span>
            @endif
            <span>ProBuilder<span class="logo-accent">CRM</span></span>
        </a>

        <nav class="admin-nav">
            @foreach ($__adminNav as $item)
                <a href="{{ route($item['route']) }}" class="admin-nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                    @include('partials.icon', ['name' => $item['icon'], 'size' => 18])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="admin-sidebar-footer">
            <a href="{{ url('/') }}" class="admin-nav-link">
                @include('partials.icon', ['name' => 'arrow-left', 'size' => 18])
                <span>Back to site</span>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin-nav-link admin-nav-link-btn">
                    @include('partials.icon', ['name' => 'log-out', 'size' => 18])
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        @yield('content')
    </main>

    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <script src="{{ asset('js/upload-progress.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
