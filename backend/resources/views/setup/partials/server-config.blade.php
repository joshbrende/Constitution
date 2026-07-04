<p class="env-snippet-note" style="margin-top:0;">
    On <strong>step 1</strong>, values below are placeholders until you complete
    <strong>step 4 — Platform settings</strong> (organisation name + installation URL).
    The wizard never writes <code>.env</code>; use the copy block to configure the server.
</p>

<div class="config-box">
    <div class="config-box-title">Current (detected from server)</div>
    <table class="kv" role="presentation">
        <tr><td class="k">APP_NAME</td><td class="v">{{ (string) ($serverConfig['current']['APP_NAME'] ?? '') }}</td></tr>
        <tr><td class="k">APP_URL</td><td class="v">{{ (string) ($serverConfig['current']['APP_URL'] ?? '') }}</td></tr>
        <tr><td class="k">APP_ENV</td><td class="v">{{ (string) ($serverConfig['current']['APP_ENV'] ?? '') }}</td></tr>
        <tr><td class="k">APP_DEBUG</td><td class="v">{{ !empty($serverConfig['current']['APP_DEBUG']) ? 'true' : 'false' }}</td></tr>
    </table>
</div>

<div class="config-box">
    <div class="config-box-title">
        Recommended for production
        <span class="pill">from wizard inputs → hosting env / .env</span>
    </div>
    <table class="kv" role="presentation">
        <tr><td class="k">APP_NAME</td><td class="v">{{ (string) ($serverConfig['recommended']['APP_NAME'] ?? 'ZANUPF') }}</td></tr>
        <tr><td class="k">APP_URL</td><td class="v">{{ (string) ($serverConfig['recommended']['APP_URL'] ?? 'https://www.zanupf.org.zw') }}</td></tr>
        <tr><td class="k">APP_ENV</td><td class="v">production</td></tr>
        <tr><td class="k">APP_DEBUG</td><td class="v">false</td></tr>
    </table>
</div>

@if ($withEnvSnippet ?? true)
@include('setup.partials.env-snippet', [
    'envRecommendations' => [
        'APP_NAME' => (string) ($serverConfig['recommended']['APP_NAME'] ?? 'ZANUPF'),
        'APP_URL' => (string) ($serverConfig['recommended']['APP_URL'] ?? 'https://www.zanupf.org.zw'),
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
    ],
    'snippetId' => $snippetId ?? 'env-snippet-server',
])
@endif

@php
    $curUrl = (string) ($serverConfig['current']['APP_URL'] ?? '');
    $looksDev = str_contains($curUrl, 'localhost') || str_contains($curUrl, '127.0.0.1') || str_contains($curUrl, '.test') || str_contains($curUrl, ':8080') || str_contains($curUrl, ':8081');
    $curEnv = (string) ($serverConfig['current']['APP_ENV'] ?? '');
    $curDebug = !empty($serverConfig['current']['APP_DEBUG']);
@endphp
@if ($looksDev || $curEnv !== 'production' || $curDebug)
    <div class="warn">
        Your server still looks like <strong>development</strong>. After install, set the copied
        <code>APP_*</code> values on the host, then run <code>php artisan config:clear</code>.
    </div>
@endif
