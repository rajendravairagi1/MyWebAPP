<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <span class="logo logo-light">
                    @if ($logoPath = \App\Models\SiteSetting::get('logo_path'))
                        <img src="{{ asset($logoPath) }}" alt="Pro Builder CRM" class="logo-image" style="height: {{ \App\Http\Controllers\Admin\BrandingController::pixelsFor(\App\Models\SiteSetting::get('logo_size')) }}px;">
                    @else
                        <span class="logo-mark">P</span>
                        Pro Builder <span class="logo-accent">CRM</span>
                    @endif
                </span>
                <p style="margin-top: 12px; max-width: 320px; color: var(--gray-300); font-size: 0.9rem;">
                    Pro Builder CRM is the all-in-one platform built for real estate builders and developers.
                    Track every project, unit and customer payment in one place. Manage loans, invoices,
                    contractors, brokers and investors without spreadsheets or scattered WhatsApp chats -
                    built to stay organized as your business grows.
                </p>
                @php $__footerPhone = \App\Models\SiteSetting::get('phone_number'); @endphp
                @if ($__footerPhone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $__footerPhone) }}" style="display: block; margin-top: 12px; color: var(--gray-300); font-size: 0.9rem; text-decoration: none;">{{ $__footerPhone }}</a>
                @endif
                <div style="margin-top: 12px;">
                    @include('partials.social-links', ['location' => 'footer'])
                </div>
            </div>

            @foreach (config('site.footer_columns') as $column)
                <div>
                    <h4>{{ $column['heading'] }}</h4>
                    <div class="footer-links">
                        @foreach ($column['links'] as $link)
                            <a href="{{ url($link['href']) }}">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="footer-bottom">
            &copy; 2022 &ndash; {{ now()->year }} {{ config('site.name') }}. All rights reserved.
            &middot; Designed &amp; Developed by {{ config('site.legal_name') }}
        </div>
    </div>
</footer>
