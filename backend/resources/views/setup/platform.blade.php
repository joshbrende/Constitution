@extends('setup.layout')

@section('title', 'Platform settings')
@section('heading', 'Software setup')
@section('subheading')
    Configure your organisation, <strong>installation URL</strong>, and platform defaults.
    The installation URL becomes <code>APP_URL</code> in your hosting environment.
@endsection
@section('body_class', '')

@section('content')
    <form id="setup-platform-form" method="POST" action="{{ route('setup.platform.store') }}">
        @csrf

        <div class="section">
            @if ($errors->any())
                <div class="err">Please fix the highlighted fields and try again.</div>
            @endif

            @include('setup.partials.installation-url', [
                'installUrl' => $installUrl,
                'domainOptions' => $domainOptions,
                'defaults' => $defaults,
            ])

            @error('install_directory')<div style="color:#991b1b;font-size:12px;margin:-8px 0 12px;">{{ $message }}</div>@enderror
            @error('install_domain')<div style="color:#991b1b;font-size:12px;margin:-8px 0 12px;">{{ $message }}</div>@enderror

            <div class="section-heading" style="margin-bottom:8px;">
                <h3>Organisation</h3>
                @include('setup.partials.field-tip', [
                    'tip' => 'Core identity settings stored in the database. Organisation name also maps to <strong>APP_NAME</strong> in your hosting environment.',
                    'aria' => 'Organisation section help',
                ])
            </div>
            <div class="grid">
                <div class="row">
                    @include('setup.partials.field-label', [
                        'for' => 'org_name',
                        'text' => 'Organisation name → <code>APP_NAME</code>',
                        'tip' => 'Displayed in the app title, emails, and PDFs. Your hosting team should set the same value as <strong>APP_NAME</strong> in <code>.env</code>.',
                        'tipAria' => 'Organisation name help',
                    ])
                    <input id="org_name" name="org_name" type="text" value="{{ old('org_name', $defaults['org_name'] ?? '') }}" required>
                    @error('org_name')<div style="color:#991b1b;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    @include('setup.partials.field-label', [
                        'for' => 'support_email',
                        'text' => 'Support email',
                        'tip' => 'Public contact address for member support inquiries shown in the app and portal.',
                        'tipAria' => 'Support email help',
                    ])
                    <input id="support_email" name="support_email" type="email" value="{{ old('support_email', $defaults['support_email'] ?? '') }}" placeholder="support@zanupf.org.zw" required>
                    @error('support_email')<div style="color:#991b1b;font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
                </div>
            </div>

            @include('setup.partials.env-snippet', [
                'envRecommendations' => $envRecommendations,
                'snippetId' => 'env-snippet-platform',
            ])

            <div class="section-heading" style="margin-top:22px;margin-bottom:8px;">
                <h3>Legal links</h3>
                @include('setup.partials.field-tip', [
                    'tip' => 'Full URLs to your legal pages. These are shown to members in the mobile app and web portal.',
                    'aria' => 'Legal links section help',
                ])
            </div>
            <div class="grid">
                <div class="row full">
                    @include('setup.partials.field-label', [
                        'for' => 'legal_privacy_url',
                        'text' => 'Privacy policy URL',
                        'tip' => 'Link to your privacy policy page, including <strong>https://</strong>.',
                        'tipAria' => 'Privacy policy help',
                    ])
                    <input id="legal_privacy_url" name="legal_privacy_url" type="text" value="{{ old('legal_privacy_url', $defaults['legal_privacy_url'] ?? '') }}" placeholder="https://www.zanupf.org.zw/privacy" required>
                </div>
                <div class="row full">
                    @include('setup.partials.field-label', [
                        'for' => 'legal_terms_url',
                        'text' => 'Terms of use URL',
                        'tip' => 'Link to your terms of service page, including <strong>https://</strong>.',
                        'tipAria' => 'Terms of use help',
                    ])
                    <input id="legal_terms_url" name="legal_terms_url" type="text" value="{{ old('legal_terms_url', $defaults['legal_terms_url'] ?? '') }}" placeholder="https://www.zanupf.org.zw/terms" required>
                </div>
                <div class="row full">
                    @include('setup.partials.field-label', [
                        'for' => 'legal_cookies_url',
                        'text' => 'Cookies URL',
                        'tip' => 'Link to your cookies policy page, including <strong>https://</strong>.',
                        'tipAria' => 'Cookies policy help',
                    ])
                    <input id="legal_cookies_url" name="legal_cookies_url" type="text" value="{{ old('legal_cookies_url', $defaults['legal_cookies_url'] ?? '') }}" placeholder="https://www.zanupf.org.zw/cookies" required>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-heading" style="margin-bottom:8px;">
                <h3>Operational toggles</h3>
                @include('setup.partials.field-tip', [
                    'tip' => 'Platform behaviour flags saved in the database. You can change these later from admin settings.',
                    'aria' => 'Operational toggles help',
                ])
            </div>
            <div class="row">
                <div class="tog">
                    <div>
                        <div class="tog-title">
                            <strong>Enable Dialogue (Chat)</strong>
                            @include('setup.partials.field-tip', [
                                'tip' => 'When enabled, the mobile app shows dialogue channels and chat features for members.',
                                'aria' => 'Dialogue toggle help',
                            ])
                        </div>
                        <small>Controls whether the mobile app shows chat.</small>
                    </div>
                    <input type="hidden" name="enable_dialogue" value="0">
                    <input type="checkbox" name="enable_dialogue" value="1" {{ old('enable_dialogue', $defaults['enable_dialogue'] ?? true) ? 'checked' : '' }}>
                </div>
            </div>
            <div class="row">
                <div class="tog">
                    <div>
                        <div class="tog-title">
                            <strong>Require National ID</strong>
                            @include('setup.partials.field-tip', [
                                'tip' => 'When enabled, members must provide a national ID during registration. Government verification can be integrated later.',
                                'aria' => 'National ID toggle help',
                            ])
                        </div>
                        <small>Government verification can be integrated later.</small>
                    </div>
                    <input type="hidden" name="require_national_id" value="0">
                    <input type="checkbox" name="require_national_id" value="1" {{ old('require_national_id', $defaults['require_national_id'] ?? true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>
    </form>

    <div class="section">
        <div class="section-heading" style="margin-bottom:8px;">
            <h3>Compare with current server</h3>
            @include('setup.partials.field-tip', [
                'tip' => 'Shows what the server is running now versus what the wizard recommends after you save platform settings.',
                'aria' => 'Server comparison help',
            ])
        </div>
        @include('setup.partials.server-config', [
            'serverConfig' => $serverConfig,
            'withEnvSnippet' => false,
        ])
    </div>
@endsection

@push('head')
<script>
    (function () {
        var protocol = document.getElementById('install_protocol');
        var domain = document.getElementById('install_domain');
        var directory = document.getElementById('install_directory');
        var preview = document.getElementById('install-url-preview-text');
        var hiddenUrl = document.getElementById('public_site_url');
        var org = document.getElementById('org_name');
        var pre = document.getElementById('env-snippet-platform');
        var subdirHint = document.getElementById('install-subdirectory-hint');
        if (!protocol || !domain || !preview) return;

        function buildUrl() {
            var p = protocol.value === 'http' ? 'http' : 'https';
            var d = (domain.value || '').trim().replace(/\/+$/, '');
            var dir = (directory && directory.value ? directory.value : '').trim().replace(/^\/+|\/+$/g, '');
            if (!d) return p + '://www.zanupf.org.zw';
            var url = p + '://' + d;
            if (dir) url += '/' + dir;
            return url;
        }

        function sync() {
            var url = buildUrl();
            preview.textContent = url;
            if (hiddenUrl) hiddenUrl.value = url;
            if (subdirHint && directory) {
                subdirHint.style.display = (directory.value || '').trim() !== '' ? 'block' : 'none';
            }
            if (org && pre) {
                var name = org.value.trim() || 'ZANUPF';
                pre.textContent = 'APP_NAME=' + name + '\nAPP_URL=' + url + '\nAPP_ENV=production\nAPP_DEBUG=false';
            }
        }

        [protocol, domain, directory, org].forEach(function (el) {
            if (el) el.addEventListener('input', sync);
            if (el) el.addEventListener('change', sync);
        });
        sync();
    })();
</script>
@endpush

@section('footer_left')
    <a href="{{ route('setup.admin') }}" class="footer-link">Back</a>
@endsection

@section('footer_right')
    @include('setup.partials.btn-next', [
        'label' => 'Save and continue',
        'form' => 'setup-platform-form',
    ])
@endsection
