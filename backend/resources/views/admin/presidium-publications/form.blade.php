@extends('layouts.dashboard')

@section('title', $publication->exists ? 'Edit publication' : 'Add publication')
@section('page_heading', $publication->exists ? 'Edit publication' : 'Add publication')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $publication->exists ? 'Edit publication' : 'Add publication' }}</div>
                    <div class="dash-panel-subtitle">Configure cover + links shown in the app.</div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.presidium-publications.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Publications</a>
                </div>
            </div>

            <form method="POST"
                action="{{ $publication->exists ? route('admin.presidium-publications.update', $publication) : route('admin.presidium-publications.store') }}"
                style="margin-top:1rem;max-width:900px;">
                @csrf
                @if($publication->exists)
                    @method('PUT')
                @endif

                <div class="dash-form-row">
                    <label class="dash-label">Title</label>
                    <input class="dash-input" name="title" value="{{ old('title', $publication->title) }}" required />
                    @error('title') <div class="dash-error">{{ $message }}</div> @enderror
                </div>

                <div class="dash-form-row">
                    <label class="dash-label">Author</label>
                    <input class="dash-input" name="author" value="{{ old('author', $publication->author) }}" />
                    @error('author') <div class="dash-error">{{ $message }}</div> @enderror
                </div>

                <div class="dash-form-row">
                    <label class="dash-label">Summary</label>
                    <textarea class="dash-input" rows="4" name="summary">{{ old('summary', $publication->summary) }}</textarea>
                    @error('summary') <div class="dash-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 180px;gap:0.75rem;">
                    <div class="dash-form-row">
                        <label class="dash-label">Slug</label>
                        <input class="dash-input" name="slug" value="{{ old('slug', $publication->slug) }}" required />
                        @error('slug') <div class="dash-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="dash-form-row">
                        <label class="dash-label">Order</label>
                        <input class="dash-input" type="number" min="0" name="order" value="{{ old('order', $publication->order ?? 0) }}" />
                        @error('order') <div class="dash-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="dash-form-row">
                    <label class="dash-label">Cover URL (relative or absolute)</label>
                    <input class="dash-input" name="cover_url" value="{{ old('cover_url', $publication->cover_url) }}" placeholder="/icon-1.png" />
                    @error('cover_url') <div class="dash-error">{{ $message }}</div> @enderror
                </div>

                <div class="dash-form-row">
                    <label class="dash-label">Article URL</label>
                    <input class="dash-input" name="article_url" value="{{ old('article_url', $publication->article_url) }}" />
                    @error('article_url') <div class="dash-error">{{ $message }}</div> @enderror
                </div>

                <div class="dash-form-row">
                    <label class="dash-label">Purchase URL</label>
                    <input class="dash-input" name="purchase_url" value="{{ old('purchase_url', $publication->purchase_url) }}" />
                    @error('purchase_url') <div class="dash-error">{{ $message }}</div> @enderror
                </div>

                <div class="dash-form-row">
                    <label class="dash-label">Online copy URL</label>
                    <input class="dash-input" name="online_copy_url" value="{{ old('online_copy_url', $publication->online_copy_url) }}" />
                    @error('online_copy_url') <div class="dash-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:flex;gap:1.25rem;align-items:center;margin-top:0.75rem;">
                    <label style="display:flex;gap:0.5rem;align-items:center;">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $publication->is_featured) ? 'checked' : '' }} />
                        <span>Featured</span>
                    </label>
                    <label style="display:flex;gap:0.5rem;align-items:center;">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $publication->is_published) ? 'checked' : '' }} />
                        <span>Published</span>
                    </label>
                </div>

                <div style="margin-top:1rem;">
                    <button class="dash-btn" type="submit">
                        {{ $publication->exists ? 'Save changes' : 'Create publication' }}
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection

