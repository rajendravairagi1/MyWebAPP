<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <span class="logo logo-light">
                    <span class="logo-mark">P</span>
                    Pro Builder <span class="logo-accent">CRM</span>
                </span>
                <p style="margin-top: 12px; max-width: 320px; color: var(--gray-300); font-size: 0.9rem;">
                    The all-in-one CRM built for real estate builders and developers.
                </p>
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
            &copy; {{ now()->year }} {{ config('site.legal_name') }}. All rights reserved.
        </div>
    </div>
</footer>
