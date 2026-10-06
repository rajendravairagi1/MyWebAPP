@extends('layouts.marketing')

@section('title', 'Install Pro Builder CRM on iPhone')
@section('description', 'The iOS app is coming soon to the App Store. Until then, add Pro Builder CRM to your iPhone home screen in two taps.')

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 640px;">
        <p class="eyebrow">iOS</p>
        <h1>Pro Builder CRM for iPhone</h1>
        <p class="body-lg">
            Our App Store app is coming soon. Until it's live, you can add Pro Builder CRM to your iPhone's home
            screen right now - it opens and works just like an app, with its own icon.
        </p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 640px;">
        <div class="card" style="display: flex; flex-direction: column; gap: var(--space-lg); padding: var(--space-xl);">
            <div style="display: flex; gap: 16px; align-items: flex-start;">
                <span style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; background: var(--color-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">1</span>
                <div>
                    <strong>Open the app in Safari</strong>
                    <p style="margin-top: 4px; color: var(--color-ink-soft);">Tap the button below - it must be opened in Safari for this to work (not Chrome or another browser).</p>
                </div>
            </div>
            <div style="display: flex; gap: 16px; align-items: flex-start;">
                <span style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; background: var(--color-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">2</span>
                <div>
                    <strong>Tap the Share icon</strong>
                    <p style="margin-top: 4px; color: var(--color-ink-soft);">It's the square with an arrow pointing up, in Safari's toolbar.</p>
                </div>
            </div>
            <div style="display: flex; gap: 16px; align-items: flex-start;">
                <span style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; background: var(--color-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">3</span>
                <div>
                    <strong>Choose "Add to Home Screen"</strong>
                    <p style="margin-top: 4px; color: var(--color-ink-soft);">Scroll down the share menu if you don't see it right away, then tap Add.</p>
                </div>
            </div>

            <a href="{{ config('site.app_url') }}" class="btn btn-primary btn-lg" style="text-align: center; margin-top: var(--space-md);">
                Open Pro Builder CRM in Safari
            </a>
        </div>
    </div>
</section>

@endsection
