<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name', 'App') }}</title>
    <style>
        :root { color-scheme: light dark; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            margin: 0; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
            background: #0f172a;
            color: #e2e8f0;
        }
        .card {
            max-width: 28rem;
            text-align: center;
            line-height: 1.5;
        }
        h1 { font-size: 1.25rem; font-weight: 600; margin: 0 0 0.75rem; color: #f8fafc; }
        p { margin: 0; font-size: 0.95rem; color: #94a3b8; }
        a {
            display: inline-block; margin-top: 1.25rem;
            color: #facc15; text-decoration: none; font-weight: 500;
        }
        a:hover { text-decoration: underline; }
        .code { font-size: 0.75rem; color: #64748b; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        @yield('content')
        @hasSection('code')
            <p class="code">@yield('code')</p>
        @endif
    </div>
</body>
</html>
