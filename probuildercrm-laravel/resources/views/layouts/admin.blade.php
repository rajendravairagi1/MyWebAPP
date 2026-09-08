<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Builder CRM — Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header style="border-bottom: 1px solid var(--color-border); background: #fff;">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between; height: 64px;">
            <strong>Pro Builder CRM — Admin</strong>
            <a href="{{ url('/') }}" style="text-decoration: none; color: var(--color-ink-soft); font-size: 0.9rem;">&larr; Back to site</a>
        </div>
    </header>

    @yield('content')

    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <script src="{{ asset('js/upload-progress.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
