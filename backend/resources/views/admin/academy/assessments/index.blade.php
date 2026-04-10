@extends('layouts.dashboard')

@section('title', 'Assessments – ' . $course->title)
@section('page_heading', 'Assessments – ' . $course->title)

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title" style="display:flex;align-items:center;gap:0.5rem;">
                        <x-icons.workflow-icon key="academy.assessment" size="18" color="var(--zanupf-gold)" />
                        Assessments
                    </div>
                    <div class="dash-panel-subtitle">Manage assessments for {{ $course->code }}</div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <form method="GET" action="{{ route('admin.academy.assessments.index', $course) }}" style="display:flex;gap:0.5rem;align-items:center;">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search assessment title"
                            style="padding:0.4rem 0.6rem;border:1px solid var(--border-subtle);border-radius:0.4rem;background:rgba(15,23,42,0.9);color:var(--text-main);min-width:16rem;"
                        >
                        <button type="submit" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;cursor:pointer;font-weight:600;font-size:0.8rem;">
                            Search
                        </button>
                    </form>
                    <a href="{{ route('admin.academy.assessments.create', $course) }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Add assessment</a>
                    <a href="{{ route('admin.academy.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Courses</a>
                </div>
            </div>

            <table class="dash-table">
                <thead>
                    <tr><th>Title</th><th>Questions</th><th>Pass mark</th><th>Duration</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($assessments as $a)
                        @php
                            $statusClass = match($a->status) {
                                'published' => 'status-pill--active',
                                'archived' => 'status-pill--pending',
                                default => 'status-pill--review',
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $a->title }}</strong></td>
                            <td>{{ $a->questions_count ?? 0 }}</td>
                            <td>{{ $a->pass_mark }}%</td>
                            <td>{{ $a->duration_minutes ? $a->duration_minutes . ' min' : '—' }}</td>
                            <td>
                                <span class="status-pill {{ $statusClass }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($a->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.academy.assessments.show', $a) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Manage questions</a>
                                <a href="{{ route('admin.academy.assessments.edit', $a) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                <form method="POST" action="{{ route('admin.academy.assessments.destroy', $a) }}" style="display:inline;" onsubmit="return confirm('Delete this assessment and all its questions?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($assessments->isEmpty())
                <p class="dash-panel-subtitle">No assessments yet. <a href="{{ route('admin.academy.assessments.create', $course) }}">Add an assessment</a>.</p>
            @endif
            @if (! $assessments->isEmpty())
                <div style="margin-top:1rem;">
                    {{ $assessments->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
