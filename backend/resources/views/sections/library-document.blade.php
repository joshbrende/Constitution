@extends('layouts.dashboard')

@section('title', $document->title)
@section('page_heading', 'Digital Library')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div style="margin-bottom:1rem;">
                <a href="{{ route('library.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Back to Library</a>
            </div>
            @if ($document->category)
                <div class="dash-nav-group-label">{{ $document->category->name }}</div>
            @endif
            <h1 style="font-size:1.5rem;font-weight:700;color:var(--text-main);margin:0.5rem 0 1rem 0;">{{ $document->title }}</h1>
            <div style="display:flex;gap:1rem;margin-bottom:1rem;font-size:0.85rem;color:var(--text-muted);">
                <span>{{ $document->document_type }}</span>
                @if ($document->published_at)
                    <span>{{ $document->published_at->format('d F Y') }}</span>
                @endif
            </div>
            @if ($document->abstract)
                <p style="font-style:italic;color:var(--text-muted);margin-bottom:1.25rem;line-height:1.5;">{{ $document->abstract }}</p>
            @endif
            <div class="library-document-body" style="line-height:1.6;color:var(--text-main);">
                @if ($document->body)
                    {!! \App\Support\HtmlSanitizer::sanitize($document->body) !!}
                @else
                    <p style="color:var(--text-muted);">{{ $document->abstract ?? 'No content.' }}</p>
                @endif
            </div>
        </section>
    </div>
@endsection
