@extends('layouts.dashboard')

@section('title', 'Dialogue – '.$channel->name)
@section('page_heading', 'Dialogue – '.$channel->name)

@section('content')
<div class="dash-content">
    <section class="dash-panel" style="grid-column: span 2;">
        <div class="dash-panel-header">
            <div>
                <div class="dash-panel-title">{{ $channel->name }} topics</div>
                <div class="dash-panel-subtitle">
                    Threads in this channel. Click a topic to open the chat window and moderate messages.
                </div>
            </div>
            <a href="{{ route('admin.dialogue.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Channels</a>
        </div>

        <form method="POST" action="{{ route('admin.dialogue.threads.store', $channel) }}" style="margin-top:0.5rem;margin-bottom:1rem;">
            @csrf
            <div style="display:flex;gap:0.5rem;align-items:flex-end;max-width:32rem;">
                <div style="flex:1;">
                    <label class="form-label" for="dialogue_new_thread_title">Start new topic</label>
                    <input id="dialogue_new_thread_title" type="text" name="title" class="form-input" placeholder="e.g. Application of Article 1 in districts" required>
                </div>
                <button type="submit" class="form-btn-primary" style="padding:0.45rem 1rem;">Create</button>
            </div>
        </form>

        <table class="dash-table" style="margin-top:1rem;">
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
                        <td><strong>{{ $t->title }}</strong></td>
                        <td>{{ $t->messages_count }}</td>
                        <td>{{ ucfirst($t->status) }}</td>
                        <td style="font-size:0.75rem;">
                            @if ($t->zanupfSection)
                                <div>ZANU PF: {{ $t->zanupfSection->title }}</div>
                            @endif
                            @if ($t->zimbabweSection)
                                <div>Zimbabwe: {{ $t->zimbabweSection->title }}</div>
                            @endif
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-muted);">
                            {{ $t->created_at?->diffForHumans() }}
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('admin.dialogue.threads.show', $t) }}" style="font-size:0.8rem;color:var(--zanupf-gold);">
                                Open
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:0.75rem;">
            {{ $threads->links() }}
        </div>
    </section>
</div>
@endsection

