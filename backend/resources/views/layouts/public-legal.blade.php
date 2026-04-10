<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Legal')</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                :root {
                    --zanupf-green: #15803d;
                    --zanupf-gold: #facc15;
                    --zanupf-red: #b91c1c;
                    --bg: #020617;
                    --panel: rgba(15,23,42,0.95);
                    --text-main: #f9fafb;
                    --text-muted: #9ca3af;
                    --border: rgba(31,41,55,0.9);
                }
                * { box-sizing: border-box; }
                body {
                    margin: 0;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                    background: radial-gradient(1000px 600px at 10% 0%, rgba(21,128,61,0.22), transparent 50%),
                                radial-gradient(900px 560px at 80% 20%, rgba(250,204,21,0.14), transparent 55%),
                                radial-gradient(1000px 700px at 90% 90%, rgba(185,28,28,0.14), transparent 55%),
                                var(--bg);
                    color: var(--text-main);
                }
                .wrap {
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 2rem 1.25rem;
                }
                .card {
                    width: 100%;
                    max-width: 980px;
                    background: var(--panel);
                    border: 1px solid var(--border);
                    border-radius: 1rem;
                    box-shadow: 0 25px 55px rgba(0,0,0,0.45);
                    overflow: hidden;
                }
                .head {
                    padding: 1.25rem 1.35rem 1rem 1.35rem;
                    border-bottom: 1px solid var(--border);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                }
                .brand {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.6rem;
                    font-weight: 800;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    font-size: 0.9rem;
                }
                .mark {
                    width: 28px;
                    height: 28px;
                    border-radius: 0.65rem;
                    background: conic-gradient(from 180deg, var(--zanupf-green), var(--zanupf-gold), var(--zanupf-red), var(--zanupf-green));
                    border: 2px solid #020617;
                }
                .nav {
                    display: inline-flex;
                    gap: 0.9rem;
                    flex-wrap: wrap;
                    justify-content: flex-end;
                }
                .nav a {
                    color: var(--text-muted);
                    text-decoration: none;
                    border-bottom: 1px solid transparent;
                    padding-bottom: 2px;
                }
                .nav a:hover {
                    color: var(--zanupf-gold);
                    border-bottom-color: rgba(250,204,21,0.55);
                }
                .body {
                    padding: 1.25rem 1.35rem 1.35rem 1.35rem;
                    color: #e5e7eb;
                    line-height: 1.65;
                }
                .body h1 {
                    margin: 0 0 0.35rem 0;
                    color: var(--text-main);
                    font-size: 1.35rem;
                }
                .body h2, .body h3 {
                    color: var(--text-main);
                    margin-top: 1rem;
                }
                .foot {
                    padding: 0.9rem 1.35rem;
                    border-top: 1px solid var(--border);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    color: var(--text-muted);
                    font-size: 0.78rem;
                }
                .foot strong { color: var(--text-main); font-weight: 600; }
            </style>
        @endif
    </head>
    <body>
        <div class="wrap">
            <div class="card">
                <div class="head">
                    <div class="brand">
                        <span class="mark" aria-hidden="true"></span>
                        <span>ZANU PF</span>
                    </div>
                    <div class="nav">
                        <a href="{{ route('legal.privacy') }}">Privacy</a>
                        <a href="{{ route('legal.terms') }}">Terms</a>
                        <a href="{{ route('legal.cookies') }}">Cookies</a>
                    </div>
                </div>
                <div class="body">
                    @yield('content')
                </div>
                <div class="foot">
                    <div>© 2026, <strong>Created by TTM Group</strong>.</div>
                    <div>Legal information for the platform.</div>
                </div>
            </div>
        </div>
    </body>
</html>

