@extends('layouts.dashboard')

@section('title', 'Presidium')
@section('page_heading', 'Presidium')

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
                    <div class="dash-panel-title">Presidium</div>
                    <div class="dash-panel-subtitle">
                        President, Vice Presidents, National Chairperson, and Secretary-General as shown in the app, linked to constitutional articles.
                    </div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.presidium.create') }}" class="dash-btn-ghost" style="text-decoration:none;">+ Add member</a>
                    <a href="{{ route('admin.presidium-publications.index') }}" class="dash-btn-ghost" style="text-decoration:none;">Publications</a>
                    <a href="{{ route('admin.home') }}" class="dash-btn-ghost" style="text-decoration:none;">← Admin</a>
                </div>
            </div>

            @if ($members->isEmpty())
                <p style="color:var(--text-muted);margin-top:1rem;">
                    No Presidium members configured yet. Use “Add member” to create the President, Vice Presidents, National Chairperson, and Secretary-General.
                </p>
            @else
                <table class="dash-table" style="margin-top:1rem;">
                    <thead>
                    <tr>
                        <th>Order</th>
                        <th style="width:52px;">Icon</th>
                        <th>Name &amp; title</th>
                        <th>Role key</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($members as $m)
                        @php
                            $iconSvg = match($m->role_slug) {
                                'president' => null,
                                'vice_president_1', 'vice_president_2' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4z" stroke="currentColor" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                                'national_chairperson' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2"/><path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/></svg>',
                                'secretary_general' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/><rect x="4" y="6" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2"/><path d="M9 12h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9 16h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
                                default => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>',
                            };
                        @endphp
                        <tr>
                            <td>{{ $m->order }}</td>
                            <td>
                                @if ($m->role_slug === 'president')
                                    <img src="/icon-1.png" alt="President icon" style="width:34px;height:34px;object-fit:contain;" />
                                @else
                                    <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:999px;border:1px solid rgba(250,204,21,0.35);color:var(--zanupf-gold);">
                                        {!! $iconSvg !!}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $m->name }}</strong>
                                <div style="font-size:0.8rem;color:var(--text-muted);">
                                    {{ $m->title }}
                                </div>
                            </td>
                            <td><code style="font-size:0.8rem;">{{ $m->role_slug }}</code></td>
                            <td>
                                @if ($m->is_published)
                                    <span class="dash-tag" style="font-size:0.75rem;background:var(--zanupf-green);color:#fff;">Published</span>
                                @else
                                    <span class="dash-tag" style="font-size:0.75rem;">Draft</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="{{ route('admin.presidium.edit', $m) }}" style="font-size:0.8rem;color:var(--zanupf-gold);margin-right:0.75rem;">Edit</a>
                                <form method="POST" action="{{ route('admin.presidium.destroy', $m) }}" style="display:inline;" onsubmit="return confirm('Remove this Presidium member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="font-size:0.8rem;color:var(--zanupf-red);background:none;border:none;cursor:pointer;padding:0;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection

