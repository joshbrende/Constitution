@php $badge = $badge ?? new \App\Models\Badge(); @endphp
<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $badge->name) }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="slug" class="form-label">Slug</label>
    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $badge->slug) }}" placeholder="Auto from name if blank">
    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="2" maxlength="500">{{ old('description', $badge->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="icon" class="form-label">Icon</label>
    <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $badge->icon) }}" placeholder="e.g. bi-trophy">
    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">Bootstrap Icons class (e.g. bi-trophy, bi-star).</div>
</div>
<div class="mb-3">
    <label for="points_required" class="form-label">Points required</label>
    <input type="number" name="points_required" id="points_required" class="form-control @error('points_required') is-invalid @enderror" value="{{ old('points_required', $badge->points_required ?? 0) }}" min="0" step="1">
    @error('points_required')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">Awarded when user reaches this many points. Use 0 to disable auto-award.</div>
</div>
