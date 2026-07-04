@php
    $lines = $envRecommendations ?? [
        'APP_NAME' => 'ZANUPF',
        'APP_URL' => 'https://www.zanupf.org.zw',
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
    ];
    $snippetId = $snippetId ?? 'env-snippet-text';
@endphp

<div class="env-snippet-wrap">
    <div class="env-snippet-head">
        <div class="env-snippet-head-title">
            <strong>Copy for hosting panel / <code>.env</code></strong>
            @include('setup.partials.field-tip', [
                'tip' => 'Give this block to your hosting team. The wizard saves settings in the database only — these values must be set on the server separately.',
                'aria' => 'Environment snippet help',
            ])
        </div>
        <button type="button" class="env-copy-btn" data-copy-target="{{ $snippetId }}">Copy</button>
    </div>
    <p class="env-snippet-note">
        The wizard saves platform settings in the <strong>database</strong> only.
        Your technical team must set these on the server (environment variables or <code>backend/.env</code>).
    </p>
    <pre id="{{ $snippetId }}" class="env-snippet" aria-label="Recommended environment variables">APP_NAME={{ $lines['APP_NAME'] }}
APP_URL={{ $lines['APP_URL'] }}
APP_ENV={{ $lines['APP_ENV'] }}
APP_DEBUG={{ $lines['APP_DEBUG'] }}</pre>
    <table class="env-map kv" role="presentation">
        <tr><td class="k">Organisation name</td><td class="v">→ <code>APP_NAME</code></td></tr>
        <tr><td class="k">Production URL (step 4)</td><td class="v">→ <code>APP_URL</code></td></tr>
        <tr><td class="k">Environment</td><td class="v">→ always <code>production</code> on live servers</td></tr>
        <tr><td class="k">Debug</td><td class="v">→ always <code>false</code> on live servers</td></tr>
    </table>
</div>

@once
    @push('head')
        <style>
            .env-snippet-wrap{ margin-top:14px; }
            .env-snippet-head{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:8px; }
            .env-snippet-head-title{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0; }
            .env-snippet-head strong{ font-size:13px; }
            .env-snippet-note{ margin:0 0 10px; font-size:12px; line-height:1.5; color:var(--muted); }
            .env-snippet{
                margin:0;
                padding:14px;
                border-radius:4px;
                background:#0f172a;
                color:#e2e8f0;
                font-size:12px;
                line-height:1.6;
                overflow-x:auto;
                border:1px solid #334155;
            }
            .env-copy-btn{
                border:1px solid #cbd5e1;
                background:#fff;
                border-radius:4px;
                padding:6px 12px;
                font-size:12px;
                font-weight:700;
                cursor:pointer;
            }
            .env-copy-btn:hover{ background:#f8fafc; }
            .env-map{ margin-top:12px; font-size:12px; }
        </style>
        <script>
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-copy-target]');
                if (!btn) return;
                var el = document.getElementById(btn.getAttribute('data-copy-target'));
                if (!el) return;
                navigator.clipboard.writeText(el.textContent.trim()).then(function () {
                    var prev = btn.textContent;
                    btn.textContent = 'Copied!';
                    setTimeout(function () { btn.textContent = prev; }, 1500);
                });
            });
        </script>
    @endpush
@endonce
