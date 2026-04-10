@extends('layouts.facilitator')

@section('title', 'Course stats')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Course stats</h1>
    <p class="text-muted mb-4">Enrollments, completions, and Knowledge Check performance per course. TTM Group facilitator metrics.</p>

    <form action="{{ route('instructor.stats') }}" method="get" class="mb-4 d-flex align-items-center gap-2 flex-wrap">
        <label class="mb-0">Date range:</label>
        <select name="range" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
            <option value="all" {{ ($range ?? 'all') === 'all' ? 'selected' : '' }}>All time</option>
            <option value="30" {{ ($range ?? '') === '30' ? 'selected' : '' }}>Last 30 days</option>
            <option value="90" {{ ($range ?? '') === '90' ? 'selected' : '' }}>Last 90 days</option>
        </select>
    </form>

    @if(empty($stats))
    <div class="alert alert-info">No courses yet. <a href="{{ route('courses.create') }}">Create a course</a> to see stats.</div>
    @else
    @isset($summary)
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-primary">{{ $summary['courses'] }}</div>
                    <small class="text-muted">Courses</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-success">{{ number_format($summary['total_enrolled']) }}</div>
                    <small class="text-muted">Enrolled</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-info">{{ number_format($summary['total_completed']) }}</div>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0">
                        @if($summary['overall_completion_rate'] !== null){{ $summary['overall_completion_rate'] }}% @else — @endif
                    </div>
                    <small class="text-muted">Completion rate</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0 text-warning">{{ number_format($summary['total_quiz_attempts']) }}</div>
                    <small class="text-muted">Quiz attempts</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center py-3">
                    <div class="h4 mb-0">
                        @if($summary['overall_quiz_pass_rate'] !== null){{ $summary['overall_quiz_pass_rate'] }}% @else — @endif
                    </div>
                    <small class="text-muted">Quiz pass rate</small>
                </div>
            </div>
        </div>
    </div>
    @endisset

    @if(($range ?? 'all') !== 'all')
    <p class="small text-muted mb-3">Metrics below are for the selected date range (enrollments by enrolled date, completions and quiz attempts by completion date).</p>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Course</th>
                    <th class="text-center">Enrolled</th>
                    <th class="text-center">Completed</th>
                    <th class="text-center">Completion rate</th>
                    <th class="text-center">Quiz attempts</th>
                    <th class="text-center">Quiz pass rate</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats as $s)
                <tr>
                    <td>
                        <a href="{{ route('courses.show', $s['course']) }}" class="text-decoration-none fw-medium">{{ $s['course']->title }}</a>
                        @if($s['course']->instructor)
                        <br><small class="text-muted">{{ $s['course']->instructor->name }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $s['enrollments'] }}</td>
                    <td class="text-center">{{ $s['completed'] }}</td>
                    <td class="text-center">
                        @if(isset($s['completion_rate']) && $s['completion_rate'] !== null){{ $s['completion_rate'] }}% @else — @endif
                    </td>
                    <td class="text-center">{{ $s['quiz_attempts'] }}</td>
                    <td class="text-center">
                        @if(isset($s['quiz_pass_rate']) && $s['quiz_pass_rate'] !== null){{ $s['quiz_pass_rate'] }}% @else — @endif
                    </td>
                    <td>
                        <a href="{{ route('courses.show', $s['course']) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="{{ route('instructor.course-learners', $s['course']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-people"></i></a>
                        @if(auth()->user()->canEditCourses())
                        <a href="{{ route('courses.edit', $s['course']) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
