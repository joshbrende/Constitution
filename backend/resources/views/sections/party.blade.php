@extends('layouts.dashboard')

@section('title', 'The Party')
@section('page_heading', 'The Party')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">The Party</div>
                    <div class="dash-panel-subtitle">
                        Name, legal status, flag, headquarters, vision and mission of ZANU PF, with overview and league mandates.
                    </div>
                </div>
            </div>

            @if ($profile?->history)
                <div class="library-document-body" style="line-height:1.6;color:var(--text-main);margin-top:1rem;">
                    {!! nl2br(e($profile->history)) !!}
                </div>
            @elseif ($body)
                <div class="library-document-body" style="line-height:1.6;color:var(--text-main);margin-top:1rem;">
                    {!! nl2br(e($body)) !!}
                </div>
            @else
                <p class="dash-panel-subtitle" style="margin-top:1rem;">Content for The Party is not available.</p>
            @endif

            @if ($profile?->vision)
                <h3 style="font-size:1.1rem;font-weight:600;color:var(--text-main);margin-top:1.5rem;margin-bottom:0.5rem;">Vision</h3>
                <div class="library-document-body" style="line-height:1.6;color:var(--text-main);">{!! nl2br(e($profile->vision)) !!}</div>
            @endif
            @if ($profile?->mission)
                <h3 style="font-size:1.1rem;font-weight:600;color:var(--text-main);margin-top:1.5rem;margin-bottom:0.5rem;">Mission</h3>
                <div class="library-document-body" style="line-height:1.6;color:var(--text-main);">{!! nl2br(e($profile->mission)) !!}</div>
            @endif

            @if ($relatedSections->isNotEmpty())
                <h3 style="font-size:1.1rem;font-weight:600;color:var(--text-main);margin-top:1.5rem;margin-bottom:0.5rem;">Related articles</h3>
                <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:0.5rem;">
                    @foreach ($relatedSections as $rel)
                        @if ($rel->section)
                            <a href="{{ route('constitution.home', ['doc' => 'zanupf', 'section' => $rel->section]) }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.9rem;">{{ $rel->label ?: $rel->section->title }}</a>
                        @endif
                    @endforeach
                </div>
            @endif

            @if ($leagues->isNotEmpty())
            <h3 style="font-size:1.1rem;font-weight:600;color:var(--text-main);margin-top:1.5rem;margin-bottom:0.5rem;">Leagues</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;margin-top:0.5rem;">
                @foreach ($leagues as $league)
                <div style="background:rgba(15,23,42,0.7);border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1rem;">
                    <h4 style="font-size:1rem;font-weight:600;color:var(--text-main);margin:0 0 0.35rem 0;">{{ $league->name }}</h4>
                    @if ($league->leader_name ?? null)
                        <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.5rem 0;">
                            <strong>{{ $league->leader_name }}</strong>
                            @if ($league->leader_title ?? null)
                                – {{ $league->leader_title }}
                            @endif
                        </p>
                    @endif
                    <p style="font-size:0.9rem;color:var(--text-main);line-height:1.5;">
                        {{ ($league->body ?? null) ?: 'Description will appear here.' }}
                    </p>
                </div>
                @endforeach
            </div>
            @endif
        </section>
    </div>
@endsection

