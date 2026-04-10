@extends('layouts.dashboard')

@section('title', $banner ? 'Edit home banner' : 'Create home banner')
@section('page_heading', $banner ? 'Edit home banner' : 'Create home banner')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $banner ? 'Edit banner' : 'Create banner' }}</div>
                    <div class="dash-panel-subtitle">
                        Banners appear as a carousel under the Overview header in the mobile app.
                    </div>
                </div>
                <a href="{{ route('admin.home-banners.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Banners</a>
            </div>

            @if ($errors->any())
                <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ $banner ? route('admin.home-banners.update', $banner) : route('admin.home-banners.store') }}">
                @csrf
                @if ($banner) @method('PUT') @endif

                <div style="display:grid;gap:1rem;max-width:40rem;">
                    <div>
                        <label class="form-label" for="title">Title <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="title" type="text" name="title" class="form-input"
                               value="{{ old('title', $banner?->title) }}" required
                               placeholder="e.g. Vision 2030 in action">
                    </div>

                    <div>
                        <label class="form-label" for="subtitle">Subtitle</label>
                        <input id="subtitle" type="text" name="subtitle" class="form-input"
                               value="{{ old('subtitle', $banner?->subtitle) }}"
                               placeholder="Short supporting line (optional)">
                    </div>

                    <div>
                        <label class="form-label" for="image_url">Image URL</label>
                        <input id="image_url" type="text" name="image_url" class="form-input"
                               value="{{ old('image_url', $banner?->image_url) }}"
                               placeholder="https://example.org/banner.jpg">
                        <p class="form-help">Optional. Used as the background image in the mobile carousel.</p>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;">
                        <div>
                            <label class="form-label" for="cta_label">CTA label</label>
                            <input id="cta_label" type="text" name="cta_label" class="form-input"
                                   value="{{ old('cta_label', $banner?->cta_label) }}"
                                   placeholder="e.g. Learn more, Become a member">
                        </div>
                        <div>
                            <label class="form-label" for="cta_url">CTA URL</label>
                            <input id="cta_url" type="text" name="cta_url" class="form-input"
                                   value="{{ old('cta_url', $banner?->cta_url) }}"
                                   placeholder="Deep link or website URL">
                            <p class="form-help">Optional. If CTA type is External, the app will open this link in the browser.</p>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;">
                        <div>
                            <label class="form-label" for="cta_type">CTA type</label>
                            @php($ctaType = old('cta_type', $banner?->cta_type) ?: 'internal')
                            <select id="cta_type" name="cta_type" class="form-input">
                                <option value="internal" {{ $ctaType === 'internal' ? 'selected' : '' }}>Internal (open inside app)</option>
                                <option value="external" {{ $ctaType === 'external' ? 'selected' : '' }}>External (open website)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="cta_tab">Internal tab</label>
                            @php($ctaTab = old('cta_tab', $banner?->cta_tab) ?: 'HomeTab')
                            <select id="cta_tab" name="cta_tab" class="form-input">
                                <option value="HomeTab" {{ $ctaTab === 'HomeTab' ? 'selected' : '' }}>Home</option>
                                <option value="ConstitutionTab" {{ $ctaTab === 'ConstitutionTab' ? 'selected' : '' }}>Constitutions</option>
                                <option value="ChatTab" {{ $ctaTab === 'ChatTab' ? 'selected' : '' }}>Chat</option>
                                <option value="ProfileTab" {{ $ctaTab === 'ProfileTab' ? 'selected' : '' }}>Profile</option>
                            </select>
                            <p class="form-help">Used when CTA type is Internal.</p>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;">
                        <div>
                            <label class="form-label" for="cta_screen">Internal screen (optional)</label>
                            <input id="cta_screen" type="text" name="cta_screen" class="form-input"
                                   value="{{ old('cta_screen', $banner?->cta_screen) }}"
                                   placeholder="e.g. PriorityProjects, Presidium, Overview">
                            <p class="form-help">Optional nested screen name inside the selected tab.</p>
                        </div>
                        <div>
                            <label class="form-label" for="cta_params_json">Internal params (JSON, optional)</label>
                            <input id="cta_params_json" type="text" name="cta_params_json" class="form-input"
                                   value="{{ old('cta_params_json', $banner?->cta_params ? json_encode($banner->cta_params) : '') }}"
                                   placeholder='e.g. {"slug":"help","fallbackTitle":"Help"}'>
                            <p class="form-help">Only needed for screens that require params.</p>
                        </div>
                    </div>

                    <div style="display:flex;gap:1.5rem;align-items:center;">
                        <label class="form-check">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $banner?->is_published ?? true) ? 'checked' : '' }}>
                            <span>Published – show in the mobile carousel.</span>
                        </label>
                        <div style="max-width:8rem;">
                            <label class="form-label" for="sort_order">Order</label>
                            <input id="sort_order" type="number" name="sort_order" class="form-input"
                                   value="{{ old('sort_order', $banner?->sort_order ?? 0) }}">
                            <p class="form-help">Lower numbers appear first.</p>
                        </div>
                    </div>
                </div>

                <div style="margin-top:1.25rem;display:flex;gap:0.75rem;">
                    <button type="submit" class="form-btn-primary">
                        {{ $banner ? 'Save changes' : 'Create banner' }}
                    </button>
                    <a href="{{ route('admin.home-banners.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                </div>
            </form>
        </section>
    </div>

    <style>
        .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
        .form-input { width:100%; padding:0.5rem 0.65rem; border:1px solid var(--border-subtle); border-radius:0.4rem; background:rgba(15,23,42,0.9); color:var(--text-main); font-size:0.95rem; }
        .form-input:focus { outline:none; border-color:var(--zanupf-gold); }
        .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; line-height:1.4; }
        .form-check { display:flex; align-items:flex-start; gap:0.6rem; cursor:pointer; font-size:0.9rem; }
        .form-check input[type="checkbox"] { width:1.1rem; height:1.1rem; margin-top:0.15rem; flex-shrink:0; }
        .form-btn-primary { padding:0.5rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.4rem; cursor:pointer; font-weight:600; font-size:0.9rem; }
        .form-btn-primary:hover { filter:brightness(1.1); }
    </style>
@endsection

