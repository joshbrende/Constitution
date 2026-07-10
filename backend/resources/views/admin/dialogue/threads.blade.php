@extends('layouts.dashboard')

@section('title', 'Dialogue – '.$channel->name)
@section('page_heading', 'Dialogue – '.$channel->name)

@section('content')
<div class="dash-content">
    <section class="dash-panel" style="grid-column: span 2;">
        <div class="dash-panel-header">
            <div>
                <div class="dash-panel-title">{{ $channel->name }}</div>
                <div class="dash-panel-subtitle">
                    Start a topic with an opening message. Members join the same thread and reply in real time on mobile.
                </div>
            </div>
            <a href="{{ route('admin.dialogue.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Channels</a>
        </div>

        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <div class="dash-subpanel">
            <h2 class="dash-subpanel-title">Start new chat</h2>
            <p class="dash-subpanel-subtitle">
                The opening message is posted as <strong style="color:var(--zanupf-gold);font-weight:600;">Editor</strong> and appears at the top of the shared feed for all members in this channel.
            </p>

            <form method="POST" action="{{ route('admin.dialogue.threads.store', $channel) }}">
                @csrf
                <div class="dash-form-field">
                    <label class="dash-form-label" for="dialogue_new_thread_title">Topic title</label>
                    <input
                        id="dialogue_new_thread_title"
                        type="text"
                        name="title"
                        class="dash-form-input"
                        value="{{ old('title') }}"
                        placeholder="e.g. Application of Article 1 in districts"
                        required
                        maxlength="255"
                    >
                </div>

                <div class="dash-form-field">
                    <label class="dash-form-label" for="dialogue_opening_message">Opening message</label>
                    <textarea
                        id="dialogue_opening_message"
                        name="opening_message"
                        class="dash-form-textarea"
                        rows="4"
                        placeholder="Introduce the topic and invite members to respond…"
                        required
                        maxlength="4000"
                    >{{ old('opening_message') }}</textarea>
                    <span class="dash-form-hint">Visible to all members when they open the thread.</span>
                </div>

                <button type="submit" class="dash-btn-primary">Start chat</button>
            </form>
        </div>

        <h3 class="dash-section-heading">Active chats</h3>

        @if ($threads->isEmpty())
            <p class="dash-panel-subtitle" style="margin-top:0.25rem;">
                No chats yet. Use the form above to open the first conversation in this channel.
            </p>
        @else
            <div class="dash-table-wrap">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Constitution</th>
                            <th>Started</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($threads as $t)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.dialogue.threads.show', $t) }}" class="dash-link-action" style="font-weight:600;">
                                        {{ $t->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="dash-count-badge">{{ $t->messages_count }}</span>
                                </td>
                                <td>
                                    @if ($t->status === 'open')
                                        <span class="status-pill status-pill--active"><span class="dot"></span>Open</span>
                                    @else
                                        <span class="status-pill status-pill--locked"><span class="dot"></span>Locked</span>
                                    @endif
                                </td>
                                <td style="font-size:0.76rem;color:var(--text-muted);max-width:14rem;">
                                    @if ($t->zanupfSection)
                                        <div>ZANU PF: {{ $t->zanupfSection->title }}</div>
                                    @endif
                                    @if ($t->zimbabweSection)
                                        <div>Zimbabwe: {{ $t->zimbabweSection->title }}</div>
                                    @endif
                                    @if (! $t->zanupfSection && ! $t->zimbabweSection)
                                        <span>—</span>
                                    @endif
                                </td>
                                <td style="font-size:0.78rem;color:var(--text-muted);white-space:nowrap;">
                                    {{ $t->created_at?->diffForHumans() }}
                                </td>
                                <td style="white-space:nowrap;text-align:right;">
                                    <a href="{{ route('admin.dialogue.threads.show', $t) }}" class="dash-link-action">Open thread →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:0.85rem;">
                {{ $threads->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
