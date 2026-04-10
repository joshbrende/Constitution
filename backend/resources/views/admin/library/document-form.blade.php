@extends('layouts.dashboard')

@section('title', $document ? 'Edit – ' . $document->title : 'Create Document')
@section('page_heading', $document ? 'Edit document' : 'Create document')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $document ? 'Edit document' : 'New document' }}</div>
                    <div class="dash-panel-subtitle">Set published_at to make it visible in the app. Access rule: public, member, or leadership.</div>
                </div>
                <a href="{{ route('admin.library.documents.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Documents</a>
            </div>

            <form method="POST" action="{{ $document ? route('admin.library.documents.update', $document) : route('admin.library.documents.store') }}">
                @csrf
                @if ($document) @method('PUT') @endif

                <div style="display:grid;gap:1rem;max-width:42rem;">
                    <div>
                        <label for="title" class="form-label">Title <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="title" type="text" name="title" value="{{ old('title', $document?->title) }}" required class="form-input">
                    </div>
                    <div>
                        <label for="slug" class="form-label">Slug</label>
                        <input id="slug" type="text" name="slug" value="{{ old('slug', $document?->slug) }}" placeholder="auto from title" class="form-input">
                    </div>
                    <div>
                        <label for="library_category_id" class="form-label">Category</label>
                        <select id="library_category_id" name="library_category_id" class="form-input">
                            <option value="">— None —</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" {{ old('library_category_id', $document?->library_category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="document_type" class="form-label">Type <span style="color:var(--zanupf-red);">*</span></label>
                            <select id="document_type" name="document_type" required class="form-input">
                                @foreach ($documentTypes as $k => $v)
                                    <option value="{{ $k }}" {{ old('document_type', $document?->document_type) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="language" class="form-label">Language</label>
                            <input id="language" type="text" name="language" value="{{ old('language', $document?->language ?? 'en') }}" maxlength="10" class="form-input" placeholder="e.g. en, sn, nd">
                        </div>
                    </div>
                    <div>
                        <label for="access_rule" class="form-label">Access <span style="color:var(--zanupf-red);">*</span></label>
                        <select id="access_rule" name="access_rule" required class="form-input">
                            @foreach ($accessRules as $k => $v)
                                <option value="{{ $k }}" {{ old('access_rule', $document?->access_rule) == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="abstract" class="form-label">Abstract</label>
                        <textarea id="abstract" name="abstract" rows="3" class="form-input">{{ old('abstract', $document?->abstract) }}</textarea>
                    </div>
                    <div>
                        <label for="body" class="form-label">Body (HTML or plain text)</label>
                        <textarea id="body" name="body" rows="12" class="form-input">{{ old('body', $document?->body) }}</textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="published_at" class="form-label">Published at</label>
                            <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $document?->published_at?->format('Y-m-d\TH:i')) }}" class="form-input">
                            <p class="form-help">Leave empty for draft. Set to show in app.</p>
                        </div>
                        <div>
                            <label for="file_path" class="form-label">File path (optional)</label>
                            <input id="file_path" type="text" name="file_path" value="{{ old('file_path', $document?->file_path) }}" placeholder="e.g. library/files/doc.pdf" class="form-input">
                        </div>
                    </div>
                    <div>
                        <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;font-weight:600;cursor:pointer;">{{ $document ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
