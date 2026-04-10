@extends('layouts.dashboard')

@section('title', $category ? 'Edit Category – ' . $category->name : 'Create Category')
@section('page_heading', $category ? 'Edit category' : 'Create category')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $category ? 'Edit category' : 'New category' }}</div>
                    <div class="dash-panel-subtitle">Used to group library documents in the app.</div>
                </div>
                <a href="{{ route('admin.library.categories.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Categories</a>
            </div>

            <form method="POST" action="{{ $category ? route('admin.library.categories.update', $category) : route('admin.library.categories.store') }}">
                @csrf
                @if ($category) @method('PUT') @endif

                <div style="display:grid;gap:1rem;max-width:32rem;">
                    <div>
                        <label for="name" class="form-label">Name <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $category?->name) }}" required class="form-input">
                    </div>
                    <div>
                        <label for="slug" class="form-label">Slug</label>
                        <input id="slug" type="text" name="slug" value="{{ old('slug', $category?->slug) }}" placeholder="auto from name" class="form-input">
                    </div>
                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3" class="form-input">{{ old('description', $category?->description) }}</textarea>
                    </div>
                    <div>
                        <label for="parent_id" class="form-label">Parent category</label>
                        <select id="parent_id" name="parent_id" class="form-input">
                            <option value="">— None —</option>
                            @foreach ($parents as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id', $category?->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="order" class="form-label">Order</label>
                        <input id="order" type="number" name="order" value="{{ old('order', $category?->order ?? 0) }}" min="0" class="form-input" style="max-width:6rem;">
                    </div>
                    <div>
                        <button type="submit" style="padding:0.5rem 1rem;background:var(--zanupf-green);color:#fff;border:none;border-radius:0.4rem;font-weight:600;cursor:pointer;">{{ $category ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
