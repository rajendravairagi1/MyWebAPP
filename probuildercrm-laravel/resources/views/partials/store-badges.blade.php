@php
    $badgeStyle = 'display: flex; align-items: center; gap: 10px; background: #0b0e1a; border: 1px solid rgba(255,255,255,0.14); border-radius: 10px; padding: 9px 16px; text-decoration: none; min-width: 176px;';
@endphp

<div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
    <a href="{{ config('site.play_store_url') }}" target="_blank" rel="noopener" style="{{ $badgeStyle }}" aria-label="Get Pro Builder CRM on Google Play">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 3.5v17a1 1 0 0 0 1.53.85l14-8.5a1 1 0 0 0 0-1.7l-14-8.5A1 1 0 0 0 4 3.5z" fill="url(#pbcrm-play-grad)"/>
            <defs>
                <linearGradient id="pbcrm-play-grad" x1="4" y1="3" x2="20" y2="21" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#34d399"/>
                    <stop offset="0.5" stop-color="#60a5fa"/>
                    <stop offset="1" stop-color="#f59e0b"/>
                </linearGradient>
            </defs>
        </svg>
        <span>
            <span style="display: block; font-size: 0.7rem; color: var(--gray-400); line-height: 1.2;">GET IT ON</span>
            <span style="display: block; font-size: 1rem; font-weight: 600; color: #fff; line-height: 1.3;">Google Play</span>
        </span>
    </a>

    <a href="{{ route('ios-install') }}" style="{{ $badgeStyle }}" aria-label="Install Pro Builder CRM on iPhone - App Store version coming soon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6" aria-hidden="true">
            <rect x="6" y="2.5" width="12" height="19" rx="2.5"/>
            <line x1="10.3" y1="19" x2="13.7" y2="19" stroke-linecap="round"/>
        </svg>
        <span>
            <span style="display: block; font-size: 0.7rem; color: var(--gray-400); line-height: 1.2;">iOS APP COMING SOON</span>
            <span style="display: block; font-size: 1rem; font-weight: 600; color: #fff; line-height: 1.3;">Add to iPhone</span>
        </span>
    </a>
</div>
