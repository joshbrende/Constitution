@extends('layouts.dashboard')

@section('title', $member ? 'Edit Presidium member' : 'Add Presidium member')
@section('page_heading', $member ? 'Edit Presidium member' : 'Add Presidium member')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">
                        {{ $member ? 'Edit Presidium member' : 'New Presidium member' }}
                    </div>
                    <div class="dash-panel-subtitle">
                        Keep names and titles exactly as used publicly. Order controls how members appear in the mobile Presidium screen.
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.presidium.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Back to Presidium</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="dash-alert dash-alert--error">
                    <ul style="margin:0;padding-left:1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ $member ? route('admin.presidium.update', $member) : route('admin.presidium.store') }}">
                @csrf
                @if($member)
                    @method('PUT')
                @endif

                <div class="form-grid">
                    <div class="form-grid-main">
                        <label class="form-label" for="presidium_member_name">Full name</label>
                        <input id="presidium_member_name" type="text" name="name" class="form-input"
                               value="{{ old('name', $member->name ?? '') }}" required>

                        <label class="form-label" for="presidium_member_title" style="margin-top:1rem;">Title</label>
                        <input id="presidium_member_title" type="text" name="title" class="form-input"
                               value="{{ old('title', $member->title ?? '') }}" required>

                        <label class="form-label" for="presidium_member_bio" style="margin-top:1rem;">Bio (optional)</label>
                        <textarea id="presidium_member_bio" name="bio" rows="5" class="form-input"
                                  placeholder="Short description for the Presidium member.">{{ old('bio', $member->bio ?? '') }}</textarea>
                    </div>

                    <div class="form-grid-side">
                        <label class="form-label" for="presidium_member_role_slug">Role key (slug)</label>
                        <input id="presidium_member_role_slug" type="text" name="role_slug" class="form-input"
                               value="{{ old('role_slug', $member->role_slug ?? '') }}"
                               placeholder="president, vice_president_1, secretary_general…" required>

                        <label class="form-label" for="presidium_member_order" style="margin-top:1rem;">Order</label>
                        <input id="presidium_member_order" type="number" name="order" class="form-input"
                               value="{{ old('order', $member->order ?? 1) }}" min="1">

                        <label class="form-label" for="presidium_member_photo_url" style="margin-top:1rem;">Photo URL (optional)</label>
                        <input id="presidium_member_photo_url" type="text" name="photo_url" class="form-input"
                               value="{{ old('photo_url', $member->photo_url ?? '') }}"
                               placeholder="https://…">

                        <label class="form-label" for="presidium_member_zanupf_section_id" style="margin-top:1rem;">ZANU PF article</label>
                        <select id="presidium_member_zanupf_section_id" name="zanupf_section_id" class="form-input">
                            <option value="">None</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ (string) old('zanupf_section_id', $member->zanupf_section_id ?? '') === (string) $section->id ? 'selected' : '' }}>
                                    {{ $section->title }}
                                </option>
                            @endforeach
                        </select>

                        <label class="form-label" for="presidium_member_zimbabwe_section_id" style="margin-top:1rem;">Zimbabwe Constitution article</label>
                        <select id="presidium_member_zimbabwe_section_id" name="zimbabwe_section_id" class="form-input">
                            <option value="">None</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ (string) old('zimbabwe_section_id', $member->zimbabwe_section_id ?? '') === (string) $section->id ? 'selected' : '' }}>
                                    {{ $section->title }}
                                </option>
                            @endforeach
                        </select>

                        <div style="margin-top:1rem;">
                            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.9rem;color:var(--text-main);">
                                <input type="checkbox" name="is_published" value="1"
                                    {{ old('is_published', $member->is_published ?? true) ? 'checked' : '' }}>
                                <span>Published (visible in the Presidium list)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div style="margin-top:1.25rem;display:flex;gap:0.75rem;">
                    <button type="submit" class="form-btn-primary">
                        {{ $member ? 'Save changes' : 'Create member' }}
                    </button>
                    <a href="{{ route('admin.presidium.index') }}" class="dash-btn-ghost" style="text-decoration:none;">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection

