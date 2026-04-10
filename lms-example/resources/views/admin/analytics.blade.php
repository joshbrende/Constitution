@extends('layouts.admin')

@section('title', 'Course analytics')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Course analytics</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Course analytics</h1>
    <p class="text-muted mb-4">
        Enrollment, completion, and Knowledge Check performance across all courses. TTM Group learning metrics at a glance.
    </p>

    @if($items->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No courses found. <a href="{{ route('courses.create') }}" class="alert-link">Create a course</a> and enroll learners to see analytics.
    </div>
    @else
    {{-- Summary cards --}}
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
                        @if($summary['overall_completion_rate'] !== null)
                            {{ $summary['overall_completion_rate'] }}%
                        @else
                            —
                        @endif
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
                        @if($summary['overall_quiz_pass_rate'] !== null)
                            {{ $summary['overall_quiz_pass_rate'] }}%
                        @else
                            —
                        @endif
                    </div>
                    <small class="text-muted">Quiz pass rate</small>
                </div>
            </div>
        </div>
    </div>
    @endisset

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <caption class="visually-hidden">Course enrollment, completion and quiz performance analytics</caption>
            <thead class="table-light">
                <tr>
                    <th>Course</th>
                    <th class="text-center">Enrolled</th>
                    <th class="text-center">Completed</th>
                    <th class="text-center">Completion rate</th>
                    <th class="text-center">Quiz attempts</th>
                    <th class="text-center">Quiz pass rate</th>
                    <th class="text-center">Avg quiz %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $row)
                @php $c = $row['course']; @endphp
                <tr>
                    <td>
                        <a href="{{ route('courses.show', $c) }}" class="text-decoration-none fw-medium">
                            {{ $c->title }}
                        </a>
                        @if($c->instructor)
                        <br>
                        <small class="text-muted">{{ $c->instructor->name }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $row['enrolled'] }}</td>
                    <td class="text-center">{{ $row['completed'] }}</td>
                    <td class="text-center">
                        @if($row['completion_rate'] !== null)
                            {{ $row['completion_rate'] }}%
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-center">{{ $row['quiz_attempts'] }}</td>
                    <td class="text-center">
                        @if($row['quiz_pass_rate'] !== null)
                            {{ $row['quiz_pass_rate'] }}%
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row['avg_quiz_pct'] !== null)
                            {{ number_format($row['avg_quiz_pct'], 1) }}%
                        @else
                            —
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

