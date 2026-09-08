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

        <div class="card" style="margin-bottom: var(--space-lg);">
            <strong style="display: block; margin-bottom: 8px;">Google reCAPTCHA (Contact form)</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                Stops spam/bot submissions on the Contact page. Get a free reCAPTCHA v2 ("I'm not a robot" checkbox)
                site key + secret key from
                <a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener">google.com/recaptcha/admin</a>
                — register your domain (probuildercrm.com), choose reCAPTCHA v2, and paste both keys below.
                Leave both blank to turn it off — the form works as before, with no checkbox.
            </p>

            <form method="POST" action="{{ route('admin.integrations.update') }}" style="display: flex; flex-direction: column; gap: var(--space-md);">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="recaptcha_site_key">Site Key</label>
                    <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" value="{{ old('recaptcha_site_key', $recaptchaSiteKey) }}" class="form-input" placeholder="6Lc...">
                </div>
                <div class="form-field">
                    <label for="recaptcha_secret_key">Secret Key</label>
                    <input type="text" id="recaptcha_secret_key" name="recaptcha_secret_key" value="{{ old('recaptcha_secret_key', $recaptchaSecretKey) }}" class="form-input" placeholder="6Lc...">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>

        <p style="color: var(--color-ink-soft); font-size: 0.85rem;">
            Status: {{ $recaptchaSiteKey && $recaptchaSecretKey ? 'reCAPTCHA is ON — the Contact form shows the checkbox.' : 'reCAPTCHA is OFF — both keys are needed to turn it on.' }}
        </p>
    </div>
</div>
@endsection
