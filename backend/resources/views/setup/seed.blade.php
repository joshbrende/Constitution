@extends('setup.layout')

@section('title', 'Install content')
@section('heading', 'Install platform content')
@section('subheading')
    This step is <strong>required</strong>. The wizard loads constitution text, roles, home banners,
    academy course, presidium profiles, library documents, and static pages — the same as
    <code>php artisan db:seed</code> for platform content.
@endsection

@section('content')
    <div class="section">
        <div class="section-heading" style="margin-bottom:8px;">
            <p style="margin:0;font-size:13px;font-weight:700;color:var(--ink);">Content to install</p>
            @include('setup.partials.field-tip', [
                'tip' => 'This step is required and cannot be skipped. It loads the same platform content as <code>php artisan db:seed</code>.',
                'aria' => 'Content install help',
            ])
        </div>
        <p>The following will be installed into your new database:</p>
        <ul style="margin:0 0 16px;padding-left:20px;font-size:13px;line-height:1.65;color:var(--muted);">
            <li>Roles, permissions, and constitution sections</li>
            <li>Home banners, party organs, presidium, and library</li>
            <li>Academy membership course, dialogue channels, static pages</li>
        </ul>
    </div>

    <form id="setup-seed-form" method="POST" action="{{ route('setup.seed.run') }}">
        @csrf

        <div class="row">
            <div class="tog">
                <div>
                    <div class="tog-title">
                        <strong>Include mobile test user (optional)</strong>
                        @include('setup.partials.field-tip', [
                            'tip' => 'Creates <strong>mobile.test@zanupf.org.zw</strong> with a known password for QA on dev/staging. Leave unchecked on production servers.',
                            'aria' => 'Mobile test user help',
                        ])
                    </div>
                    <small>Creates <code>mobile.test@zanupf.org.zw</code> for dev/staging only — leave off on production.</small>
                </div>
                <input type="hidden" name="seed_mobile_test_user" value="0">
                <input type="checkbox" name="seed_mobile_test_user" value="1">
            </div>
        </div>
    </form>
@endsection

@section('footer_left')
    <a href="{{ route('setup.platform') }}" class="footer-link">Back</a>
@endsection

@section('footer_right')
    @include('setup.partials.btn-next', [
        'label' => 'Install content',
        'form' => 'setup-seed-form',
    ])
@endsection
