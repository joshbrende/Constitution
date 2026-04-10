<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Learning') – {{ config('app.name') }}</title>
    <meta name="description" content="{{ config('brand.description', 'TTM Group LMS – Premier AI training in South Africa.') }}">
    <link rel="canonical" href="{{ config('app.url') }}{{ request()->getRequestUri() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ auth()->check() && auth()->user()->canEditCourses() ? route('instructor.dashboard') : route('courses.index') }}">
                <strong>{{ config('brand.name', 'TTM Group') }}</strong> LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    @auth
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @endif
                    @if(!auth()->user()->canEditCourses() || auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">All Courses</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('learner.dashboard') }}">My learning</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('courses.my') }}">My Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('notes.index') }}">My notes</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('leaderboard.index') }}">Leaderboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('help.index') }}"><i class="bi bi-question-circle me-1"></i>Help</a></li>
                    @if(auth()->user()->canEditCourses())
                    <li class="nav-item"><a class="nav-link" href="{{ route('instructor.dashboard') }}">Instructing</a></li>
                    @endif
                    @endauth
                    @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">All Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('leaderboard.index') }}">Leaderboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('help.index') }}"><i class="bi bi-question-circle me-1"></i>Help</a></li>
                    @endguest
                </ul>
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell"></i>
                            @php $unreadN = auth()->user()->unreadNotifications()->count(); @endphp
                            @if($unreadN > 0)<span class="badge bg-danger rounded-pill ms-1">{{ $unreadN > 9 ? '9+' : $unreadN }}</span>@endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @forelse(auth()->user()->unreadNotifications()->limit(5)->get() as $nb)
                            <li><a class="dropdown-item text-wrap" href="{{ route('notifications.read-and-go', $nb->id) }}">{{ is_array($nb->data) ? \Illuminate\Support\Str::limit($nb->data['message'] ?? 'Notification', 60) : 'Notification' }}</a></li>
                            @empty
                            <li><span class="dropdown-item text-muted">No new notifications</span></li>
                            @endforelse
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('notifications.index') }}">View all</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a></li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                    @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @if(session('message'))
        <div class="container"><div class="alert alert-info">{{ session('message') }}</div></div>
        @endif
        @if(session('status'))
        <div class="container"><div class="alert alert-success">{{ session('status') }}</div></div>
        @endif
        @if(session('quiz_result'))
        @php $r = session('quiz_result'); @endphp
        <div class="container">
            <div class="alert {{ $r['passed'] ? 'alert-success' : 'alert-warning' }}">
                Knowledge Check: {{ $r['score'] }}% {{ $r['passed'] ? '– Passed' : '– Try again' }}
            </div>
        </div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-muted small">
            <div class="row">
                <div class="col-md-6">&copy; {{ date('Y') }} <strong>{{ config('brand.name', 'TTM Group') }}</strong>. All rights reserved. {{ config('brand.tagline', 'AI Training Excellence') }}.</div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('courses.index') }}" class="me-3">Courses</a>
                    <a href="{{ route('help.index') }}" class="me-3">Help</a>
                    <a href="{{ config('brand.website_url', 'https://www.ttm-group.co.za') }}" target="_blank" rel="noopener noreferrer">{{ config('brand.name', 'TTM Group') }} Website</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
