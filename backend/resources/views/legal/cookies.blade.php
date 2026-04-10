@extends('layouts.public-legal')

@section('title', 'Cookie Policy')
@section('content')
    <h1>Cookie policy</h1>
    <div style="color:var(--text-muted);font-size:0.95rem;line-height:1.65;">
            <p>
                This admin console uses cookies and browser storage primarily to support authentication, security, and a consistent user experience.
            </p>

            <h3 style="color:var(--text-main);margin-top:1rem;">What we store</h3>
            <ul>
                <li>Session cookies required for login and CSRF protection.</li>
                <li>Preference storage such as theme mode and dashboard layout toggles.</li>
            </ul>

            <h3 style="color:var(--text-main);margin-top:1rem;">Managing cookies</h3>
            <p>
                You can clear cookies and site data from your browser settings. If you clear session cookies you will be signed out.
            </p>
@endsection

