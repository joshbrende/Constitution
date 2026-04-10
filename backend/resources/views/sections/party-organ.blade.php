@extends('layouts.dashboard')

@section('title', $organ->name)
@section('page_heading', 'Party Organs')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div style="margin-bottom:1rem;">
                <a href="{{ route('party-organs.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Back to Party Organs</a>
            </div>
            <h1 style="font-size:1.5rem;font-weight:700;color:var(--text-main);margin:0.5rem 0 1rem 0;">{{ $organ->name }}</h1>
            @if ($organ->short_description)
                <p style="font-style:italic;color:var(--text-muted);margin-bottom:1.25rem;line-height:1.5;">{{ $organ->short_description }}</p>
            @endif
            <div class="library-document-body" style="line-height:1.6;color:var(--text-main);">
                @if ($organ->body)
                    {!! \App\Support\HtmlSanitizer::sanitize($organ->body) !!}
                @else
                    <p style="color:var(--text-muted);">{{ $organ->short_description ?? 'No content.' }}</p>
                @endif
            </div>
        </section>
    </div>
@endsection
