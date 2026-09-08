@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'integrations'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p class="form-error" style="margin-bottom: var(--space-md);">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('admin.integrations.update') }}">
            @csrf
            @method('PUT')

            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Google reCAPTCHA (Contact form)</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Stops spam/bot submissions on the Contact page. Get a free reCAPTCHA v2 ("I'm not a robot" checkbox)
                    site key + secret key from
                    <a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener">google.com/recaptcha/admin</a>
                    - register your domain (probuildercrm.com), choose reCAPTCHA v2, and paste both keys below.
                    Leave both blank to turn it off - the form works as before, with no checkbox.
                </p>

                <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                    <div class="form-field">
                        <label for="recaptcha_site_key">Site Key</label>
                        <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" value="{{ old('recaptcha_site_key', $recaptchaSiteKey) }}" class="form-input" placeholder="6Lc...">
                    </div>
                    <div class="form-field">
                        <label for="recaptcha_secret_key">Secret Key</label>
                        <input type="text" id="recaptcha_secret_key" name="recaptcha_secret_key" value="{{ old('recaptcha_secret_key', $recaptchaSecretKey) }}" class="form-input" placeholder="6Lc...">
                    </div>
                </div>

                <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin-top: var(--space-md);">
                    Status: {{ $recaptchaSiteKey && $recaptchaSecretKey ? 'reCAPTCHA is ON - the Contact form shows the checkbox.' : 'reCAPTCHA is OFF - both keys are needed to turn it on.' }}
                </p>
            </div>

            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Demo Request Notifications</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Every time someone submits the Contact / Book a Demo form, it's saved under the "Demo Requests" tab
                    and an email is sent to the address(es) below. Add more than one by separating them with a comma.
                    Leave blank to turn email notifications off (submissions still get saved either way).
                </p>

                <div class="form-field">
                    <label for="notification_emails">Notify these emails</label>
                    <input type="text" id="notification_emails" name="notification_emails" value="{{ old('notification_emails', $notificationEmails) }}" class="form-input" placeholder="you@example.com, teammate@example.com">
                </div>

                <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin-top: var(--space-md);">
                    Status: {{ $notificationEmails ? 'Notifications are ON.' : 'Notifications are OFF - add an email above to turn them on.' }}
                </p>
            </div>

            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Google Analytics</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Go to <a href="https://analytics.google.com" target="_blank" rel="noopener">analytics.google.com</a> →
                    Admin → Data Streams → your web stream, and copy the full tracking snippet it gives you
                    (starts with <code>&lt;script async src="https://www.googletagmanager.com/gtag/js...</code>).
                    Paste the whole thing below - it gets added to every page automatically. Leave blank to turn it off.
                </p>

                <div class="form-field">
                    <label for="analytics_script">Tracking code</label>
                    <textarea id="analytics_script" name="analytics_script" rows="6" class="form-textarea" style="font-family: monospace; font-size: 0.82rem;" placeholder="<!-- Google tag (gtag.js) -->&#10;<script async src=&quot;https://www.googletagmanager.com/gtag/js?id=G-XXXXXXX&quot;></script>&#10;<script>...</script>">{{ old('analytics_script', $analyticsScript) }}</textarea>
                </div>

                <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin-top: var(--space-md);">
                    Status: {{ $analyticsScript ? 'Analytics is ON - tracking code is live on every page.' : 'Analytics is OFF - paste your tracking code above to turn it on.' }}
                </p>
            </div>

            <div style="margin-bottom: var(--space-lg);">
                <button type="submit" class="btn btn-primary">Save Integrations</button>
            </div>
        </form>

        <div class="card" style="margin-bottom: var(--space-lg);">
            <strong style="display: block; margin-bottom: 8px;">Sitemap submission</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                <code>/sitemap.xml</code> already updates itself automatically - every time you publish or edit a blog
                post, it's submitted to Google &amp; Bing right away (no extra step needed). Use the button below only
                if you want to trigger that submission manually, right now.
            </p>

            <form method="POST" action="{{ route('admin.integrations.ping-sitemap') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Submit Sitemap Now</button>
            </form>

            <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin-top: var(--space-md);">
                {{ $sitemapLastPingedAt ? 'Last submitted: '.\Illuminate\Support\Carbon::parse($sitemapLastPingedAt)->diffForHumans() : 'Not submitted yet.' }}
            </p>
        </div>
    </div>
</div>
@endsection
