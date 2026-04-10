@extends('layouts.dashboard')

@section('title', 'Opinion & Dialogue')
@section('page_heading', 'Opinion & Dialogue')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Dialogue streams</div>
                    <div class="dash-panel-subtitle">
                        Channels for Presidium and Leagues, linked to specific ZANU PF and Zimbabwe constitutional articles.
                    </div>
                </div>
            </div>

            <p class="dash-panel-subtitle" style="margin-bottom:1rem;">
                Use the mobile app’s Chat tab to participate in structured discussions. Dialogue channels (Presidium, Youth League,
                Women’s League, War Veterans League, etc.) are curated and moderated from the admin dashboard.
            </p>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
                <div style="background:rgba(15,23,42,0.9);border-radius:0.75rem;border:1px solid var(--border-subtle);padding:0.9rem;">
                    <h3 style="font-size:0.95rem;font-weight:600;color:var(--text-main);margin:0 0 0.4rem 0;">
                        Chat on mobile
                    </h3>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0 0 0.6rem 0;">
                        Members can join Dialogue from the Chat tab in the mobile app, with channels mapped to constitutional articles.
                    </p>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">
                        New messages and unread counts are surfaced per channel, similar to WhatsApp communities.
                    </p>
                </div>

                <div style="background:rgba(15,23,42,0.9);border-radius:0.75rem;border:1px solid var(--border-subtle);padding:0.9rem;">
                    <h3 style="font-size:0.95rem;font-weight:600;color:var(--text-main);margin:0 0 0.4rem 0;">
                        Admin moderation
                    </h3>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0 0 0.6rem 0;">
                        Authorised cadres can create, edit and moderate Dialogue channels and threads from the admin dashboard.
                        Threads can be locked, messages pinned or removed, and each channel can be linked to specific articles.
                    </p>
                    <a href="{{ route('admin.dialogue.index') }}"
                       class="dash-btn-ghost"
                       style="display:inline-block;margin-top:0.6rem;text-decoration:none;font-size:0.85rem;">
                        Open Dialogue admin
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
