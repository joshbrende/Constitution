@extends('layouts.facilitator')

@section('title', 'Knowledge Check stats')

@section('content')
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Knowledge Check stats</h1>
    <p class="text-muted mb-4">Per–Knowledge Check: attempts, pass rate, and average score.</p>

    <form action="{{ route('instructor.quiz-stats') }}" method="get" class="mb-4 d-flex align-items-center gap-2 flex-wrap">
        <label class="mb-0">Date range:</label>
        <select name="range" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
            <option value="all" {{ ($range ?? 'all') === 'all' ? 'selected' : '' }}>All time</option>
            <option value="30" {{ ($range ?? '') === '30' ? 'selected' : '' }}>Last 30 days</option>
            <option value="90" {{ ($range ?? '') === '90' ? 'selected' : '' }}>Last 90 days</option>
        </select>
    </form>

    @if(empty($quizStats))
    <div class="alert alert-info">No Knowledge Checks in your courses yet. Add quiz units to courses to see per–Knowledge Check stats.</div>
    @else
    @if(($range ?? 'all') !== 'all')
    <p class="small text-muted mb-3">Attempts below are those completed in the selected date range.</p>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Course</th>
                    <th>Knowledge Check</th>
                    <th class="text-center">Attempts</th>
                    <th class="text-center">Passed</th>
                    <th class="text-center">Pass rate</th>
                    <th class="text-center">Average&nbsp;%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizStats as $s)
                <tr>
                    <td>
                        <a href="{{ route('courses.show', $s['course']) }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($s['course']->title ?? '—', 35) }}</a>
                    </td>
                    <td>{{ $s['quiz']->title ?? '—' }}</td>
                    <td class="text-center">{{ $s['attempts'] }}</td>
                    <td class="text-center">{{ $s['passed'] }}</td>
                    <td class="text-center">{{ $s['pass_rate'] !== null ? $s['pass_rate'] . '%' : '—' }}</td>
                    <td class="text-center">{{ $s['avg_pct'] !== null ? number_format($s['avg_pct'], 1) . '%' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
