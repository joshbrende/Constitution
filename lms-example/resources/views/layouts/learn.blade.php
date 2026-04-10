<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Learn') – {{ config('app.name') }}</title>
    <meta name="description" content="{{ config('brand.description') }}">
    <link rel="canonical" href="{{ config('app.url') }}{{ request()->getRequestUri() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --lms-accent: #dc3545; --lms-sidebar-width: 320px; }
        body { overflow: hidden; height: 100vh; }
        .learn-topbar { background: #2c2c2c; height: 48px; }
        .learn-sidebar { width: var(--lms-sidebar-width); background: #fff; border-right: 1px solid #dee2e6; overflow-y: auto; }
        .learn-course-hero { background: linear-gradient(180deg, rgba(0,0,0,.5) 0%, transparent 100%), #4a4a4a; background-size: cover; background-position: center; color: #fff; padding: 1.5rem; min-height: 140px; }
        .learn-course-hero h2 { font-size: 1.25rem; font-weight: 700; margin: 0 0 .5rem; }
        .learn-progress-pct { font-size: .75rem; opacity: .9; }
        .learn-progress-bar { height: 4px; background: rgba(255,255,255,.3); border-radius: 2px; overflow: hidden; }
        .learn-progress-bar .fill { height: 100%; background: #fff; border-radius: 2px; }
        .learn-standalones { border-bottom: 1px solid #eee; }
        .learn-day { border-bottom: 1px solid #eee; }
        .learn-day-header { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #495057; padding: .65rem 1rem; background: #f0f2f5; }
        .learn-trailing { border-top: 1px solid #eee; }
        .learn-module { border-bottom: 1px solid #eee; }
        .learn-module-header { display: flex; align-items: center; gap: .35rem; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; padding: .65rem 1rem; cursor: pointer; user-select: none; transition: background .15s; }
        .learn-module-header:hover { background: #f8f9fa; color: #495057; }
        .learn-module-header .learn-module-caret { font-size: .65rem; transition: transform .2s; }
        .learn-module.collapsed .learn-module-caret { transform: rotate(-90deg); }
        .learn-module-lessons { overflow: hidden; }
        .learn-module.collapsed .learn-module-lessons { display: none; }
        .learn-nav-item { display: flex; align-items: center; gap: .5rem; padding: .5rem 1rem; color: #212529; text-decoration: none; border-left: 3px solid transparent; transition: background .15s, border-color .15s; }
        .learn-nav-item:hover { background: #f8f9fa; color: #212529; }
        .learn-nav-item.active { background: rgba(220,53,69,.08); border-left-color: var(--lms-accent); color: var(--lms-accent); font-weight: 600; }
        .learn-nav-item .icon { width: 20px; text-align: center; color: #6c757d; }
        .learn-nav-item.active .icon { color: var(--lms-accent); }
        .learn-nav-item .flex-grow-1 { min-width: 0; }
        .learn-nav-item .circle { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #dee2e6; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .learn-nav-item.done .circle { border-color: var(--lms-accent); background: var(--lms-accent); color: #fff; }
        .learn-nav-item.active .circle { border-color: var(--lms-accent); background: rgba(220,53,69,.2); }
        .learn-nav-item .circle i { font-size: .6rem; }
        .learn-nav-item-locked { cursor: not-allowed; color: #adb5bd; }
        .learn-nav-item-locked:hover { background: #f8f9fa; }
        .learn-quiz-q .form-check { margin-bottom: .35rem; }
        .learn-quiz-q .form-check:last-of-type { margin-bottom: 0; }
        .learn-main { flex: 1; overflow-y: auto; background: #fff; }
        .learn-content-wrap { max-width: 720px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }
        .learn-meta { font-size: .85rem; color: #6c757d; margin-bottom: .25rem; }
        .learn-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .5rem; }
        .learn-accent { width: 60px; height: 4px; background: var(--lms-accent); margin-bottom: 1.5rem; }
        .learn-body { line-height: 1.7; }
        .learn-attendance { border: 1px solid #dee2e6; border-radius: 8px; padding: 1.25rem; background: #f8f9fa; }
        .learn-attendance-form .form-label { margin-bottom: 0.2rem; }
        .learn-media-wrap { margin: 1.5rem 0; border-radius: 8px; overflow: hidden; background: #000; }
        .learn-media-wrap video, .learn-media-wrap iframe { width: 100%; display: block; }
        .learn-bottom-bar { position: fixed; bottom: 0; left: var(--lms-sidebar-width); right: 0; height: 56px; background: #f0f0f0; border-top: 1px solid #dee2e6; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; }
        .learn-bar-next { display: flex; align-items: center; gap: .5rem; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .learn-bar-next:hover { color: #0a58ca; }
        .learn-bar-prev { color: #6c757d; text-decoration: none; }
        .learn-bar-prev:hover { color: #212529; }
        .learn-btn-complete { background: var(--lms-accent); color: #fff; border: none; padding: .5rem 1.25rem; border-radius: 6px; font-weight: 500; }
        .learn-btn-complete:hover { background: #bb2d3b; color: #fff; }
        @media (max-width: 991.98px) {
            .learn-sidebar { position: fixed; left: 0; top: 48px; height: calc(100vh - 48px); z-index: 1030; transform: translateX(-100%); transition: transform .2s; box-shadow: 2px 0 8px rgba(0,0,0,.1); }
            .learn-sidebar.show { transform: translateX(0); }
            .learn-main { margin-left: 0 !important; }
            .learn-bottom-bar { left: 0; padding: 0 1rem; }
            .learn-content-wrap { padding: 1rem 1rem 4rem; }
            .learn-title { font-size: 1.5rem; }
            .learn-course-hero { padding: 1rem; min-height: 120px; }
            .learn-course-hero h2 { font-size: 1.1rem; }
            .learn-topbar { font-size: .875rem; }
            .learn-topbar a { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px; }
        }
        @media (max-width: 575.98px) {
            .learn-content-wrap { padding: 0.75rem 0.75rem 3.5rem; }
            .learn-title { font-size: 1.25rem; }
            .learn-bottom-bar { height: 48px; font-size: .875rem; padding: 0 0.75rem; }
            .learn-bar-next, .learn-bar-prev { font-size: .875rem; }
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column">
    <header class="learn-topbar d-flex align-items-center px-3">
        <button type="button" class="btn btn-link text-light me-2 d-lg-none" id="learn-sidebar-toggle" aria-label="Toggle menu">
            <i class="bi bi-list fs-5"></i>
        </button>
        <a href="{{ route('courses.index') }}" class="text-light text-decoration-none small fw-bold me-2">{{ config('brand.name', 'TTM Group') }} LMS</a>
        <span class="text-light opacity-75 small me-2 d-none d-sm-inline">|</span>
        <a href="{{ route('courses.index') }}" class="text-light text-decoration-none small me-3">Courses</a>
        @hasSection('learn-back')
        @yield('learn-back')
        @endif
        @auth
        <a href="{{ route('notifications.index') }}" class="text-light text-decoration-none small ms-auto me-2" title="Notifications"><i class="bi bi-bell"></i>@if(($unreadN = auth()->user()->unreadNotifications()->count()) > 0)<span class="badge bg-danger rounded-pill ms-1">{{ $unreadN > 9 ? '9+' : $unreadN }}</span>@endif</a>
        <a href="{{ route('profile.edit') }}" class="text-light-50 text-decoration-none small me-2">{{ auth()->user()->name }}</a>
        <a href="{{ route('courses.my') }}" class="text-light ms-0 small">Exit to my courses</a>
        @endauth
    </header>
    <div class="d-flex flex-grow-1 overflow-hidden">
        @yield('sidebar')
        <main class="learn-main flex-grow-1 ms-0">
            @if(session('message'))
            <div class="alert alert-info mb-0 rounded-0">{{ session('message') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    @hasSection('bottom-bar')
    @yield('bottom-bar')
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @hasSection('chat')
    @yield('chat')
    @endif
    <script>
        document.getElementById('learn-sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.learn-sidebar')?.classList.toggle('show');
        });
        document.querySelectorAll('.learn-module-header').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var mod = this.closest('.learn-module');
                if (!mod) return;
                mod.classList.toggle('collapsed');
                this.setAttribute('aria-expanded', mod.classList.contains('collapsed') ? 'false' : 'true');
            });
            btn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
