@extends('layouts.dashboard')

@section('title', 'Dialogue – '.$thread->title)
@section('page_heading', 'Dialogue – '.$thread->channel->name)

@section('content')
<div class="dash-content">
    <section class="dash-panel" style="grid-column: span 2;max-height:calc(100vh - 140px);display:flex;flex-direction:column;">
        <div class="dash-panel-header">
            <div>
                <div class="dash-panel-title">
                    {{ $thread->title }}
                    <span style="font-size:0.8rem;font-weight:500;color:var(--zanupf-gold);margin-left:0.35rem;">
                        — {{ $thread->channel->name }}
                    </span>
                </div>
                <div class="dash-panel-subtitle">
                    @if ($thread->zanupfSection)
                        ZANU PF: {{ $thread->zanupfSection->title }}
                    @endif
                    @if ($thread->zimbabweSection)
                        @if ($thread->zanupfSection) · @endif
                        Zimbabwe: {{ $thread->zimbabweSection->title }}
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:0.5rem;">
                @if ($thread->status === 'open')
                    <form method="POST" action="{{ route('admin.dialogue.threads.lock', $thread) }}">
                        @csrf
                        <button type="submit" class="dash-btn-ghost" style="text-decoration:none;">Lock thread</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.dialogue.threads.unlock', $thread) }}">
                        @csrf
                        <button type="submit" class="dash-btn-ghost" style="text-decoration:none;">Unlock thread</button>
                    </form>
                @endif
                <a href="{{ route('admin.dialogue.threads.index', $thread->channel) }}" class="dash-btn-ghost" style="text-decoration:none;">← Topics</a>
            </div>
        </div>

        <div id="dialogue-thread-messages"
             data-prev="{{ $messages->previousPageUrl() }}"
             data-next="{{ $messages->nextPageUrl() }}"
             style="flex:1;overflow:auto;border-radius:0.75rem;border:1px solid var(--border-subtle);padding:0.6rem;background:rgba(15,23,42,0.9);display:flex;flex-direction:column;">
            @forelse ($messages as $msg)
                @php
                    $rawName = $msg->user?->name ?? 'Member';
                    $displayName = $rawName === 'System' ? 'Editor' : $rawName;
                @endphp
                @if ($msg->is_deleted)
                    <div style="margin-bottom:0.4rem;font-size:0.8rem;color:var(--text-muted);font-style:italic;">
                        Message removed by moderator.
                    </div>
                @else
                    <div style="display:flex;gap:0.6rem;margin-bottom:0.5rem;">
                        <div style="width:30px;height:30px;border-radius:999px;background:#020617;border:1px solid var(--border-subtle);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:600;color:var(--zanupf-gold);">
                            {{ mb_strtoupper(mb_substr($displayName, 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex;justify-content:space-between;gap:0.5rem;">
                                <div style="font-size:0.85rem;font-weight:600;color:var(--text-main);">
                                    {{ $displayName }}
                                    @if ($rawName === 'System')
                                        <span style="font-size:0.75rem;color:var(--zanupf-gold);margin-left:0.25rem;">(editor)</span>
                                    @endif
                                </div>
                                <div style="font-size:0.7rem;color:var(--text-muted);white-space:nowrap;">
                                    {{ $msg->created_at?->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            <div style="margin-top:0.2rem;padding:0.45rem 0.6rem;border-radius:0.6rem;background:rgba(15,23,42,0.95);font-size:0.9rem;color:var(--text-main);white-space:pre-wrap;">
                                {{ $msg->body }}
                            </div>
                            <div style="margin-top:0.25rem;font-size:0.75rem;color:var(--text-muted);display:flex;gap:0.75rem;align-items:center;">
                                @if ($msg->is_pinned)
                                    <span style="color:var(--zanupf-gold);">Pinned</span>
                                @endif
                                <form method="POST" action="{{ $msg->is_pinned ? route('admin.dialogue.messages.unpin', $msg) : route('admin.dialogue.messages.pin', $msg) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" style="background:none;border:none;padding:0;color:var(--zanupf-gold);cursor:pointer;">
                                        {{ $msg->is_pinned ? 'Unpin' : 'Pin' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.dialogue.messages.destroy', $msg) }}" style="display:inline;" onsubmit="return confirm('Remove this message?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;padding:0;color:var(--zanupf-red);cursor:pointer;">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <p style="font-size:0.85rem;color:var(--text-muted);margin-top:0.5rem;">
                    No messages yet in this thread.
                </p>
            @endforelse
            <div style="margin-top:0.25rem;display:flex;justify-content:space-between;align-items:center;gap:0.75rem;">
                <div style="display:flex;gap:0.5rem;font-size:0.8rem;">
                    @if ($messages->onFirstPage())
                        <span style="color:var(--text-muted);">← Previous</span>
                    @else
                        <a href="{{ $messages->previousPageUrl() }}" style="color:var(--zanupf-gold);text-decoration:none;">← Previous</a>
                    @endif
                    @if ($messages->hasMorePages())
                        <a href="{{ $messages->nextPageUrl() }}" style="color:var(--zanupf-gold);text-decoration:none;">Next →</a>
                    @else
                        <span style="color:var(--text-muted);">Next →</span>
                    @endif
                </div>
                <div>
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
        @if ($thread->status === 'open')
            <form method="POST" action="{{ route('admin.dialogue.messages.store', $thread) }}" enctype="multipart/form-data" style="margin-top:0.75rem;">
                @csrf
                <label class="form-label">Post as admin/editor</label>
                <textarea name="body" rows="3" class="form-input" placeholder="Type a message into this thread…" required></textarea>
                <div style="margin-top:0.5rem;">
                    <label class="form-label" style="font-size:0.85rem;">Attach media (images, PDF, audio, video) — max 50MB each</label>
                    <input type="file" name="attachments[]" multiple class="form-input" />
                    @error('attachments.*')
                        <div style="color:var(--zanupf-red);font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="form-btn-primary" style="margin-top:0.5rem;">Send message</button>
            </form>
        @else
            <p style="font-size:0.8rem;color:var(--text-muted);margin-top:0.75rem;">
                This thread is locked. Unlock it to post new messages.
            </p>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const container = document.getElementById('dialogue-thread-messages');
        if (!container) return;

        let touchStartX = null;
        let touchStartY = null;

        container.addEventListener('touchstart', function (e) {
            if (!e.touches || e.touches.length !== 1) return;
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        container.addEventListener('touchend', function (e) {
            if (touchStartX === null || touchStartY === null) return;
            const touchEndX = (e.changedTouches && e.changedTouches[0].clientX) || touchStartX;
            const touchEndY = (e.changedTouches && e.changedTouches[0].clientY) || touchStartY;

            const dx = touchEndX - touchStartX;
            const dy = touchEndY - touchStartY;

            // Only react to mostly horizontal swipes with enough distance
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
                if (dx < 0) {
                    // swipe left -> next page if exists
                    const nextUrl = container.dataset.next;
                    if (nextUrl) window.location.href = nextUrl;
                } else {
                    // swipe right -> previous page if exists
                    const prevUrl = container.dataset.prev;
                    if (prevUrl) window.location.href = prevUrl;
                }
            }

            touchStartX = null;
            touchStartY = null;
        }, { passive: true });
    })();
</script>
@endpush
