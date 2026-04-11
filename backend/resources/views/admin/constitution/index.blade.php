@extends('layouts.dashboard')

@section('title', 'Constitution Management')
@section('page_heading', 'Constitution Management')

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Manage Constitution Structure</div>
                    <div class="dash-panel-subtitle">
                        Edit Parts, Chapters, and Sections. Create amendments; Presidium approval required to publish.
                    </div>
                </div>
                @if ($pendingCount > 0)
                    <span class="dash-tag" style="background:rgba(250,204,21,0.2);color:#facc15;">
                        {{ $pendingCount }} awaiting Presidium approval
                    </span>
                @endif
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-top:1rem;">
                <a href="{{ route('admin.constitution.parts') }}" class="dash-tile">
                    <div class="dash-tile-title">Parts</div>
                    <div class="dash-tile-text">Manage Parts (top-level structure).</div>
                    <div class="dash-tile-footer">Manage parts</div>
                </a>
                @if($amendmentChapter ?? null)
                <a href="{{ route('admin.constitution.sections', $amendmentChapter) }}" class="dash-tile">
                    <div class="dash-tile-title">Amendment Bill No. 3</div>
                    <div class="dash-tile-text">Edit clause text for the mobile and web readers (draft → review → publish workflow).</div>
                    <div class="dash-tile-footer">Edit amendment clauses</div>
                </a>
                @endif
                <a href="{{ route('constitution.home') }}" class="dash-tile" target="_blank">
                    <div class="dash-tile-title">View Constitution</div>
                    <div class="dash-tile-text">Read the published constitution.</div>
                    <div class="dash-tile-footer">Open reader</div>
                </a>
            </div>

            <div style="margin-top:1.5rem;">
                <div class="dash-panel-title" style="margin-bottom:0.5rem;">Structure overview</div>
                <div class="dash-panel-subtitle" style="margin-bottom:0.75rem;">
                    Parts → Chapters → Sections. Click a part to manage its chapters and sections.
                </div>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    @foreach ($parts as $part)
                        <div style="border:1px solid var(--border-subtle);border-radius:0.5rem;padding:0.75rem;">
                            <a href="{{ route('admin.constitution.chapters', $part) }}" style="font-weight:600;color:var(--zanupf-gold);text-decoration:none;">
                                Part {{ $part->number }} – {{ $part->title }}
                            </a>
                            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">
                                {{ $part->chapters->count() }} chapters,
                                {{ $part->chapters->sum(fn($c) => $c->sections->count()) }} sections
                            </div>
                        </div>
                    @endforeach
                    @if ($parts->isEmpty())
                        <p class="dash-panel-subtitle">No parts yet. <a href="{{ route('admin.constitution.parts') }}">Create a part</a>.</p>
                    @endif
                </div>
            </div>

            <div style="margin-top:1.5rem;padding:1rem;background:rgba(15,23,42,0.6);border-radius:0.5rem;border:1px solid var(--border-subtle);">
                <div class="dash-panel-title" style="font-size:0.9rem;margin-bottom:0.5rem;">Official Amendment Bill PDF (mobile download)</div>
                <p class="dash-panel-subtitle" style="margin-bottom:0.75rem;">
                    Upload the gazetted or cabinet PDF. The app exposes it at <code style="font-size:0.75rem;">GET /api/v1/constitution/official/amendment3</code> and shows an “Official PDF” action on phones when this file is present.
                    Ensure <code style="font-size:0.75rem;">php artisan storage:link</code> has been run on the server.
                </p>
                @if(!empty($amendmentOfficialPdfAvailable))
                    <p style="font-size:0.85rem;color:#86efac;margin-bottom:0.75rem;">A file is currently on disk and available to the public API.</p>
                @else
                    <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:0.75rem;">No PDF on disk yet — mobile users will not see the official download until you upload one.</p>
                @endif
                <form action="{{ route('admin.constitution.amendment-official-pdf') }}" method="post" enctype="multipart/form-data" style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center;">
                    @csrf
                    <label for="amendment_official_pdf" style="font-size:0.85rem;color:var(--text-muted);max-width:100%;cursor:pointer;">
                        <span style="display:block;margin-bottom:0.25rem;">PDF file</span>
                        <input id="amendment_official_pdf" type="file" name="pdf" accept="application/pdf" required style="font-size:0.85rem;color:var(--text-muted);max-width:100%;">
                    </label>
                    <button type="submit" class="const-tool-btn" style="border:none;cursor:pointer;">Upload / replace PDF</button>
                </form>
            </div>

            <div style="margin-top:1.5rem;padding:0.75rem;background:rgba(15,23,42,0.6);border-radius:0.5rem;border:1px solid var(--border-subtle);">
                <div class="dash-panel-title" style="font-size:0.9rem;">Amendment workflow</div>
                <ol style="margin:0.5rem 0 0 1rem;padding:0;font-size:0.85rem;color:var(--text-muted);line-height:1.6;">
                    <li>Content Editor creates or edits a section version as <strong>draft</strong>.</li>
                    <li>Submit for approval → status becomes <strong>in_review</strong>.</li>
                    <li><strong>Presidium</strong> approves or rejects. Approved versions become <strong>published</strong> and go live.</li>
                </ol>
            </div>
        </section>
    </div>
@endsection
