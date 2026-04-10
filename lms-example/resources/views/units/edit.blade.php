@extends('layouts.facilitator')

@section('title', 'Edit unit: ' . $unit->title)

@section('content')
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.instructor') }}">Instructing</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ \Illuminate\Support\Str::limit($course->title, 35) }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.edit', $course) }}">Edit course</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit unit</li>
        </ol>
    </nav>

    <h1 class="h2">Edit unit</h1>
    <p class="text-muted">{{ $unit->title }}</p>

    <form action="{{ route('units.update', [$course, $unit]) }}" method="post" class="mb-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                   value="{{ old('title', $unit->title) }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="unit_type" class="form-label">Type <span class="text-danger">*</span></label>
            <select class="form-select @error('unit_type') is-invalid @enderror" id="unit_type" name="unit_type" required>
                <option value="text" {{ old('unit_type', $unit->unit_type) === 'text' ? 'selected' : '' }}>Lesson (text)</option>
                <option value="video" {{ old('unit_type', $unit->unit_type) === 'video' ? 'selected' : '' }}>Video</option>
                <option value="audio" {{ old('unit_type', $unit->unit_type) === 'audio' ? 'selected' : '' }}>Audio</option>
                <option value="document" {{ old('unit_type', $unit->unit_type) === 'document' ? 'selected' : '' }}>Document</option>
                <option value="assignment" {{ old('unit_type', $unit->unit_type) === 'assignment' ? 'selected' : '' }}>Assignment</option>
                <option value="quiz" {{ old('unit_type', $unit->unit_type) === 'quiz' ? 'selected' : '' }}>Knowledge Check</option>
            </select>
            @error('unit_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content"
                      rows="16" placeholder="Use the toolbar to format: headings, bold, lists, links. What you see is how learners will see it.">{{ old('content', $unit->content) }}</textarea>
            <small class="text-muted">Use the toolbar to format text like in Word or WordPress—no HTML needed. Use the <strong>Paragraph ▼</strong> dropdown for headings. <em>Code</em> shows the raw HTML only if you need it; avoid changing step markers in lessons with multiple steps.</small>
            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        @if($unit->title === 'Module 1: Introduction')
        <div class="mb-4 p-3 border rounded bg-light">
            <h6 class="mb-2"><i class="bi bi-file-earmark-arrow-down me-2"></i>Reload from template</h6>
            <p class="small text-muted mb-2">Replace the content above with the latest <code>01_introduction.md</code> (Module 1: Understanding SALGA 2026 Context). This overwrites your current content and sets duration to 6 minutes.</p>
            <form action="{{ route('units.refresh-from-file', [$course, $unit]) }}" method="post" class="d-inline" onsubmit="return confirm('This will overwrite the current content. Continue?');">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-clockwise me-1"></i>Reload from 01_introduction.md</button>
            </form>
        </div>
        @endif

        <div class="mb-3">
            <label for="description" class="form-label">Short description</label>
            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                   value="{{ old('description', $unit->description) }}" maxlength="500" placeholder="Optional summary">
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label for="duration" class="form-label">Duration (minutes)</label>
                <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration"
                       value="{{ old('duration', $unit->duration) }}" min="0" max="999" placeholder="e.g. 15">
                @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="order" class="form-label">Order</label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order"
                       value="{{ old('order', $unit->order) }}" min="0" placeholder="0">
                <small class="text-muted">Lower = earlier in curriculum.</small>
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label for="video_url" class="form-label">Video URL</label>
            <input type="url" class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url"
                   value="{{ old('video_url', $unit->video_url) }}" placeholder="YouTube, Vimeo, or direct URL">
            @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="audio_url" class="form-label">Audio URL</label>
            <input type="url" class="form-control @error('audio_url') is-invalid @enderror" id="audio_url" name="audio_url"
                   value="{{ old('audio_url', $unit->audio_url) }}">
            @error('audio_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="document_url" class="form-label">Document URL</label>
            <input type="url" class="form-control @error('document_url') is-invalid @enderror" id="document_url" name="document_url"
                   value="{{ old('document_url', $unit->document_url) }}">
            @error('document_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        @if($unit->unit_type === 'quiz' && $unit->quiz)
        <div class="alert alert-info d-flex align-items-start justify-content-between flex-wrap gap-2">
            <span>
                <i class="bi bi-question-circle me-2"></i>This unit is linked to Knowledge Check <strong>{{ $unit->quiz->title }}</strong>.
                Change title, instructions, pass mark, and questions in the dedicated editor.
            </span>
            <a href="{{ route('units.quiz.edit', [$course, $unit]) }}" class="btn btn-sm btn-outline-primary align-self-center"><i class="bi bi-pencil-square me-1"></i>Edit Knowledge Check</a>
        </div>
        @endif

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update unit</button>
            <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-secondary">Back to course</a>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#content',
        height: 480,
        plugins: 'lists link code',
        toolbar: 'undo redo | formatselect | bold italic | bullist numlist | link | code',
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3',
        extended_valid_elements: 'div[class|data-step-title]',
        promotion: false,
        branding: false,
        placeholder: 'Use the toolbar to add headings, lists, bold text, and links. Click "Source code" only if you need to edit HTML.'
    });
});
</script>
@endpush
@endsection
