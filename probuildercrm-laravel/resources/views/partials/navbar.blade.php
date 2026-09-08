@php
    $currentPath = request()->getPathInfo();
    $__navPhone = \App\Models\SiteSetting::get('phone_number');
    $__navShowSocial = \App\Models\SiteSetting::get('social_show_header');
@endphp
@if ($__navPhone || $__navShowSocial)
    <div class="navbar-topbar">
        <div class="container navbar-topbar-inner">
            @if ($__navPhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $__navPhone) }}" class="navbar-phone">{{ $__navPhone }}</a>
            @endif
            @include('partials.social-links', ['location' => 'header'])
        </div>
    </div>
@endif
<header class="navbar" x-data="{ open: false }">
    <div class="container">
        <a href="{{ url('/') }}" class="logo">
            @if ($logoPath = \App\Models\SiteSetting::get('logo_path'))
                <img src="{{ asset($logoPath) }}" alt="Pro Builder CRM" class="logo-image" style="height: {{ \App\Http\Controllers\Admin\BrandingController::pixelsFor(\App\Models\SiteSetting::get('logo_size')) }}px;">
            @else
                <span class="logo-mark">P</span>
                Pro Builder <span class="logo-accent">CRM</span>
            @endif
        </a>

        <nav class="nav-desktop">
            @foreach (config('site.nav_items') as $item)
                <a href="{{ url($item['href']) }}" class="nav-link">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ route('contact') }}" class="btn btn-primary">Book a Free Demo</a>
        </nav>

        <button type="button" aria-label="Toggle menu" @click="open = !open" class="nav-toggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="nav-mobile-panel" x-show="open" x-cloak style="display: none;">
        <div class="container">
            @foreach (config('site.nav_items') as $item)
                <a href="{{ url($item['href']) }}" @click="open = false">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ route('contact') }}" @click="open = false" class="btn btn-primary" style="margin-top: 8px; width: 100%;">Book a Free Demo</a>
        </div>
    </div>
</header>
