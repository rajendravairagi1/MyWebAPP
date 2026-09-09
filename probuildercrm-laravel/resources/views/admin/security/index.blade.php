@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 700px;">
        @include('admin.partials.tabs', ['active' => 'security'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        @if ($freshBackupCodes)
            <div class="card" style="margin-bottom: var(--space-lg); border: 2px solid #f59e0b;">
                <strong style="display: block; margin-bottom: 8px;">Save your backup codes now</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Each code works once, to get back into Admin if you lose your phone. This is the only time they're
                    shown - write them down or save them somewhere safe (not on the phone with the authenticator app).
                </p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-family: monospace; font-size: 1rem; background: var(--color-bg-soft); border-radius: var(--radius-sm); padding: 16px;">
                    @foreach ($freshBackupCodes as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($qr)
            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Set up two-factor authentication</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Scan this with Google Authenticator, Authy, or any authenticator app - or enter the key manually if
                    you can't scan. Then type the 6-digit code it shows to turn 2FA on.
                </p>
                <div id="qr-canvas" style="display: flex; justify-content: center; margin-bottom: var(--space-md);"></div>
                <p style="text-align: center; font-family: monospace; font-size: 0.95rem; letter-spacing: 0.05em; word-break: break-all; margin-bottom: var(--space-lg);">
                    {{ $qr['secret'] }}
                </p>

                <form method="POST" action="{{ route('admin.security.confirm') }}" style="display: flex; flex-direction: column; gap: 12px; max-width: 240px; margin: 0 auto;">
                    @csrf
                    <div class="form-field">
                        <label for="code">6-digit code</label>
                        <input id="code" name="code" type="text" inputmode="numeric" required autofocus class="form-input" placeholder="000000">
                    </div>
                    @error('code')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn btn-primary" style="justify-content: center;">Confirm & Turn On</button>
                </form>
                <form method="POST" action="{{ route('admin.security.cancel') }}" style="text-align: center; margin-top: 12px;">
                    @csrf
                    <button type="submit" class="mini-btn">Cancel</button>
                </form>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
            <script>
                try {
                    new QRCode(document.getElementById('qr-canvas'), {
                        text: @json($qr['otpauth']),
                        width: 200,
                        height: 200,
                    });
                } catch (e) {
                    // No network / CDN blocked - the manual key above still works.
                }
            </script>
        @elseif ($enabled)
            <div class="card" style="margin-bottom: var(--space-lg);">
                <strong style="display: block; margin-bottom: 8px;">Two-factor authentication is ON</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Logging into Admin now needs your password and a code from your authenticator app.
                </p>
                <form method="POST" action="{{ route('admin.security.backup-codes') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('This replaces your old backup codes - they\'ll stop working. Continue?')">
                        Generate New Backup Codes
                    </button>
                </form>
            </div>

            <div class="card">
                <strong style="display: block; margin-bottom: 8px;">Turn off two-factor authentication</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Confirm your password to turn 2FA off. Only do this if you're sure - it's what keeps someone who
                    guesses your password out of Admin.
                </p>
                <form method="POST" action="{{ route('admin.security.disable') }}" style="display: flex; gap: 12px; max-width: 320px;">
                    @csrf
                    <input name="password" type="password" required class="form-input" placeholder="Current password">
                    <button type="submit" class="btn btn-secondary" style="flex-shrink: 0;">Turn Off</button>
                </form>
                @error('password')
                    <p class="form-error" style="margin-top: 8px;">{{ $message }}</p>
                @enderror
            </div>
        @else
            <div class="card">
                <strong style="display: block; margin-bottom: 8px;">Two-factor authentication is off</strong>
                <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                    Add a second step to Admin login - your password plus a 6-digit code from an authenticator app
                    (Google Authenticator, Authy, etc.) on your phone. Recommended, since the password alone is what
                    stands between anyone and full control of the site.
                </p>
                <form method="POST" action="{{ route('admin.security.start') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Enable Two-Factor Authentication</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
