<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup Wizard • {{ $defaults['org_name'] ?? 'ZANUPF' }}</title>
    <style>
        :root{
            --zanupf-green:#166534;
            --zanupf-gold:#facc15;
            --ink:#0b1220;
            --muted:#475569;
            --card:#ffffff;
            --border:rgba(2,6,23,.14);
        }
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Liberation Sans", sans-serif;
            color:var(--ink);
            background:
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.90)),
                url("{{ asset('bg-1.jpg') }}") center/cover no-repeat fixed;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding: 28px 16px;
        }
        .wrap{width:100%; max-width: 980px;}
        .card{
            background: rgba(255,255,255,.93);
            border: 1px solid rgba(2,6,23,.12);
            border-radius: 18px;
            overflow:hidden;
            box-shadow: 0 24px 60px rgba(2,6,23,.18);
        }
        .head{
            display:flex;
            gap: 18px;
            padding: 18px 18px 14px 18px;
            align-items:center;
            border-bottom: 1px solid rgba(2,6,23,.10);
            background: linear-gradient(90deg, rgba(22,101,52,.08), rgba(250,204,21,.08));
        }
        .logo{
            width: 64px; height: 64px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid rgba(2,6,23,.10);
            display:flex; align-items:center; justify-content:center;
            overflow:hidden;
            flex: 0 0 auto;
        }
        .logo img{ width: 54px; height: 54px; object-fit: contain; }
        .title h1{ margin:0; font-size: 18px; letter-spacing:.2px;}
        .title p{ margin:6px 0 0; color: var(--muted); font-size: 13px; line-height:1.35;}
        .body{ padding: 18px; display:grid; grid-template-columns: minmax(0, 1fr) minmax(0, .9fr); gap: 18px;}
        @media (max-width: 900px){ .body{ grid-template-columns: 1fr; } }
        .panel{
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(2,6,23,.10);
            border-radius: 14px;
            padding: 14px;
        }
        .panel h2{ margin:0 0 10px; font-size: 14px; }
        .panel p{ margin:0 0 10px; color: var(--muted); font-size: 13px; line-height:1.5;}
        .grid{ display:grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 620px){ .grid{ grid-template-columns: 1fr; } }
        label{ display:block; font-size: 12px; color: var(--muted); margin-bottom: 6px;}
        input[type="text"], input[type="email"], input[type="url"]{
            width:100%;
            padding: 11px 12px;
            border-radius: 12px;
            border: 1px solid rgba(2,6,23,.16);
            outline:none;
            background: #fff;
            font-size: 13px;
        }
        input:focus{
            border-color: rgba(22,101,52,.55);
            box-shadow: 0 0 0 4px rgba(22,101,52,.12);
        }
        .row{ margin-bottom: 10px; }
        .full{ grid-column: 1 / -1; }
        .tog{
            display:flex; justify-content:space-between; align-items:center;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(2,6,23,.10);
            background: rgba(15,23,42,.03);
            gap: 10px;
        }
        .tog strong{ font-size: 13px; }
        .tog small{ display:block; color: var(--muted); font-size: 12px; margin-top: 2px; line-height:1.35;}
        .btns{ display:flex; gap: 10px; justify-content:flex-end; margin-top: 12px; }
        .btn{
            border:none;
            border-radius: 12px;
            padding: 11px 14px;
            font-weight: 700;
            font-size: 13px;
            cursor:pointer;
        }
        .btn-primary{
            background: linear-gradient(180deg, rgba(22,101,52,1), rgba(17,94,46,1));
            color:#fff;
            box-shadow: 0 10px 22px rgba(22,101,52,.22);
        }
        .btn-primary:hover{ filter: brightness(1.03); }
        .btn-ghost{
            background: rgba(2,6,23,.05);
            color: rgba(2,6,23,.88);
            border: 1px solid rgba(2,6,23,.10);
        }
        .err{
            background: rgba(239,68,68,.10);
            border: 1px solid rgba(239,68,68,.25);
            color: rgba(127,29,29,1);
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .hint{
            border-left: 4px solid var(--zanupf-gold);
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(250,204,21,.11);
            border: 1px solid rgba(250,204,21,.22);
            color: rgba(2,6,23,.86);
            font-size: 13px;
            line-height: 1.45;
        }
        .kv{
            width:100%;
            border-collapse: collapse;
            font-size: 12.5px;
            margin-top: 6px;
        }
        .kv td{
            padding: 8px 0;
            border-bottom: 1px solid rgba(2,6,23,.08);
            vertical-align: top;
        }
        .kv tr:last-child td{ border-bottom:none; }
        .kv .k{ color: var(--muted); width: 34%; padding-right: 10px; }
        .kv .v{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        .pill{
            display:inline-block;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(2,6,23,.06);
            border: 1px solid rgba(2,6,23,.10);
            color: rgba(2,6,23,.86);
            margin-left: 6px;
        }
        .warn{
            margin-top: 10px;
            padding: 9px 11px;
            border-radius: 12px;
            background: rgba(239,68,68,.08);
            border: 1px solid rgba(239,68,68,.18);
            color: rgba(127,29,29,1);
            font-size: 12.5px;
            line-height: 1.45;
        }
        code{ background: rgba(2,6,23,.06); padding: 0.12rem 0.35rem; border-radius: 8px; }
        .foot{
            padding: 12px 18px 16px;
            color: var(--muted);
            font-size: 12px;
            border-top: 1px solid rgba(2,6,23,.08);
            display:flex; justify-content:space-between; gap: 10px; flex-wrap: wrap;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            <div class="logo" aria-hidden="true">
                <img src="{{ asset('Logo.png') }}" alt="">
            </div>
            <div class="title">
                <h1>Setup Wizard</h1>
                <p>Run once to configure your platform defaults. Only <strong>system_admin</strong> can complete this wizard.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('setup.store') }}">
            @csrf
            <div class="body">
                <div class="panel">
                    <h2>Branding and contact</h2>

                    @if ($errors->any())
                        <div class="err">
                            Please fix the highlighted fields and try again.
                        </div>
                    @endif

                    <div class="grid">
                        <div class="row">
                            <label for="org_name">Organisation name</label>
                            <input id="org_name" name="org_name" type="text" value="{{ old('org_name', $defaults['org_name'] ?? '') }}" required>
                            @error('org_name')<div style="color:#7f1d1d;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <label for="support_email">Support email</label>
                            <input id="support_email" name="support_email" type="email" value="{{ old('support_email', $defaults['support_email'] ?? '') }}" required>
                            @error('support_email')<div style="color:#7f1d1d;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="row full">
                            <label for="public_site_url">Public site URL (optional)</label>
                            <input id="public_site_url" name="public_site_url" type="url" value="{{ old('public_site_url', $defaults['public_site_url'] ?? '') }}" placeholder="https://zanupfonline.org.zw">
                            @error('public_site_url')<div style="color:#7f1d1d;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h2 style="margin-top:14px;">Legal links</h2>
                    <div class="grid">
                        <div class="row full">
                            <label for="legal_privacy_url">Privacy policy URL</label>
                            <input id="legal_privacy_url" name="legal_privacy_url" type="text" value="{{ old('legal_privacy_url', $defaults['legal_privacy_url'] ?? '') }}" required>
                            @error('legal_privacy_url')<div style="color:#7f1d1d;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="row full">
                            <label for="legal_terms_url">Terms of use URL</label>
                            <input id="legal_terms_url" name="legal_terms_url" type="text" value="{{ old('legal_terms_url', $defaults['legal_terms_url'] ?? '') }}" required>
                            @error('legal_terms_url')<div style="color:#7f1d1d;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="row full">
                            <label for="legal_cookies_url">Cookies URL</label>
                            <input id="legal_cookies_url" name="legal_cookies_url" type="text" value="{{ old('legal_cookies_url', $defaults['legal_cookies_url'] ?? '') }}" required>
                            @error('legal_cookies_url')<div style="color:#7f1d1d;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <h2>Operational toggles</h2>
                    <p>These are safe defaults you can change later in Admin settings.</p>

                    <div class="row">
                        <div class="tog">
                            <div>
                                <strong>Enable Dialogue (Chat)</strong>
                                <small>Controls whether the mobile app should show chat.</small>
                            </div>
                            <input type="hidden" name="enable_dialogue" value="0">
                            <input type="checkbox" name="enable_dialogue" value="1" {{ old('enable_dialogue', $defaults['enable_dialogue'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="row">
                        <div class="tog">
                            <div>
                                <strong>Require National ID</strong>
                                <small>Keep enabled now; Govt verification can be integrated later.</small>
                            </div>
                            <input type="hidden" name="require_national_id" value="0">
                            <input type="checkbox" name="require_national_id" value="1" {{ old('require_national_id', $defaults['require_national_id'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="hint" style="margin-top:12px;">
                        Mobile config tip: your app will use the API base URL you compile into Expo, e.g. <code>EXPO_PUBLIC_API_BASE_URL</code>.
                        If your domain changes later, update Expo config and rebuild the app.
                    </div>

                    <h2 style="margin-top:14px;">Server config checklist</h2>
                    <p>This wizard does <strong>not</strong> write <code>.env</code>. Before going live, ensure the server environment is correct.</p>

                    <div style="margin-top:10px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(2,6,23,.10); background: rgba(15,23,42,.02);">
                        <div style="font-weight:700; font-size: 13px;">Current (detected)</div>
                        <table class="kv" role="presentation">
                            <tr><td class="k">APP_NAME</td><td class="v">{{ (string) ($serverConfig['current']['APP_NAME'] ?? '') }}</td></tr>
                            <tr><td class="k">APP_URL</td><td class="v">{{ (string) ($serverConfig['current']['APP_URL'] ?? '') }}</td></tr>
                            <tr><td class="k">APP_ENV</td><td class="v">{{ (string) ($serverConfig['current']['APP_ENV'] ?? '') }}</td></tr>
                            <tr><td class="k">APP_DEBUG</td><td class="v">{{ !empty($serverConfig['current']['APP_DEBUG']) ? 'true' : 'false' }}</td></tr>
                        </table>
                    </div>

                    <div style="margin-top:10px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(2,6,23,.10); background: rgba(22,101,52,.05);">
                        <div style="font-weight:700; font-size: 13px;">
                            Recommended for production
                            <span class="pill">set via hosting env vars / .env</span>
                        </div>
                        <table class="kv" role="presentation">
                            <tr><td class="k">APP_NAME</td><td class="v">{{ (string) ($serverConfig['recommended']['APP_NAME'] ?? 'ZANUPF') }}</td></tr>
                            <tr><td class="k">APP_URL</td><td class="v">{{ (string) ($serverConfig['recommended']['APP_URL'] ?? 'https://your-domain.example') }}</td></tr>
                            <tr><td class="k">APP_ENV</td><td class="v">production</td></tr>
                            <tr><td class="k">APP_DEBUG</td><td class="v">false</td></tr>
                        </table>
                    </div>

                    @php
                        $curUrl = (string) ($serverConfig['current']['APP_URL'] ?? '');
                        $looksDev = str_contains($curUrl, 'localhost') || str_contains($curUrl, '127.0.0.1') || str_contains($curUrl, '.test') || str_contains($curUrl, ':8080');
                        $curEnv = (string) ($serverConfig['current']['APP_ENV'] ?? '');
                        $curDebug = !empty($serverConfig['current']['APP_DEBUG']);
                    @endphp
                    @if ($looksDev || $curEnv !== 'production' || $curDebug)
                        <div class="warn">
                            Heads-up: your current server configuration looks like a <strong>development</strong> setup. Production should use a real domain (<code>APP_URL</code>), <code>APP_ENV=production</code>, and <code>APP_DEBUG=false</code>.
                        </div>
                    @endif

                    <div class="btns">
                        <a class="btn btn-ghost" href="{{ route('dashboard') }}" style="text-decoration:none;display:inline-flex;align-items:center;">Cancel</a>
                        <button type="submit" class="btn btn-primary">Complete setup</button>
                    </div>
                </div>
            </div>

            <div class="foot">
                <div>© {{ date('Y') }} • Setup Wizard</div>
                <div>Theme: ZANU PF</div>
            </div>
        </form>
    </div>
</div>
</body>
</html>

