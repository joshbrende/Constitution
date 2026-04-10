@extends('layouts.dashboard')

@section('title', 'Manage The Party')
@section('page_heading', 'Manage The Party')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;margin-bottom:1.5rem;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">The Party</div>
                    <div class="dash-panel-subtitle">
                        Manage key Party information by editing the underlying constitutional articles, Party profile, and Party organs.
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="dash-btn-ghost" style="text-decoration:none;">← Overview</a>
            </div>

            <form method="POST" action="{{ route('admin.party.update') }}">
                @csrf
                @method('PUT')

                <div style="display:grid;gap:1rem;max-width:52rem;margin-top:1rem;">
                    <div>
                        <label for="history" class="form-label">Party history &amp; overview</label>
                        <textarea id="history" name="history" rows="6" class="form-input" placeholder="High-level narrative about the Party's history, identity, and mission.">{{ old('history', $profile?->history) }}</textarea>
                        <p class="form-help">Admin-editable summary (e.g. adapted from the official site). Shown on The Party page.</p>
                    </div>

                    <div>
                        <label for="vision" class="form-label">Vision</label>
                        <textarea id="vision" name="vision" rows="3" class="form-input" placeholder="Expandable vision statement. Shown on The Party page when set.">{{ old('vision', $profile?->vision) }}</textarea>
                        <p class="form-help">Optional. When set, displayed as a dedicated block on The Party page and in the app.</p>
                    </div>
                    <div>
                        <label for="mission" class="form-label">Mission</label>
                        <textarea id="mission" name="mission" rows="3" class="form-input" placeholder="Expandable mission statement. Shown on The Party page when set.">{{ old('mission', $profile?->mission) }}</textarea>
                        <p class="form-help">Optional. When set, displayed as a dedicated block on The Party page and in the app.</p>
                    </div>

                    <p class="form-help" style="margin-top:0.5rem;">Leagues (Veterans, Women's, Youth) can also be managed—and more leagues added—via <a href="{{ route('admin.party-leagues.index') }}" style="color:var(--zanupf-gold);">Manage Party Leagues</a>.</p>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
                        <div>
                            <label for="veterans_league_leader_name" class="form-label">Veterans League leader</label>
                            <input id="veterans_league_leader_name" type="text" name="veterans_league_leader_name" class="form-input" value="{{ old('veterans_league_leader_name', $profile?->veterans_league_leader_name) }}" placeholder="e.g. Cde Douglas Mahiya">
                            <input type="text" name="veterans_league_leader_title" class="form-input" style="margin-top:0.4rem;" value="{{ old('veterans_league_leader_title', $profile?->veterans_league_leader_title) }}" placeholder="e.g. Secretary Veterans League">
                            <p class="form-help">Name and title as currently serving.</p>
                        </div>
                        <div>
                            <label for="womens_league_leader_name" class="form-label">Women's League leader</label>
                            <input id="womens_league_leader_name" type="text" name="womens_league_leader_name" class="form-input" value="{{ old('womens_league_leader_name', $profile?->womens_league_leader_name) }}" placeholder="e.g. Mabel Chinomona">
                            <input type="text" name="womens_league_leader_title" class="form-input" style="margin-top:0.4rem;" value="{{ old('womens_league_leader_title', $profile?->womens_league_leader_title) }}" placeholder="e.g. Secretary Women's League">
                            <p class="form-help">Name and title as currently serving.</p>
                        </div>
                        <div>
                            <label for="youth_league_leader_name" class="form-label">Youth League leader</label>
                            <input id="youth_league_leader_name" type="text" name="youth_league_leader_name" class="form-input" value="{{ old('youth_league_leader_name', $profile?->youth_league_leader_name) }}" placeholder="e.g. Tinoda Machakaire">
                            <input type="text" name="youth_league_leader_title" class="form-input" style="margin-top:0.4rem;" value="{{ old('youth_league_leader_title', $profile?->youth_league_leader_title) }}" placeholder="e.g. Secretary Youth League">
                            <p class="form-help">Name and title as currently serving.</p>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
                        <div>
                            <label for="veterans_league_body" class="form-label">Veterans League description</label>
                            <textarea id="veterans_league_body" name="veterans_league_body" rows="4" class="form-input" placeholder="Mandate and role of the Veterans League.">{{ old('veterans_league_body', $profile?->veterans_league_body) }}</textarea>
                        </div>
                        <div>
                            <label for="womens_league_body" class="form-label">Women's League description</label>
                            <textarea id="womens_league_body" name="womens_league_body" rows="4" class="form-input" placeholder="Mandate and role of the Women's League.">{{ old('womens_league_body', $profile?->womens_league_body) }}</textarea>
                        </div>
                        <div>
                            <label for="youth_league_body" class="form-label">Youth League description</label>
                            <textarea id="youth_league_body" name="youth_league_body" rows="4" class="form-input" placeholder="Mandate and role of the Youth League.">{{ old('youth_league_body', $profile?->youth_league_body) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="form-btn-primary">Save Party profile</button>
                    </div>
                </div>
            </form>

                <div style="margin-top:1.5rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-main);margin:0 0 0.75rem 0;">Related constitution articles</h3>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.75rem 0;">Link any constitution section to The Party page so users can open it from there.</p>
                    @if ($relatedSections->isNotEmpty())
                        <ul style="list-style:none;padding:0;margin:0 0 1rem 0;">
                            @foreach ($relatedSections as $rel)
                                <li style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0;border-bottom:1px solid var(--border-subtle);">
                                    <span style="flex:1;">{{ $rel->label ?: $rel->section?->title }}</span>
                                    <a href="{{ route('constitution.home', ['doc' => 'zanupf', 'section' => $rel->section]) }}" style="font-size:0.85rem;color:var(--zanupf-gold);">View</a>
                                    <a href="{{ route('admin.constitution.sections.edit', $rel->section) }}" style="font-size:0.85rem;color:var(--zanupf-gold);">Edit</a>
                                    <form method="POST" action="{{ route('admin.party.related-sections.detach', $rel->id) }}" style="display:inline;" onsubmit="return confirm('Unlink this article from The Party?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="font-size:0.85rem;color:var(--zanupf-red);background:none;border:none;cursor:pointer;padding:0;">Unlink</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.75rem 0;">No related articles linked yet.</p>
                    @endif
                    <form method="POST" action="{{ route('admin.party.related-sections.attach') }}" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;">
                        @csrf
                        <div style="min-width:200px;">
                            <label for="section_id" class="form-label">Add article</label>
                            <select id="section_id" name="section_id" class="form-input" required>
                                <option value="">— Select section —</option>
                                @foreach ($sectionsForSelect as $sec)
                                    <option value="{{ $sec->id }}">{{ $sec->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width:160px;">
                            <label for="label" class="form-label">Label (optional)</label>
                            <input id="label" type="text" name="label" class="form-input" placeholder="e.g. Article 1">
                        </div>
                        <button type="submit" class="form-btn-primary" style="padding:0.5rem 1rem;">Link</button>
                    </form>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;margin-top:1.5rem;">
                <div style="background:rgba(15,23,42,0.7);border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-main);margin:0 0 0.5rem 0;">
                        Party Leagues
                    </h3>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.75rem 0;">
                        Add or edit leagues (Veterans, Women's, Youth and any others). Shown on The Party page and in the app.
                    </p>
                    <a href="{{ route('admin.party-leagues.index') }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Manage Party Leagues</a>
                </div>

                <div style="background:rgba(15,23,42,0.7);border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-main);margin:0 0 0.5rem 0;">
                        Article 1 – The Party
                    </h3>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.75rem 0;">
                        Name, legal status, seal, flag, headquarters, vision and mission of ZANU PF.
                    </p>
                    @if ($articleTheParty)
                        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                            <a href="{{ route('constitution.home', ['doc' => 'zanupf', 'section' => $articleTheParty]) }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">View in reader</a>
                            <a href="{{ route('admin.constitution.sections.edit', $articleTheParty) }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Edit article</a>
                            <a href="{{ route('admin.constitution.sections.versions', $articleTheParty) }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Amendments</a>
                        </div>
                    @else
                        <p style="font-size:0.85rem;color:var(--zanupf-red);margin:0;">Article 1 section not found. Check the Constitution seeder.</p>
                    @endif
                </div>

                <div style="background:rgba(15,23,42,0.7);border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-main);margin:0 0 0.5rem 0;">
                        Principal Organs & Structure
                    </h3>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.75rem 0;">
                        Constitutional description of principal organs and structures of the Party.
                    </p>
                    @if ($articleOrgans)
                        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                            <a href="{{ route('constitution.home', ['doc' => 'zanupf', 'section' => $articleOrgans]) }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">View in reader</a>
                            <a href="{{ route('admin.constitution.sections.edit', $articleOrgans) }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Edit article</a>
                            <a href="{{ route('admin.constitution.sections.versions', $articleOrgans) }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Amendments</a>
                        </div>
                    @else
                        <p style="font-size:0.85rem;color:var(--zanupf-red);margin:0;">Article 4 section not found. Check the Constitution seeder.</p>
                    @endif
                </div>

                <div style="background:rgba(15,23,42,0.7);border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1rem;">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--text-main);margin:0 0 0.5rem 0;">
                        Party Organs (data)
                    </h3>
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.75rem 0;">
                        Edit the Party Organs list used by the app (Congress, Central Committee, Politburo, Leagues, etc.).
                    </p>
                    <a href="{{ route('admin.party-organs.index') }}" class="dash-btn-ghost" style="text-decoration:none;font-size:0.85rem;">Manage Party Organs</a>
                </div>
            </div>
        </section>
    </div>
@endsection

