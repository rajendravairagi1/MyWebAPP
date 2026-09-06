<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Pro Builder CRM</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--color-bg-soft);">
        <form method="POST" action="{{ route('admin.login.submit') }}" class="card" style="width: 100%; max-width: 360px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <h1 style="font-size: 1.3rem;">Admin Login</h1>
            <div class="form-field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autofocus class="form-input">
            </div>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn btn-primary" style="justify-content: center;">Log In</button>
        </form>
    </div>
</body>
</html>
