@extends('layouts.facilitator')

@section('title', 'Facilitator Dashboard')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Facilitator Dashboard</h1>
    <p class="text-muted mb-4">Overview of your courses, enrollments, and recent activity.</p>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                        <i class="bi bi-journal-text display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ $courses->count() }}</div>
                        <small class="text-muted">Courses</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('courses.instructor') }}" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-bookmark-check display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ $totalEnrollments }}</div>
                        <small class="text-muted">Enrollments</small>
                    </div>
                </div>
            </div>
        </div>
        @if(($atRiskTotal ?? 0) > 0)
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0 border-warning">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-25 text-warning p-3 me-3">
                        <i class="bi bi-exclamation-triangle display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0">{{ $atRiskTotal }}</div>
                        <small class="text-muted">At-risk learners</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#at-risk" class="small text-decoration-none">View by course <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(($atRiskTotal ?? 0) > 0 && !empty($atRiskByCourse))
    <div class="row mb-4" id="at-risk">
        <div class="col-lg-12">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>At-risk learners by course</h5>
                    <p class="text-muted small mb-0 mt-1">Learners with &lt;50% progress and no activity in the last 14 days. Click a course to view and filter learners.</p>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush list-group-numbered">
                        @foreach($courses as $c)
                        @if(($atRiskByCourse[$c->id] ?? 0) > 0)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('instructor.course-learners', [$c, 'filter' => 'at-risk']) }}" class="text-decoration-none fw-medium">{{ $c->title }}</a>
                            <span class="badge bg-warning text-dark">{{ $atRiskByCourse[$c->id] }} at risk</span>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!auth()->user()->isAdmin() && ($coursesAvailableForInstructing->isNotEmpty() || $pendingRequests->isNotEmpty()))
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Courses available for instructing</h5>
                    <p class="text-muted small mb-0 mt-1">Request to facilitate a course. The admin will approve or reject. Once approved, you get full access.</p>
                </div>
                <div class="card-body">
                    @if($coursesAvailableForInstructing->isEmpty())
                    <p class="text-muted mb-0">No courses without a facilitator at the moment. Check back later.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coursesAvailableForInstructing as $c)
                                <tr>
                                    <td>
                                        <a href="{{ route('courses.show', $c) }}" class="text-decoration-none">{{ $c->title }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if($pendingRequestCourseIds->contains($c->id))
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                        <form action="{{ route('instructor.request-course', $c) }}" method="post" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Request to facilitate</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Your pending requests</h5>
                </div>
                <div class="card-body">
                    @if($pendingRequests->isEmpty())
                    <p class="text-muted mb-0">You have no pending requests.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Requested</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $req)
                                <tr>
                                    <td><a href="{{ route('courses.show', $req->course) }}" class="text-decoration-none">{{ $req->course->title ?? '—' }}</a></td>
                                    <td><small class="text-muted">{{ $req->created_at?->format('d M Y H:i') }}</small></td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent enrollments</h5>
                    <a href="{{ route('courses.instructor') }}" class="small">Instructing</a>
                </div>
                <div class="card-body">
                    @if($recentEnrollments->isEmpty())
                    <p class="text-muted mb-0">No enrollments yet. Enrollments appear here when learners join your courses.</p>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($recentEnrollments as $e)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $e->user->name ?? 'User #' . $e->user_id }}</strong>
                                <span class="text-muted"> → </span>
                                <span>{{ \Illuminate\Support\Str::limit($e->course->title ?? 'Course', 35) }}</span>
                                <br>
                                <small class="text-muted">{{ $e->enrolled_at?->format('d M Y H:i') }}</small>
                            </div>
                            <a href="{{ route('courses.show', $e->course) }}" class="btn btn-sm btn-outline-primary">View course</a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('courses.create') }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-plus-lg me-2"></i>Create course
                    </a>
                    <a href="{{ route('courses.instructor') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-journal-text me-2"></i>Instructing
                    </a>
                    <a href="{{ route('instructor.stats') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-bar-chart me-2"></i>Stats
                    </a>
                    <a href="{{ route('instructor.results') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-clipboard-check me-2"></i>Knowledge Check results
                    </a>
                    <a href="{{ route('instructor.quiz-stats') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-pie-chart me-2"></i>Knowledge Check stats
                    </a>
                    <a href="{{ route('instructor.submissions.index') }}" class="btn btn-outline-secondary w-100 mb-2 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark-arrow-up me-2"></i>Submissions</span>
                        @if(($pendingSubmissionsCount ?? 0) > 0)
                        <span class="badge bg-warning text-dark">{{ $pendingSubmissionsCount > 99 ? '99+' : $pendingSubmissionsCount }} pending</span>
                        @endif
                    </a>
                    <a href="{{ route('instructor.ratings') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-star me-2"></i>My ratings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
