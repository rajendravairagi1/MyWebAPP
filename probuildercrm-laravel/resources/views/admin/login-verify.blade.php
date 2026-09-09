<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Pro Builder CRM</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--color-bg-soft);">
        <form method="POST" action="{{ route('admin.login.verify.submit') }}" class="card" style="width: 100%; max-width: 360px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <h1 style="font-size: 1.3rem;">Enter your code</h1>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem;">
                Open your authenticator app and enter the current 6-digit code - or use one of your backup codes.
            </p>
            <div class="form-field">
                <label for="code">Code</label>
                <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required autofocus
                       class="form-input" style="letter-spacing: 0.1em; font-size: 1.1rem;" placeholder="000000">
            </div>
            @error('code')
                <p class="form-error">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn btn-primary" style="justify-content: center;">Verify</button>
        </form>
    </div>
</body>
</html>
