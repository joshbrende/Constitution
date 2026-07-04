@extends('setup.layout')

@section('title', 'System checks')
@section('heading', 'System checks')
@section('subheading')
    Verify the server environment and database connection. Clicking <strong>Let's do this!</strong> will
    create the database (if needed), install all tables, and prepare the platform for configuration.
    Ensure <code>DB_*</code> values are set in <code>.env</code> before continuing.
@endsection
@section('body_class', 'two-col')

@section('content')
    <div class="section">
        <div class="section-heading" style="margin-bottom:8px;">
            <h3>Environment readiness</h3>
            @include('setup.partials.field-tip', [
                'tip' => 'Critical checks must pass before continuing. Database and schema issues are resolved automatically when you click <strong>Let\'s do this!</strong>',
                'aria' => 'Environment readiness help',
            ])
        </div>
        <p>Critical checks must pass. Database and schema rows will be resolved automatically when you continue.</p>

        <ul class="check-list">
            @foreach ($checks as $check)
                <li>
                    <span class="status {{ $check['status'] }}">{{ $check['status'] }}</span>
                    <div class="check-body">
                        <strong>{{ $check['label'] }}</strong>
                        <span>{{ $check['message'] }}</span>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($needsDatabaseProvision ?? false)
            <div class="hint">
                <strong>Database install pending.</strong> Continuing will run
                <code>CREATE DATABASE</code> (MySQL/MariaDB, if permitted) and
                <code>php artisan migrate --force</code> to create all application tables.
            </div>
        @endif
    </div>

    <div class="section">
        <div class="section-heading" style="margin-bottom:8px;">
            <h3>Server config checklist</h3>
            @include('setup.partials.field-tip', [
                'tip' => 'Set <strong>DB_*</strong>, mail, and <strong>APP_*</strong> values via your hosting panel or <code>.env</code> file before going live. The wizard reads these but does not change them.',
                'aria' => 'Server config help',
            ])
        </div>
        <p>Set these via hosting environment variables or <code>.env</code> before production.</p>
        @include('setup.partials.server-config', ['serverConfig' => $serverConfig])
    </div>

    <form id="setup-continue-form" method="POST" action="{{ route('setup.continue') }}" style="display:none;">
        @csrf
    </form>
@endsection

@section('footer_left')
    <a href="{{ route('setup.index') }}" class="footer-link">Back</a>
@endsection

@section('footer_right')
    @include('setup.partials.btn-next', [
        'label' => "Let's do this!",
        'form' => 'setup-continue-form',
        'disabled' => ! $canContinue,
    ])
@endsection
