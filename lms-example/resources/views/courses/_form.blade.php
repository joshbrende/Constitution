@csrf
@if(isset($course))
@method('PUT')
@endif
@php $c = $course ?? null; @endphp

<div class="mb-3">
    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
           value="{{ old('title', $c?->title ?? '') }}" required>
    @error('title')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="slug" class="form-label">Slug</label>
    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
           value="{{ old('slug', $c?->slug ?? '') }}" placeholder="Leave blank to auto-generate from title">
    @error('slug')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="short_description" class="form-label">Short description</label>
    <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
              name="short_description" rows="2">{{ old('short_description', $c?->short_description ?? '') }}</textarea>
    @error('short_description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
              rows="6">{{ old('description', $c?->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
        <option value="draft" {{ old('status', $c?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
        <option value="published" {{ old('status', $c?->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
    </select>
    @error('status')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if(auth()->user()->isAdmin())
@php $instructors = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('roles.name', ['admin','facilitator','instructor']))->orderBy('name')->get(); @endphp
<div class="mb-3">
    <label for="instructor_id" class="form-label">Instructor</label>
    <select class="form-select @error('instructor_id') is-invalid @enderror" id="instructor_id" name="instructor_id">
        <option value="" {{ (old('instructor_id', $c?->instructor_id) ?? '') == '' ? 'selected' : '' }}>— Available for instructing request —</option>
        @foreach($instructors as $u)
        <option value="{{ $u->id }}" {{ (old('instructor_id', $c?->instructor_id) ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
    </select>
    <small class="text-muted">Leave as "Available for instructing request" to let facilitators request to instruct. Assign a user to give them immediate access.</small>
    @error('instructor_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
@endif

@if(isset($course) && ($certificateTemplates ?? collect())->isNotEmpty())
<div class="mb-3">
    <label for="certificate_template_id" class="form-label">Certificate template</label>
    <select class="form-select @error('certificate_template_id') is-invalid @enderror" id="certificate_template_id" name="certificate_template_id">
        <option value="">— Default (performance template) —</option>
        @foreach($certificateTemplates as $tpl)
        <option value="{{ $tpl->id }}" {{ (old('certificate_template_id', $c?->certificate_template_id) ?? '') == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
        @endforeach
    </select>
    <small class="text-muted">Optional. Certificates for this course use this PDF template; only the title (course name) changes.</small>
    @error('certificate_template_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
@endif

@php $tags = $tags ?? collect(); $tagIds = old('tags', ($c?->tags ?? collect())->pluck('id')->toArray()); if (!is_array($tagIds)) { $tagIds = []; } @endphp
<div class="mb-3">
    <label class="form-label">Tags</label>
    <div class="d-flex flex-wrap gap-2">
        @forelse($tags as $t)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $t->id }}" id="tag{{ $t->id }}" {{ in_array($t->id, $tagIds) ? 'checked' : '' }}>
            <label class="form-check-label" for="tag{{ $t->id }}">{{ $t->name }}</label>
        </div>
        @empty
        <span class="text-muted small">No tags yet. <a href="{{ route('admin.tags.index') }}">Create tags</a> in Admin.</span>
        @endforelse
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ isset($course) ? 'Update' : 'Create' }} course</button>
    @if(isset($course))
    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Cancel</a>
    @else
    <a href="{{ route('courses.instructor') }}" class="btn btn-outline-secondary">Cancel</a>
    @endif
</div>
