@extends('layouts.dashboard')

@section('title', 'Platform settings')
@section('page_heading', 'Platform settings')

@section('content')
<style>
    .ps-wrap { max-width: 980px; }
    .ps-card {
        background: var(--bg-panel);
        border: 1px solid var(--border-subtle);
        border-radius: 0.95rem;
        padding: 1.05rem 1.1rem;
    }
    .ps-card h2 { margin:0 0 0.75rem 0; font-size:0.95rem; font-weight:700; }
    .ps-grid { display:grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; }
    @media (max-width: 900px){ .ps-grid { grid-template-columns: 1fr; } }
    .ps-row label { display:block; font-size:0.82rem; color: var(--text-muted); margin-bottom: 0.35rem; }
    .ps-row input[type="text"], .ps-row input[type="email"], .ps-row input[type="url"]{
        width:100%;
        padding: 0.7rem 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border-subtle);
        background: rgba(15,23,42,0.04);
        color: var(--text-main);
        outline: none;
    }
    body.theme-light .ps-row input[type="text"], body.theme-light .ps-row input[type="email"], body.theme-light .ps-row input[type="url"]{
        background: #fff;
    }
    .ps-full { grid-column: 1 / -1; }
    .ps-tog{
        display:flex; align-items:center; justify-content:space-between; gap: 0.8rem;
        padding: 0.7rem 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border-subtle);
        background: rgba(15,23,42,0.04);
    }
    body.theme-light .ps-tog{ background:#fff; }
    .ps-btns{ display:flex; justify-content:flex-end; gap: 0.6rem; margin-top: 0.9rem; }
    .ps-btn{
        display:inline-flex; align-items:center; justify-content:center;
        padding: 0.65rem 0.85rem;
        border-radius: 0.8rem;
        font-weight: 700;
        border: 1px solid var(--border-subtle);
        cursor:pointer;
        text-decoration:none;
    }
    .ps-btn-primary{ background: rgba(22,101,52,0.18); color: var(--text-main); }
    body.theme-light .ps-btn-primary{ background: #166534; color: #fff; border-color: rgba(22,101,52,0.55); }
    .ps-note{ color: var(--text-muted); font-size: 0.85rem; margin: 0 0 0.85rem 0; line-height:1.5; }
</style>

<div class="ps-wrap">
    <p class="ps-note">
        These settings are stored in the database and are safe to update without editing server environment files.
    </p>

    @if (session('success'))
        <div style="margin:0 0 0.9rem 0; padding:0.75rem 0.9rem; border-radius:0.9rem; border:1px solid rgba(34,197,94,0.25); background:rgba(34,197,94,0.12); color:var(--text-main);">
            {{ session('success') }}
        </div>
    @endif

    <div class="ps-card">
        <h2>Branding and legal</h2>

        <form method="POST" action="{{ route('admin.platform-settings.update') }}">
            @csrf
            @method('PUT')

            <div class="ps-grid">
                <div class="ps-row">
                    <label for="org_name">Organisation name</label>
                    <input id="org_name" type="text" name="org_name" value="{{ old('org_name', $settings['org_name'] ?? '') }}" required>
                    @error('org_name')<div style="color:#ef4444;font-size:0.8rem;margin-top:0.35rem;">{{ $message }}</div>@enderror
                </div>

                <div class="ps-row">
                    <label for="support_email">Support email</label>
                    <input id="support_email" type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? '') }}" required>
                    @error('support_email')<div style="color:#ef4444;font-size:0.8rem;margin-top:0.35rem;">{{ $message }}</div>@enderror
                </div>

                <div class="ps-row ps-full">
                    <label for="public_site_url">Public site URL (optional)</label>
                    <input id="public_site_url" type="url" name="public_site_url" value="{{ old('public_site_url', $settings['public_site_url'] ?? '') }}" placeholder="https://zanupfonline.org.zw">
                    @error('public_site_url')<div style="color:#ef4444;font-size:0.8rem;margin-top:0.35rem;">{{ $message }}</div>@enderror
                </div>

                <div class="ps-row ps-full">
                    <label for="legal_privacy_url">Privacy policy URL</label>
                    <input id="legal_privacy_url" type="text" name="legal_privacy_url" value="{{ old('legal_privacy_url', $settings['legal_privacy_url'] ?? '') }}" required>
                    @error('legal_privacy_url')<div style="color:#ef4444;font-size:0.8rem;margin-top:0.35rem;">{{ $message }}</div>@enderror
                </div>

                <div class="ps-row ps-full">
                    <label for="legal_terms_url">Terms of use URL</label>
                    <input id="legal_terms_url" type="text" name="legal_terms_url" value="{{ old('legal_terms_url', $settings['legal_terms_url'] ?? '') }}" required>
                    @error('legal_terms_url')<div style="color:#ef4444;font-size:0.8rem;margin-top:0.35rem;">{{ $message }}</div>@enderror
                </div>

                <div class="ps-row ps-full">
                    <label for="legal_cookies_url">Cookies URL</label>
                    <input id="legal_cookies_url" type="text" name="legal_cookies_url" value="{{ old('legal_cookies_url', $settings['legal_cookies_url'] ?? '') }}" required>
                    @error('legal_cookies_url')<div style="color:#ef4444;font-size:0.8rem;margin-top:0.35rem;">{{ $message }}</div>@enderror
                </div>

                <div class="ps-row ps-full">
                    <div class="ps-tog">
                        <div>
                            <div style="font-weight:700;color:var(--text-main);">Enable Dialogue (Chat)</div>
                            <div style="color:var(--text-muted);font-size:0.82rem;margin-top:0.15rem;">Used by mobile UI to decide whether to show chat.</div>
                        </div>
                        <input type="hidden" name="enable_dialogue" value="0">
                        <input type="checkbox" name="enable_dialogue" value="1" {{ old('enable_dialogue', $settings['enable_dialogue'] ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <div class="ps-row ps-full">
                    <div class="ps-tog">
                        <div>
                            <div style="font-weight:700;color:var(--text-main);">Require National ID</div>
                            <div style="color:var(--text-muted);font-size:0.82rem;margin-top:0.15rem;">Keep enabled; verification integration can follow later.</div>
                        </div>
                        <input type="hidden" name="require_national_id" value="0">
                        <input type="checkbox" name="require_national_id" value="1" {{ old('require_national_id', $settings['require_national_id'] ?? true) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="ps-btns">
                <a class="ps-btn" href="{{ route('admin.guide.settings') }}">Back</a>
                <button class="ps-btn ps-btn-primary" type="submit">Save changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

