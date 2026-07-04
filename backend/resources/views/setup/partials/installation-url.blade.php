@php
    $install = $installUrl ?? ['protocol' => 'https', 'domain' => 'localhost', 'directory' => ''];
    $domainOptions = $domainOptions ?? [$install['domain']];
    $protocolValue = old('install_protocol', $install['protocol'] ?? 'https');
    $domainValue = old('install_domain', $install['domain'] ?? '');
    $directoryValue = old('install_directory', $install['directory'] ?? '');
@endphp

<div class="install-url-block">
    <h3 class="install-url-title">
        Choose Installation URL
        @include('setup.partials.field-tip', [
            'tip' => 'The URL where members and admins will access the platform. This becomes <strong>APP_URL</strong> on your server.',
            'aria' => 'Installation URL section help',
        ])
    </h3>
    <p class="install-url-lead">Please choose the URL to install the software.</p>

    <div class="install-url-row">
        <div class="install-url-field install-url-field--protocol">
            <select id="install_protocol" name="install_protocol" aria-label="Choose protocol">
                <option value="https" @selected($protocolValue === 'https')>https://</option>
                <option value="http" @selected($protocolValue === 'http')>http://</option>
            </select>
            <span class="field-label">
                Choose Protocol
                @include('setup.partials.field-tip', [
                    'tip' => 'If your site has SSL, then please choose the HTTPS protocol.',
                    'aria' => 'Protocol help',
                ])
            </span>
        </div>

        <div class="install-url-field install-url-field--domain">
            <input
                id="install_domain"
                name="install_domain"
                type="text"
                list="install_domain_list"
                value="{{ $domainValue }}"
                required
                autocomplete="off"
                aria-label="Choose domain"
            >
            <datalist id="install_domain_list">
                @foreach ($domainOptions as $host)
                    <option value="{{ $host }}"></option>
                @endforeach
            </datalist>
            <span class="field-label">
                Choose Domain
                @include('setup.partials.field-tip', [
                    'tip' => 'Please choose the domain to install the platform.',
                    'aria' => 'Domain help',
                ])
            </span>
        </div>

        <div class="install-url-field install-url-field--directory">
            <input
                id="install_directory"
                name="install_directory"
                type="text"
                value="{{ $directoryValue }}"
                placeholder=""
                autocomplete="off"
                aria-label="In directory"
            >
            <span class="field-label">
                In Directory
                @include('setup.partials.field-tip', [
                    'tip' => 'The directory is relative to your domain and <strong>should not exist</strong>. e.g. To install at http://mydomain/dir/ just type <strong>dir</strong>. To install only in http://mydomain/ leave this empty.',
                    'aria' => 'Directory help',
                ])
            </span>
        </div>
    </div>

    <p class="install-url-preview">
        Your installation URL :
        <strong id="install-url-preview-text">@php
            try {
                echo e(\App\Services\Setup\InstallationUrlBuilder::build(
                    $protocolValue,
                    $domainValue !== '' ? $domainValue : ($domainOptions[0] ?? 'localhost'),
                    $directoryValue
                ));
            } catch (\Throwable) {
                echo 'https://www.zanupf.org.zw';
            }
        @endphp</strong>
    </p>

    <div id="install-subdirectory-hint" class="hint" style="margin-top:10px;{{ trim($directoryValue) === '' ? ' display:none;' : '' }}">
        <strong>Subdirectory install.</strong> Point your web server at this path and ensure rewrite rules reach Laravel's <code>public/</code> folder. Set <code>SESSION_DOMAIN</code> to your domain if cookies fail across subpaths.
    </div>

    <input type="hidden" name="public_site_url" id="public_site_url" value="{{ old('public_site_url', $defaults['public_site_url'] ?? '') }}">
</div>

@once
    @push('head')
        <style>
            .install-url-block{
                margin-bottom: 20px;
                padding-bottom: 18px;
                border-bottom: 1px solid var(--line);
            }
            .install-url-title{
                margin:0 0 4px;
                font-size:15px;
                font-weight:700;
                color:var(--ink);
                display:flex;
                align-items:center;
                gap:8px;
                flex-wrap:wrap;
            }
            .install-url-lead{
                margin:0 0 14px;
                font-size:13px;
                color:var(--muted);
            }
            .install-url-row{
                display:grid;
                grid-template-columns: 120px minmax(0, 1fr) minmax(0, 1fr);
                gap: 14px;
                align-items:start;
            }
            @media (max-width:720px){
                .install-url-row{ grid-template-columns:1fr; }
            }
            .install-url-field select,
            .install-url-field input{
                width:100%;
                margin-bottom:6px;
            }
            .install-url-preview{
                margin:14px 0 0;
                font-size:13px;
                color:var(--ink);
            }
            .install-url-preview strong{
                color:#2563eb;
                word-break:break-all;
            }
        </style>
    @endpush
@endonce
