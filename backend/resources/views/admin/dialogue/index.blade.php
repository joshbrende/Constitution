@extends('layouts.dashboard')

@section('title', 'Manage Dialogue')
@section('page_heading', 'Manage Dialogue')

@section('content')
<div class="dash-content">
    <section class="dash-panel" style="grid-column: span 2;">
        <div class="dash-panel-header">
            <div>
                <div class="dash-panel-title">Dialogue channels</div>
                <div class="dash-panel-subtitle">
                    Presidium and Leagues dialogue spaces linked to the constitutions.
                </div>
            </div>
            <div>
                <a href="{{ route('admin.dialogue.reports.index') }}" class="dash-btn-ghost" style="text-decoration:none;">Reports</a>
            </div>
        </div>

        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif

        @if ($channels->isEmpty())
            <p class="dash-panel-subtitle" style="margin-top:1rem;">
                No channels found. Run the DialogueSeeder to create Presidium and League channels.
            </p>
        @else
            <table class="dash-table" style="margin-top:1rem;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Threads</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($channels as $ch)
                        <tr>
                            <td>
                                <a href="{{ route('admin.dialogue.threads.index', $ch) }}" style="color:var(--zanupf-gold);text-decoration:none;">
                                    <strong>{{ $ch->name }}</strong>
                                </a>
                            </td>
                            <td><code style="font-size:0.8rem;">{{ $ch->slug }}</code></td>
                            <td>{{ $ch->threads_count }}</td>
                            <td style="white-space:nowrap;">
                                <a href="{{ route('admin.dialogue.threads.index', $ch) }}" style="font-size:0.8rem;color:var(--zanupf-gold);margin-right:0.5rem;">
                                    View topics
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection

