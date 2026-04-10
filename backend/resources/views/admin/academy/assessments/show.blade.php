@extends('layouts.dashboard')

@section('title', $assessment->title)
@section('page_heading', $assessment->title)

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
                    <div class="dash-panel-title">{{ $assessment->title }}</div>
                    <div class="dash-panel-subtitle">
                        {{ $assessment->course->code }} · Pass {{ $assessment->pass_mark }}% ·
                        @if ($assessment->duration_minutes)
                            {{ $assessment->duration_minutes }} min
                        @else
                            No time limit
                        @endif
                        · {{ ucfirst($assessment->status) }}
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <a href="{{ route('admin.academy.questions.create', $assessment) }}" style="padding:0.4rem 0.75rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Add question</a>
                    <a href="{{ route('admin.academy.assessments.edit', $assessment) }}" class="dash-btn-ghost" style="text-decoration:none;">Edit assessment</a>
                    <a href="{{ route('admin.academy.assessments.index', $assessment->course) }}" class="dash-btn-ghost" style="text-decoration:none;">← Assessments</a>
                </div>
            </div>

            @if ($assessment->description)
                <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1rem;">{{ $assessment->description }}</p>
            @endif

            @php
                $grouped = $assessment->questions->groupBy(fn ($q) => $q->module_id ?? 'uncategorised');
                $modules = $assessment->course->modules;
            @endphp

            @foreach ($modules as $module)
                @php $moduleQuestions = $grouped->get($module->id, collect()); @endphp
                @if ($moduleQuestions->isNotEmpty())
                    <div style="margin-bottom:1.5rem;">
                        <h3 style="font-size:1rem;font-weight:600;color:var(--zanupf-gold);margin-bottom:0.5rem;padding-bottom:0.25rem;border-bottom:1px solid var(--border-subtle);">
                            {{ $module->title }}
                            <span style="font-size:0.8rem;font-weight:400;color:var(--text-muted);">({{ $moduleQuestions->count() }} questions)</span>
                        </h3>
                        <table class="dash-table">
                            <thead>
                                <tr><th style="width:3rem;">#</th><th>Question</th><th>Difficulty</th><th>Options</th><th>Marks</th><th></th></tr>
                            </thead>
                            <tbody>
                                @foreach ($moduleQuestions as $q)
                                    <tr>
                                        <td>{{ $q->order }}</td>
                                        <td>{{ Str::limit($q->body, 80) }}</td>
                                        <td><span class="dash-tag" style="font-size:0.75rem;">{{ ucfirst($q->difficulty ?? 'medium') }}</span></td>
                                        <td>{{ $q->options->count() }} ({{ $q->options->where('is_correct', true)->count() }} correct)</td>
                                        <td>{{ $q->marks }}</td>
                                        <td>
                                            <a href="{{ route('admin.academy.questions.edit', $q) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                            <form method="POST" action="{{ route('admin.academy.questions.destroy', $q) }}" style="display:inline;" onsubmit="return confirm('Delete this question?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach

            @php $uncategorised = $grouped->get('uncategorised', collect()); @endphp
            @if ($uncategorised->isNotEmpty())
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;padding-bottom:0.25rem;border-bottom:1px solid var(--border-subtle);">
                        Uncategorised
                        <span style="font-size:0.8rem;font-weight:400;">({{ $uncategorised->count() }} questions)</span>
                    </h3>
                    <table class="dash-table">
                        <thead>
                            <tr><th style="width:3rem;">#</th><th>Question</th><th>Difficulty</th><th>Options</th><th>Marks</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($uncategorised as $q)
                                <tr>
                                    <td>{{ $q->order }}</td>
                                    <td>{{ Str::limit($q->body, 80) }}</td>
                                    <td><span class="dash-tag" style="font-size:0.75rem;">{{ ucfirst($q->difficulty ?? 'medium') }}</span></td>
                                    <td>{{ $q->options->count() }} ({{ $q->options->where('is_correct', true)->count() }} correct)</td>
                                    <td>{{ $q->marks }}</td>
                                    <td>
                                        <a href="{{ route('admin.academy.questions.edit', $q) }}" style="font-size:0.8rem;margin-right:0.5rem;color:var(--zanupf-gold);">Edit</a>
                                        <form method="POST" action="{{ route('admin.academy.questions.destroy', $q) }}" style="display:inline;" onsubmit="return confirm('Delete this question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.8rem;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($assessment->questions->isEmpty())
                <p class="dash-panel-subtitle">No questions yet. <a href="{{ route('admin.academy.questions.create', $assessment) }}">Add a question</a>.</p>
            @endif
        </section>
    </div>
@endsection
