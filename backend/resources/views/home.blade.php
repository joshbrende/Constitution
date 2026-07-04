@extends('layouts.public')

@section('title', $orgName.' — Constitution & Academy Platform')

@section('meta_description', 'Official digital platform for studying the Constitution of Zimbabwe, ZANU PF party constitution, academy courses, assessments, and membership certificates.')

@push('styles')
<style>
    .hero {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        align-items: center;
        margin-bottom: 3rem;
    }
    @media (min-width: 900px) {
        .hero { grid-template-columns: 1.05fr 0.95fr; gap: 2.5rem; }
    }
    .hero-copy { max-width: 38rem; }
    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.25rem 0.7rem;
        border-radius: 999px;
        border: 1px solid rgba(250, 204, 21, 0.35);
        background: rgba(250, 204, 21, 0.08);
        color: var(--gold);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }
    .eyebrow-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #22c55e;
    }
    .hero h1 {
        margin: 0 0 1rem;
        font-size: clamp(2rem, 4.5vw, 3.15rem);
        line-height: 1.08;
        letter-spacing: -0.02em;
        font-weight: 800;
    }
    .hero-lead {
        margin: 0 0 1.5rem;
        font-size: 1.05rem;
        line-height: 1.65;
        color: #cbd5e1;
        max-width: 34rem;
    }
    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .hero-note {
        font-size: 0.82rem;
        color: #94a3b8;
        line-height: 1.5;
    }
    .hero-visual {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid var(--panel-border);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
        min-height: 280px;
        background: #0f172a;
    }
    .hero-visual img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        min-height: 280px;
    }
    .hero-visual-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 35%, rgba(2, 6, 23, 0.92) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1.25rem;
    }
    .hero-visual-caption {
        font-size: 0.85rem;
        color: #e2e8f0;
        line-height: 1.45;
    }
    .setup-banner {
        margin-bottom: 2rem;
        padding: 1rem 1.15rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(250, 204, 21, 0.35);
        background: rgba(250, 204, 21, 0.08);
        color: #fde68a;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    .setup-banner a {
        color: #fff;
        font-weight: 600;
    }
    .section-head {
        margin-bottom: 1.25rem;
    }
    .section-head h2 {
        margin: 0 0 0.35rem;
        font-size: 1.35rem;
        font-weight: 800;
    }
    .section-head p {
        margin: 0;
        color: #94a3b8;
        font-size: 0.95rem;
        max-width: 40rem;
    }
    .feature-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }
    @media (min-width: 640px) {
        .feature-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 960px) {
        .feature-grid { grid-template-columns: repeat(4, 1fr); }
    }
    .feature-card {
        background: var(--panel);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.85rem;
        padding: 1.15rem;
        min-height: 100%;
        transition: border-color 0.15s ease, transform 0.15s ease;
    }
    .feature-card:hover {
        border-color: rgba(250, 204, 21, 0.35);
        transform: translateY(-2px);
    }
    .feature-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.55rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 0.75rem;
        background: rgba(21, 128, 61, 0.2);
        border: 1px solid rgba(34, 197, 94, 0.35);
    }
    .feature-card h3 {
        margin: 0 0 0.4rem;
        font-size: 1rem;
        font-weight: 700;
    }
    .feature-card p {
        margin: 0;
        font-size: 0.85rem;
        line-height: 1.55;
        color: #94a3b8;
    }
    .split-panels {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (min-width: 800px) {
        .split-panels { grid-template-columns: 1fr 1fr; }
    }
    .panel {
        background: var(--panel);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.85rem;
        padding: 1.35rem;
    }
    .panel h3 {
        margin: 0 0 0.5rem;
        font-size: 1.05rem;
        font-weight: 700;
    }
    .panel p {
        margin: 0 0 1rem;
        font-size: 0.9rem;
        line-height: 1.6;
        color: #94a3b8;
    }
    .panel ul {
        margin: 0;
        padding-left: 1.1rem;
        color: #cbd5e1;
        font-size: 0.88rem;
        line-height: 1.7;
    }
    .stripe {
        height: 3px;
        border-radius: 999px;
        margin: 2.5rem 0;
        background: linear-gradient(90deg, var(--green-bright), var(--gold), var(--red));
        opacity: 0.85;
    }
    .mobile-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: center;
        margin-bottom: 2.5rem;
        padding: 1.5rem;
        border-radius: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: linear-gradient(135deg, rgba(21, 128, 61, 0.12), rgba(15, 23, 42, 0.92) 55%);
    }
    @media (min-width: 768px) {
        .mobile-section { grid-template-columns: 1.2fr auto; }
    }
    .mobile-section h2 {
        margin: 0 0 0.5rem;
        font-size: 1.25rem;
        font-weight: 800;
    }
    .mobile-section p {
        margin: 0;
        color: #94a3b8;
        font-size: 0.92rem;
        line-height: 1.6;
        max-width: 34rem;
    }
    .store-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }
    .store-badge-link {
        display: inline-block;
        line-height: 0;
        border-radius: 6px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .store-badge-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }
    .store-badge-link img {
        display: block;
        height: 40px;
        width: auto;
    }
    .mobile-coming-soon {
        font-size: 0.82rem;
        color: #94a3b8;
        font-style: italic;
    }
</style>
@endpush

@section('content')
    @auth
        @if (! $setupComplete)
            <div class="setup-banner" role="status">
                Platform setup is still in progress.
                <a href="{{ $navCta['url'] ?? route('setup.index') }}">Continue the setup wizard</a>
                to finish configuring {{ $orgName }} before using the dashboard.
            </div>
        @endif
    @endauth

    <section class="hero" aria-labelledby="home-hero-title">
        <div class="hero-copy">
            <div class="eyebrow">
                <span class="eyebrow-dot" aria-hidden="true"></span>
                Official digital platform
            </div>
            <h1 id="home-hero-title">Study the Constitution. Build knowledge. Serve with clarity.</h1>
            <p class="hero-lead">
                {{ $orgName }} brings together constitutional texts, structured academy learning,
                assessments, certificates, and member services in one secure environment for
                administrators, instructors, and members.
            </p>
            <div class="hero-actions">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-gold">Create a member account</a>
                    @endif
                @else
                    @if ($setupComplete)
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Open dashboard</a>
                    @else
                        <a href="{{ $navCta['url'] ?? route('setup.index') }}" class="btn btn-accent">Continue setup</a>
                    @endif
                    <a href="{{ route('certificate.verify') }}" class="btn btn-outline-gold">Verify a certificate</a>
                @endguest
            </div>
            <p class="hero-note">
                @if ($mobileAppStoreUrl || $mobilePlayStoreUrl)
                    Download the mobile app for courses, constitution study, and member services on the go.
                @else
                    Mobile app available for members — study on the go, track academy progress, and access dialogue features after sign-in.
                @endif
            </p>
            @if ($mobileAppStoreUrl || $mobilePlayStoreUrl)
                <div style="margin-top: 1rem;">
                    @include('partials.mobile-store-badges', [
                        'appStoreUrl' => $mobileAppStoreUrl,
                        'playStoreUrl' => $mobilePlayStoreUrl,
                    ])
                </div>
            @endif
        </div>

        <div class="hero-visual" aria-hidden="true">
            <img src="{{ asset('constitution.jpg') }}" alt="">
            <div class="hero-visual-overlay">
                <p class="hero-visual-caption">
                    Read the Constitution of Zimbabwe, ZANU PF party constitution, and amendment materials with search and structured navigation.
                </p>
            </div>
        </div>
    </section>

    <div class="stripe" aria-hidden="true"></div>

    <section class="mobile-section" aria-labelledby="mobile-title">
        <div>
            <h2 id="mobile-title">Take the academy with you</h2>
            <p>
                The {{ $orgName }} mobile app lets members read constitutional texts, enrol in courses,
                complete assessments, and stay connected — with profile and province details added when required for features.
            </p>
        </div>
        <div>
            @include('partials.mobile-store-badges', [
                'appStoreUrl' => $mobileAppStoreUrl,
                'playStoreUrl' => $mobilePlayStoreUrl,
            ])
            @if (! $mobileAppStoreUrl && ! $mobilePlayStoreUrl)
                <p class="mobile-coming-soon">Coming soon to the App Store and Google Play.</p>
            @endif
        </div>
    </section>

    <section aria-labelledby="features-title">
        <div class="section-head">
            <h2 id="features-title">Everything in one platform</h2>
            <p>Purpose-built for constitutional literacy, academy delivery, and transparent membership administration.</p>
        </div>

        <div class="feature-grid">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">📜</div>
                <h3>Constitution reader</h3>
                <p>Browse ZANU PF and national constitutional texts with parts, chapters, and full-text search.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🎓</div>
                <h3>Academy &amp; assessments</h3>
                <p>Enrol in courses, complete assessments, and progress toward membership certification.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">💬</div>
                <h3>Dialogue &amp; library</h3>
                <p>Participate in guided dialogue and access official documents from the party library.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">✅</div>
                <h3>Certificate verification</h3>
                <p>Publicly verify issued certificates using certificate number and verification code.</p>
            </article>
        </div>
    </section>

    <section class="split-panels" aria-labelledby="audience-title">
        <div class="panel">
            <h3 id="audience-title">For members &amp; learners</h3>
            <p>Register, sign in, and access learning materials from the web or mobile app.</p>
            <ul>
                <li>Study constitutional texts and amendments</li>
                <li>Complete academy courses and assessments</li>
                <li>Track certificates and application status</li>
                <li>Update profile details when required for features</li>
            </ul>
        </div>
        <div class="panel">
            <h3>For administrators</h3>
            <p>Secure backend tools for content, users, analytics, and certificate workflows.</p>
            <ul>
                <li>Manage constitution content and academy courses</li>
                <li>Review applications, dialogue, and provincial scope</li>
                <li>Issue and audit certificates with verification</li>
                <li>Configure platform settings after installation</li>
            </ul>
        </div>
    </section>
@endsection
