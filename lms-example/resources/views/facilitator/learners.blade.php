@extends('layouts.facilitator')

@section('title', 'Learners – ' . $course->title)

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Learners – {{ $course->title }}</h1>
    <p class="text-muted mb-3">
        Enrollment, progress and recent activity for this course.
    </p>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('courses.instructor') }}" class="small text-decoration-none">
                &larr; Back to instructing courses
            </a>
        </div>
        <div class="btn-group btn-group-sm" role="group" aria-label="Filter learners">
            <a href="{{ route('instructor.course-learners', [$course]) }}"
               class="btn btn-outline-secondary {{ $filter === '' ? 'active' : '' }}">
                All learners
            </a>
            <a href="{{ route('instructor.course-learners', [$course, 'filter' => 'at-risk']) }}"
               class="btn btn-outline-secondary {{ $filter === 'at-risk' ? 'active' : '' }}">
                At risk
                @if($atRiskCount > 0)
                    <span class="badge bg-danger ms-1">{{ $atRiskCount }}</span>
                @endif
            </a>
        </div>
    </div>

    @if($rows->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No enrollments yet for this course. Learners will appear here once they enroll.
    </div>
    @elseif($filter === 'at-risk' && $rows->where('at_risk', true)->isEmpty())
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>No at-risk learners. All enrolled learners are making good progress.
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <caption class="visually-hidden">Learner progress and activity for {{ $course->title }}</caption>
            <thead class="table-light">
                <tr>
                    <th>Learner</th>
                    <th class="text-center">Progress</th>
                    <th class="text-center">Units completed</th>
                    <th class="text-center">Quizzes completed</th>
                    <th class="text-center">Last activity</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                @php
                    $e = $row['enrollment'];
                    $cp = $row['progress'];
                @endphp
                <tr @if($row['at_risk']) class="table-warning" @endif>
                    <td>
                        <strong>{{ $e->user->name ?? 'User #' . $e->user_id }}</strong><br>
                        <small class="text-muted">{{ $e->user->email ?? '' }}</small>
                    </td>
                    <td class="text-center">
                        {{ $row['percentage'] }}%
                    </td>
                    <td class="text-center">
                        {{ $cp->units_completed ?? '–' }}/{{ $cp->total_units ?? '–' }}
                    </td>
                    <td class="text-center">
                        {{ $cp->quizzes_completed ?? '–' }}/{{ $cp->total_quizzes ?? '–' }}
                    </td>
                    <td class="text-center">
                        <small class="text-muted">
                            {{ $row['last_activity_at'] ? $row['last_activity_at']->diffForHumans() : 'No activity yet' }}
                        </small>
                    </td>
                    <td class="text-center">
                        @if($row['at_risk'])
                        <span class="badge bg-warning text-dark">At risk</span>
                        @elseif($row['percentage'] >= 100)
                        <span class="badge bg-success">Completed</span>
                        @else
                        <span class="badge bg-secondary">In progress</span>
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

