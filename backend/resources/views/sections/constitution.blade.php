@extends('layouts.dashboard')

@section('title', $docMeta['title'] ?? 'Constitution')
@section('page_heading', $docMeta['title'] ?? 'Constitution')

@section('content')
    <div class="const-layout">
        <aside class="const-nav">
            <div class="const-doc-switcher" style="margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border-subtle);">
                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.35rem;">Document</div>
                <a href="{{ route('constitution.home', ['doc' => 'zanupf']) }}" class="const-doc-link {{ ($doc ?? 'zanupf') === 'zanupf' ? 'is-active' : '' }}" style="display:block;padding:0.35rem 0;font-size:0.85rem;color:var(--text-muted);text-decoration:none;">ZANU PF</a>
                <a href="{{ route('constitution.home', ['doc' => 'zimbabwe']) }}" class="const-doc-link {{ ($doc ?? 'zanupf') === 'zimbabwe' ? 'is-active' : '' }}" style="display:block;padding:0.35rem 0;font-size:0.85rem;color:var(--text-muted);text-decoration:none;">Zimbabwe</a>
                <a href="{{ route('constitution.home', ['doc' => 'amendment3']) }}" class="const-doc-link {{ ($doc ?? 'zanupf') === 'amendment3' ? 'is-active' : '' }}" style="display:block;padding:0.35rem 0;font-size:0.85rem;color:var(--text-muted);text-decoration:none;">Amendment No. 3</a>
            </div>
            @foreach ($chapters as $chapter)
                <div class="const-nav-chapter">
                    <div class="const-nav-chapter-title">
                        @if(($doc ?? '') === 'amendment3')
                            Memorandum &amp; Clauses
                        @else
                            Chapter {{ $chapter->number }} – {{ $chapter->title }}
                        @endif
                    </div>
                    <ul class="const-nav-sections">
                        @foreach ($chapter->sections as $sec)
                            <li>
                                <a
                                    href="{{ route('constitution.home', ['doc' => $doc ?? 'zanupf', 'section' => $sec]) }}"
                                    class="const-nav-link {{ optional($activeSection)->id === $sec->id ? 'is-active' : '' }}"
                                >
                                    <span class="number">{{ $sec->logical_number }}</span>
                                    <span>{{ $sec->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </aside>

        <section class="const-reader">
            @if ($activeSection && $activeVersion)
                <h2 class="const-reader-title">
                    {{ ($docMeta['section_label'] ?? 'Article') }} {{ $activeSection->logical_number }} – {{ $activeSection->title }}
                </h2>
                <div class="const-reader-meta">
                    @if(($doc ?? '') === 'amendment3')
                        <p style="margin:0 0 0.75rem 0;">Clause text below is published through the administration console (content editors and Presidium workflow). It is the in-app reading copy, not a substitute for legal advice.</p>
                        @if(!empty($amendmentOfficialPdfUrl))
                            <p style="margin:0;">
                                <a href="{{ $amendmentOfficialPdfUrl }}" target="_blank" rel="noopener noreferrer" style="color:var(--zanupf-gold);font-weight:600;text-decoration:none;">Download official PDF</a>
                                <span style="color:var(--text-muted);font-size:0.85rem;"> — same file served to the mobile app</span>
                            </p>
                        @endif
                    @else
                        From Chapter {{ $activeSection->chapter->number }} – {{ $activeSection->chapter->title }}.
                    @endif
                </div>

                <div class="const-toolbar" style="flex-wrap:wrap;align-items:center;gap:0.5rem;">
                    @php
                        $canEdit = app(\App\Services\AdminAccessService::class)->canAccessSection(auth()->user(), 'constitution');
                    @endphp
                    @if ($canEdit)
                        <a href="{{ route('admin.constitution.sections.edit', $activeSection) }}" class="const-tool-btn" style="text-decoration:none;">Edit</a>
                        <a href="{{ route('admin.constitution.sections.versions', $activeSection) }}" class="const-tool-btn" style="text-decoration:none;">Amendments</a>
                    @endif
                    <button type="button" class="const-tool-btn" id="const-search-in-article">Search in article</button>
                    <span style="font-size:0.78rem;color:var(--text-muted);line-height:1.4;max-width:28rem;">
                        Highlights, notes, translation, and read-aloud are implemented in the <strong>mobile app</strong>. On the web, use your browser’s find (Ctrl+F / Cmd+F) or the button above.
                    </span>
                </div>

                @if(($doc ?? '') === 'amendment3' && $activeSection->amendmentClauseRelations?->isNotEmpty())
                    <div class="const-amends-block" style="margin-bottom:1.5rem;padding:1rem;background:rgba(15,23,42,0.6);border-radius:0.5rem;border:1px solid var(--border-subtle);">
                        <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.5rem;">Relates to Constitution of Zimbabwe</div>
                        <ul style="margin:0;padding-left:1.25rem;color:var(--zanupf-gold);">
                            @foreach($activeSection->amendmentClauseRelations as $rel)
                                <li>
                                    @if($rel->zimbabweSection)
                                        <a href="{{ route('constitution.home', ['doc' => 'zimbabwe', 'section' => $rel->zimbabweSection]) }}" style="color:var(--zanupf-gold);text-decoration:none;">{{ $rel->ref_label ?? ('Section ' . $rel->zimbabweSection->logical_number) }}</a>
                                    @else
                                        {{ $rel->ref_label }}
                                    @endif
                                    <span style="font-size:0.75rem;color:var(--text-muted);">({{ $rel->relation_type }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="const-reader-body" class="const-body" style="white-space:pre-wrap;">{{ $activeVersion->body }}</div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var btn = document.getElementById('const-search-in-article');
                        var bodyEl = document.getElementById('const-reader-body');
                        if (!btn || !bodyEl) return;
                        btn.addEventListener('click', function () {
                            var q = window.prompt('Find in this article:');
                            if (!q || !String(q).trim()) return;
                            q = String(q).trim();
                            if (typeof window.find === 'function') {
                                var sel = window.getSelection();
                                if (sel) sel.removeAllRanges();
                                var range = document.createRange();
                                range.selectNodeContents(bodyEl);
                                sel.addRange(range);
                                var found = window.find(q, false, false, true, false, false, false);
                                if (!found) window.alert('No more matches in this article for: ' + q);
                                if (sel) sel.removeAllRanges();
                            } else {
                                window.alert('Use your browser’s find: Ctrl+F (Windows/Linux) or Cmd+F (Mac).');
                            }
                        });
                    });
                </script>
            @else
                <p class="dash-panel-subtitle">
                    No content found yet. Please complete the seeding or contact an administrator.
                </p>
            @endif
        </section>
    </div>
@endsection

