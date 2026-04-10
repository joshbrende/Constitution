@extends('layouts.facilitator')

@section('title', 'Grade submission')

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('instructor.submissions.index') }}">Submissions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Grade</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Grade submission</h1>
    <p class="text-muted mb-4">
        <strong>{{ $submission->assignment->title ?? 'Assignment' }}</strong> · {{ $submission->course?->title }} · {{ $submission->user->name ?? 'User #' . $submission->user_id }}
    </p>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Submission</div>
                <div class="card-body">
                    <div class="mb-3"><strong>Response</strong></div>
                    <div class="bg-light p-3 rounded mb-3" style="white-space: pre-wrap;">{{ $submission->content ?? '—' }}</div>
                    @if(!empty($submission->attachments) && is_array($submission->attachments))
                    <div class="mb-2"><strong>Attachments</strong></div>
                    <ul class="list-unstyled">
                        @foreach($submission->attachments as $att)
                        <li><a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" rel="noopener">{{ $att['name'] ?? 'File' }}</a></li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Grade</div>
                <div class="card-body">
                    @if($submission->isGraded())
                    <p class="text-muted">Already graded: <strong>{{ $submission->score }}/{{ $submission->max_points }}</strong></p>
                    @if($submission->instructor_feedback)
                    <p class="small">{{ $submission->instructor_feedback }}</p>
                    @endif
                    <p class="small text-muted">Graded {{ $submission->graded_at?->format('d M Y H:i') }}.</p>
                    @endif
                    <form action="{{ route('instructor.submissions.update', $submission) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="score" class="form-label">Score (0–{{ $submission->max_points ?: 100 }})</label>
                            <input type="number" name="score" id="score" class="form-control" min="0" max="{{ $submission->max_points ?: 100 }}" value="{{ old('score', $submission->score ?? 0) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Feedback (optional)</label>
                            <textarea name="instructor_feedback" id="feedback" class="form-control" rows="4">{{ old('instructor_feedback', $submission->instructor_feedback) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save grade</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
