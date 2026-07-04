<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Study the Constitution, complete academy courses, and manage membership on the ZANU PF digital platform.')">
    <title>@yield('title', $orgName ?? 'ZANU PF Academy')</title>
    @include('partials.favicon')
    <style>
        :root {
            --green: #166534;
            --green-bright: #15803d;
            --gold: #facc15;
            --red: #b91c1c;
            --ink: #0f172a;
            --ink-soft: #334155;
            --muted: #64748b;
            --line: rgba(255, 255, 255, 0.12);
            --panel: rgba(15, 23, 42, 0.92);
            --panel-border: rgba(250, 204, 21, 0.22);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            color: #f8fafc;
            background:
                radial-gradient(900px 520px at 8% -10%, rgba(21, 128, 61, 0.35), transparent 55%),
                radial-gradient(760px 480px at 92% 8%, rgba(250, 204, 21, 0.14), transparent 50%),
                radial-gradient(900px 600px at 50% 110%, rgba(185, 28, 28, 0.18), transparent 55%),
                #020617;
            min-height: 100vh;
        }
        a { color: inherit; }
        .skip-to-main {
            position: absolute;
            left: -9999px;
            top: 0.75rem;
            z-index: 10000;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background: var(--gold);
            color: #020617;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .skip-to-main:focus {
            left: 0.75rem;
            outline: 2px solid var(--gold);
            outline-offset: 2px;
        }
        .site-header {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(12px);
            background: rgba(2, 6, 23, 0.82);
            border-bottom: 1px solid var(--line);
        }
        .site-header-inner,
        .site-main,
        .site-footer-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }
        .site-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 4rem;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            min-width: 0;
        }
        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        }
        .brand-logo img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }
        .brand-text {
            min-width: 0;
        }
        .brand-name {
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .brand-tag {
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .site-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.55rem 1.1rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: filter 0.15s ease, border-color 0.15s ease, background 0.15s ease;
        }
        .btn:hover { filter: brightness(1.06); }
        .btn-ghost {
            background: transparent;
            border-color: rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
        }
        .btn-ghost:hover { border-color: var(--gold); color: #fff; }
        .btn-primary {
            background: var(--green-bright);
            border-color: #14532d;
            color: #fff;
        }
        .btn-accent {
            background: linear-gradient(135deg, var(--gold), #fde047);
            border-color: #ca8a04;
            color: #1e293b;
        }
        .btn-outline-gold {
            background: transparent;
            border-color: rgba(250, 204, 21, 0.55);
            color: var(--gold);
        }
        .site-main { padding: 2.5rem 1.25rem 3.5rem; }
        .site-footer {
            border-top: 1px solid var(--line);
            background: rgba(2, 6, 23, 0.65);
            padding: 1.75rem 0 2.25rem;
        }
        .site-footer-inner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1.25rem;
        }
        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
        }
        .footer-links a:hover { color: var(--gold); text-decoration: underline; }
        @media (max-width: 640px) {
            .brand-tag { display: none; }
            .site-nav .btn { padding: 0.5rem 0.85rem; font-size: 0.8rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <a href="#main-content" class="skip-to-main">Skip to main content</a>

    <header class="site-header">
        <div class="site-header-inner">
            <a href="{{ url('/') }}" class="brand">
                <span class="brand-logo">
                    @if (file_exists(public_path('Logo.png')))
                        <img src="{{ asset('Logo.png') }}" alt="">
                    @else
                        <span aria-hidden="true" style="font-weight:800;color:var(--green);font-size:0.75rem;">ZPF</span>
                    @endif
                </span>
                <span class="brand-text">
                    <span class="brand-name">{{ $orgName ?? 'ZANU PF Academy' }}</span>
                    <span class="brand-tag">Constitution &amp; Academy</span>
                </span>
            </a>

            <nav class="site-nav" aria-label="Primary">
                @auth
                    @if ($navCta ?? null)
                        <a href="{{ $navCta['url'] }}" class="btn btn-{{ $navCta['variant'] }}">{{ $navCta['label'] }}</a>
                    @endif
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-ghost">Create account</a>
                    @endif
                @endauth
                <a href="{{ route('login') }}" class="btn btn-{{ auth()->check() ? 'ghost' : 'primary' }}">Log in</a>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1" class="site-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer-inner">
            <span>
                &copy; {{ date('Y') }} {{ $orgName ?? 'ZANU PF Academy' }}. All rights reserved.
                @if (! empty($supportEmail))
                    <span style="display:block;margin-top:0.25rem;">Support: <a href="mailto:{{ $supportEmail }}" style="color:#cbd5e1;">{{ $supportEmail }}</a></span>
                @endif
            </span>
            <div class="footer-links">
                <a href="{{ route('certificate.verify') }}">Verify certificate</a>
                <a href="{{ route('legal.privacy') }}">Privacy</a>
                <a href="{{ route('legal.terms') }}">Terms</a>
                <a href="{{ route('legal.cookies') }}">Cookies</a>
            </div>
        </div>
    </footer>
</body>
</html>
