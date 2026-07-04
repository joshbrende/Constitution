@extends('setup.layout')

@section('title', 'Welcome')
@section('splash')
    <div class="splash">
        <div class="splash-logo">
            <img src="{{ asset('Logo.png') }}" alt="ZANU PF">
        </div>
        <h2>Welcome to the Platform Setup Wizard</h2>
        <p>
            Install the ZANU PF constitution and academy platform on your government or enterprise server.
            This guided setup creates your database, administrator account, and platform content.
        </p>
        <span class="splash-badge">Secure first-run setup</span>
    </div>
@endsection

@section('footer_left')
    <span class="footer-link footer-link-muted">Step 1 of 6</span>
@endsection

@section('footer_right')
    <a href="{{ route('setup.checks') }}" class="btn-next">
        Next
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
@endsection
