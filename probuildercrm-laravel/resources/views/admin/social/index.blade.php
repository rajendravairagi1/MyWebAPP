@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'social'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p class="form-error" style="margin-bottom: var(--space-md);">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('admin.social.update') }}">
            @csrf
            @method('PUT')

            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Footer description</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Shown under the logo in the site footer. Keep it short - 2 to 4 sentences reads best.
                </p>
                <div class="form-field">
                    <label for="footer_description">Description</label>
                    <textarea id="footer_description" name="footer_description" rows="4" maxlength="500" class="form-textarea">{{ old('footer_description', $footerDescription) }}</textarea>
                </div>
            </div>

            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Contact phone number</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Shown in the header and footer across the site. Leave blank to hide it.
                </p>
                <div class="form-field">
                    <label for="phone_number">Phone number</label>
                    <input id="phone_number" name="phone_number" value="{{ old('phone_number', $phoneNumber) }}" class="form-input" placeholder="+91 78980 02496">
                </div>
            </div>

            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Social media links</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Paste the full link for whichever platforms you use. Leave any blank to hide just that icon.
                </p>

                <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                    <div class="form-field">
                        <label for="social_facebook">Facebook</label>
                        <input id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $links['facebook']) }}" class="form-input" placeholder="https://facebook.com/yourpage">
                    </div>
                    <div class="form-field">
                        <label for="social_instagram">Instagram</label>
                        <input id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $links['instagram']) }}" class="form-input" placeholder="https://instagram.com/yourpage">
                    </div>
                    <div class="form-field">
                        <label for="social_linkedin">LinkedIn</label>
                        <input id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $links['linkedin']) }}" class="form-input" placeholder="https://linkedin.com/company/yourpage">
                    </div>
                    <div class="form-field">
                        <label for="social_twitter">Twitter / X</label>
                        <input id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $links['twitter']) }}" class="form-input" placeholder="https://x.com/yourpage">
                    </div>
                    <div class="form-field">
                        <label for="social_whatsapp">WhatsApp</label>
                        <input id="social_whatsapp" name="social_whatsapp" value="{{ old('social_whatsapp', $links['whatsapp']) }}" class="form-input" placeholder="https://wa.me/917898002496">
                    </div>
                </div>

                @if ($hasLinksButHidden)
                    <p style="background: #fef3c7; color: #92400e; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 0.88rem; margin-top: var(--space-md);">
                        You've added a link above, but "Show in header" and "Show in footer" are both off below - so nothing is showing on the site yet. Check at least one and Save.
                    </p>
                @endif

                <div style="display: flex; gap: var(--space-lg); margin-top: var(--space-md); flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="social_show_header" value="1" @checked(old('social_show_header', $showHeader))>
                        Show in header
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="social_show_footer" value="1" @checked(old('social_show_footer', $showFooter))>
                        Show in footer
                    </label>
                </div>
                <p style="color: var(--color-ink-soft); font-size: 0.82rem; margin-top: 8px;">
                    A link above only appears on the site once you check "Show in header" and/or "Show in footer" here.
                </p>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
