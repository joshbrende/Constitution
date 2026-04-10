@extends('layouts.app')

@section('title', 'My learning')

@section('content')
<div class="container py-4">
    <h1 class="h2 mb-1">My learning</h1>
    <p class="text-muted mb-4">Your progress, certificates, and activity.</p>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                        <i class="bi bi-star display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ (int) $user->points }}</div>
                        <small class="text-muted">Points</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('leaderboard.index') }}" class="small text-decoration-none">Leaderboard <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-award display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ $user->badges->count() }}</div>
                        <small class="text-muted">Badges</small>
                    </div>
                </div>
                @if($user->badges->isNotEmpty())
                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($user->badges->take(3) as $b)
                        <span class="badge bg-secondary" title="{{ $b->description ?? $b->name }}">{{ $b->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 me-3">
                        <i class="bi bi-journal-bookmark display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ $inProgressCount }}</div>
                        <small class="text-muted">In progress</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('courses.my') }}" class="small text-decoration-none">My courses <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                        <i class="bi bi-patch-check display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ $certificateCount }}</div>
                        <small class="text-muted">Certificates</small>
                    </div>
                </div>
                @if($certificateCount > 0)
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('certificates.show', $certificates->first()) }}" class="small text-decoration-none">View <i class="bi bi-arrow-right"></i></a>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($resumeCourse && $resumeUnit)
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="text-muted small mb-1">Continue learning</div>
                <h5 class="mb-1">{{ $resumeCourse->title }}</h5>
                <p class="mb-0 text-muted small">
                    You last viewed: <strong>{{ $resumeUnit->title }}</strong>
                    @if($resumeEnrollment)
                        · {{ (int)($resumeEnrollment->progress_percentage ?? 0) }}% complete
                    @endif
                </p>
            </div>
            <div class="text-md-end">
                <a href="{{ route('learn.show', ['course' => $resumeCourse, 'unit' => $resumeUnit->id]) }}" class="btn btn-primary">
                    <i class="bi bi-play-circle me-1"></i>Resume
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($recommendedCourses->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-stars me-2"></i>Recommended for you</h5>
            <a href="{{ route('courses.index') }}" class="small text-decoration-none">Browse all</a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($recommendedCourses as $rc)
                <div class="col-md-4">
                    <a href="{{ route('courses.show', $rc) }}" class="text-decoration-none text-reset">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-truncate mb-1">{{ $rc->title }}</h6>
                                @if($rc->short_description)
                                <p class="card-text text-muted small mb-2">{{ \Illuminate\Support\Str::limit($rc->short_description, 80) }}</p>
                                @endif
                                @if($rc->instructor)
                                <p class="card-text text-muted small mb-0">
                                    <i class="bi bi-person me-1"></i>{{ $rc->instructor->name }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>In progress</h5>
                    <a href="{{ route('courses.my') }}" class="small">My courses</a>
                </div>
                <div class="card-body">
                    @if($inProgress->isEmpty())
                    <p class="text-muted mb-0">No courses in progress. <a href="{{ route('courses.index') }}">Browse courses</a> to get started.</p>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($inProgress as $e)
                        <a href="{{ route('learn.show', ['course' => $e->course, 'start' => 1]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span class="text-truncate me-2">{{ $e->course->title }}</span>
                            <span class="badge bg-primary rounded-pill">{{ (int)($e->progress_percentage ?? 0) }}%</span>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-award me-2"></i>Certificates</h5>
                    @if($certificates->isNotEmpty())
                    <a href="{{ route('courses.my') }}" class="small">My courses</a>
                    @endif
                </div>
                <div class="card-body">
                    @if($certificates->isEmpty())
                    <p class="text-muted mb-0">No certificates yet. Complete a course to earn one.</p>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($certificates as $cert)
                        <a href="{{ route('certificates.show', $cert) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span class="text-truncate me-2">{{ $cert->course->title ?? 'Course' }}</span>
                            <i class="bi bi-download text-muted"></i>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($recentCompletions->isNotEmpty())
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent activity</h5>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0">
                @foreach($recentCompletions as $uc)
                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>{{ $uc->unit->title ?? 'Unit' }}</strong> in {{ $uc->course->title ?? 'Course' }}
                    </span>
                    <small class="text-muted">{{ $uc->completed_at?->format('d M Y H:i') }}</small>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('courses.index') }}" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Browse courses</a>
        <a href="{{ route('courses.my') }}" class="btn btn-outline-secondary ms-2">My courses</a>
    </div>
</div>
@endsection
