<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Facilitator') – {{ config('app.name') }}</title>
    <meta name="description" content="{{ config('brand.description') }}">
    <link rel="canonical" href="{{ config('app.url') }}{{ request()->getRequestUri() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .facilitator-sidebar { min-height: calc(100vh - 56px - 120px); }
        .facilitator-sidebar .nav-link { border-radius: 0.375rem; }
        .facilitator-sidebar .nav-link.active { background: rgba(13, 110, 253, 0.15); color: #0d6efd; }
        .facilitator-sidebar .nav-link:hover:not(.active) { background: rgba(0,0,0,.05); }
        @media (max-width: 767.98px) {
            .facilitator-sidebar { min-height: auto; margin-bottom: 1rem !important; }
            .facilitator-sidebar .card-body { padding: 0.5rem; }
            .facilitator-sidebar .nav-link { padding: 0.5rem 0.75rem; font-size: 0.875rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
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
                    @if(auth()->user()->canEditCourses())
                    <li class="nav-item"><a class="nav-link" href="{{ route('instructor.dashboard') }}">Instructing</a></li>
                    @endif
                    @endauth
                    @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">All Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('leaderboard.index') }}">Leaderboard</a></li>
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

    <div class="container-fluid py-4">
        @if(session('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
        @endif
        @if(session('quiz_result'))
        @php $r = session('quiz_result'); @endphp
        <div class="alert {{ $r['passed'] ? 'alert-success' : 'alert-warning' }}">
            Knowledge Check: {{ $r['score'] }}% {{ $r['passed'] ? '– Passed' : '– Try again' }}
        </div>
        @endif

        <div class="row">
            <aside class="col-md-3 col-lg-2 mb-4 mb-md-0">
                <nav class="card shadow-sm facilitator-sidebar">
                    <div class="card-body py-2">
                        <div class="small text-muted text-uppercase px-3 py-2">Facilitator</div>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}" href="{{ route('instructor.dashboard') }}">
                                    <i class="bi bi-grid-1x2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('help.facilitator') ? 'active' : '' }}" href="{{ route('help.facilitator') }}">
                                    <i class="bi bi-question-circle me-2"></i>Help
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('courses.instructor') || request()->routeIs('courses.edit') || request()->routeIs('units.edit') ? 'active' : '' }}" href="{{ route('courses.instructor') }}">
                                    <i class="bi bi-journal-text me-2"></i>Instructing
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('courses.create') ? 'active' : '' }}" href="{{ route('courses.create') }}">
                                    <i class="bi bi-plus-lg me-2"></i>Create course
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('instructor.stats') ? 'active' : '' }}" href="{{ route('instructor.stats') }}">
                                    <i class="bi bi-bar-chart me-2"></i>Stats
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('instructor.quiz-stats') ? 'active' : '' }}" href="{{ route('instructor.quiz-stats') }}">
                                    <i class="bi bi-pie-chart me-2"></i>Knowledge Check stats
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('instructor.results') ? 'active' : '' }}" href="{{ route('instructor.results') }}">
                                    <i class="bi bi-clipboard-check me-2"></i>Knowledge Check results
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('instructor.ratings') ? 'active' : '' }}" href="{{ route('instructor.ratings') }}">
                                    <i class="bi bi-star me-2"></i>My ratings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('instructor.submissions*') ? 'active' : '' }}" href="{{ route('instructor.submissions.index') }}">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Submissions
                                    @if(($pendingSubmissionsCount ?? 0) > 0)<span class="badge bg-warning text-dark ms-1">{{ $pendingSubmissionsCount > 99 ? '99+' : $pendingSubmissionsCount }}</span>@endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('instructor.certificate-signature*') ? 'active' : '' }}" href="{{ route('instructor.certificate-signature') }}">
                                    <i class="bi bi-pen me-2"></i>Certificate signature
                                </a>
                            </li>
                            @if(auth()->user()->isAdmin())
                            <li class="nav-item mt-2 pt-2 border-top">
                                <a class="nav-link {{ request()->routeIs('admin.instructor-requests*') ? 'active' : '' }}" href="{{ route('admin.instructor-requests.index') }}">
                                    <i class="bi bi-person-plus me-2"></i>Instructor requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.facilitator-ratings*') ? 'active' : '' }}" href="{{ route('admin.facilitator-ratings.index') }}">
                                    <i class="bi bi-star me-2"></i>Facilitator ratings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-person-lines-fill me-2"></i>Attendance
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </nav>
            </aside>
            <main class="col-md-9 col-lg-10">
                @yield('content')
            </main>
        </div>
    </div>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-muted small">
            <div class="row">
                <div class="col-md-6">&copy; {{ date('Y') }} <strong>{{ config('brand.name', 'TTM Group') }}</strong>. All rights reserved.</div>
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
