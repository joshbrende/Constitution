@extends('layouts.dashboard')

@section('title', $campaign ? 'Edit notification' : 'New notification')
@section('page_heading', $campaign ? 'Edit notification' : 'New notification')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2; max-width: 720px;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $campaign ? 'Edit draft' : 'Compose notification' }}</div>
                    <div class="dash-panel-subtitle">Published notifications cannot be changed. Members see these in the mobile app bell inbox.</div>
                </div>
                <a href="{{ route('admin.member-notifications.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Notifications</a>
            </div>

            @if ($errors->any())
                <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ $campaign ? route('admin.member-notifications.update', $campaign) : route('admin.member-notifications.store') }}">
                @csrf
                @if ($campaign)
                    @method('PUT')
                @endif

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Title</label>
                    <input type="text" name="title" value="{{ old('title', $campaign->title ?? '') }}" required maxlength="120" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Message</label>
                    <textarea name="body" rows="5" required maxlength="5000" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">{{ old('body', $campaign->body ?? '') }}</textarea>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Audience</label>
                    <select name="audience_type" id="audience_type" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                        @foreach (['all' => 'All members', 'province' => 'One province', 'role' => 'One role'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('audience_type', $campaign->audience_type ?? 'all') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="province_field" style="margin-bottom:1rem;display:none;">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Province</label>
                    <select name="province_id" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                        <option value="">Select province</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}" @selected((string) old('province_id', $campaign->province_id ?? '') === (string) $province->id)>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="role_field" style="margin-bottom:1rem;display:none;">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Role</label>
                    <select name="role_id" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                        <option value="">Select role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id', $campaign->role_id ?? '') === (string) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Call to action</label>
                    <select name="cta_type" id="cta_type" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                        @foreach (['none' => 'None (message only)', 'internal' => 'Open app screen', 'external' => 'Open website URL'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('cta_type', $campaign->cta_type ?? 'none') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="cta_fields" style="display:none;">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Button label (optional)</label>
                        <input type="text" name="cta_label" value="{{ old('cta_label', $campaign->cta_label ?? '') }}" maxlength="80" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                    </div>
                    <div id="cta_internal" style="display:none;">
                        <div style="margin-bottom:1rem;">
                            <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Tab (e.g. HomeTab, ConstitutionTab)</label>
                            <input type="text" name="cta_tab" value="{{ old('cta_tab', $campaign->cta_tab ?? 'HomeTab') }}" maxlength="50" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                        </div>
                        <div style="margin-bottom:1rem;">
                            <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Screen (e.g. AcademyHome, Presidium)</label>
                            <input type="text" name="cta_screen" value="{{ old('cta_screen', $campaign->cta_screen ?? '') }}" maxlength="80" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                        </div>
                        <div style="margin-bottom:1rem;">
                            <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Screen params (JSON, optional)</label>
                            <textarea name="cta_params_json" rows="2" maxlength="2000" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);font-family:monospace;font-size:0.85rem;">{{ old('cta_params_json', $campaign?->cta_params ? json_encode($campaign->cta_params, JSON_PRETTY_PRINT) : '') }}</textarea>
                        </div>
                    </div>
                    <div id="cta_external" style="display:none;margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">URL</label>
                        <input type="url" name="cta_url" value="{{ old('cta_url', $campaign->cta_url ?? '') }}" maxlength="500" style="width:100%;padding:0.5rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);">
                    </div>
                </div>

                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:1.25rem;">
                    <button type="submit" name="publish_now" value="0" style="padding:0.5rem 1rem;border-radius:0.4rem;border:1px solid var(--border-subtle);background:var(--bg-elevated);color:var(--text-main);cursor:pointer;font-weight:600;">Save draft</button>
                    <button type="submit" name="publish_now" value="1" style="padding:0.5rem 1rem;border-radius:0.4rem;border:none;background:var(--zanupf-green);color:#fff;cursor:pointer;font-weight:600;">Save &amp; publish now</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const audience = document.getElementById('audience_type');
            const provinceField = document.getElementById('province_field');
            const roleField = document.getElementById('role_field');
            const ctaType = document.getElementById('cta_type');
            const ctaFields = document.getElementById('cta_fields');
            const ctaInternal = document.getElementById('cta_internal');
            const ctaExternal = document.getElementById('cta_external');

            function syncAudience() {
                const value = audience.value;
                provinceField.style.display = value === 'province' ? 'block' : 'none';
                roleField.style.display = value === 'role' ? 'block' : 'none';
            }

            function syncCta() {
                const value = ctaType.value;
                ctaFields.style.display = value === 'none' ? 'none' : 'block';
                ctaInternal.style.display = value === 'internal' ? 'block' : 'none';
                ctaExternal.style.display = value === 'external' ? 'block' : 'none';
            }

            audience.addEventListener('change', syncAudience);
            ctaType.addEventListener('change', syncCta);
            syncAudience();
            syncCta();
        });
    </script>
@endsection
