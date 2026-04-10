@extends('layouts.dashboard')

@section('title', 'FAQ')
@section('page_heading', 'FAQ')

@section('content')
    <div class="dash-content">
        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Most Commonly Asked Questions</div>
                    <div class="dash-panel-subtitle">Quick answers for common admin workflows.</div>
                </div>
                <a href="{{ route('admin.guide.help') }}" class="dash-btn-ghost" style="text-decoration:none;">← Help</a>
            </div>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
            @endif

            <div style="display:grid;gap:0.6rem;max-width:56rem;">
                @foreach ($faqs as $i => $item)
                    <details class="faq-item" {{ $i === 0 ? 'open' : '' }}>
                        <summary class="faq-q">
                            <span>{{ $item['q'] }}</span>
                            <span class="faq-chevron">›</span>
                        </summary>
                        <div class="faq-a">{{ $item['a'] }}</div>
                    </details>
                @endforeach
            </div>
        </section>

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Have More Questions</div>
                    <div class="dash-panel-subtitle">Don’t worry! Email us your questions, or use the form below.</div>
                </div>
                <div class="dash-tag">{{ $supportEmail }}</div>
            </div>

            <form method="POST" action="{{ route('admin.guide.faq.questions.store') }}">
                @csrf

                <div style="display:grid;grid-template-columns: 1fr 1fr; gap:0.9rem; max-width:56rem;">
                    <div>
                        <label class="form-label" for="name">Name</label>
                        <input id="name" name="name" class="form-input" value="{{ old('name', auth()->user()->name . ' ' . auth()->user()->surname) }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="form-input" value="{{ old('email', auth()->user()->email) }}" required>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" for="subject">Subject (optional)</label>
                        <input id="subject" name="subject" class="form-input" value="{{ old('subject') }}" placeholder="e.g. Certificates, Academy, Dialogue moderation">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" for="message">Message</label>
                        <textarea id="message" name="message" rows="6" class="form-input" required placeholder="Type your question...">{{ old('message') }}</textarea>
                        <p class="form-help">This will be emailed to the developers and stored for tracking.</p>
                    </div>
                </div>

                <div style="margin-top:1rem;display:flex;gap:0.75rem;align-items:center;">
                    <button type="submit" class="form-btn-primary">Send question</button>
                    <a class="dash-btn-ghost" href="mailto:{{ $supportEmail }}?subject=Admin%20FAQ%20Question" style="text-decoration:none;">
                        Or email {{ $supportEmail }}
                    </a>
                </div>
            </form>
        </section>
    </div>

    <style>
        .faq-item {
            border: 1px solid rgba(31,41,55,0.9);
            border-radius: 0.8rem;
            background: rgba(2,6,23,0.35);
            overflow: hidden;
        }
        .faq-q {
            list-style: none;
            cursor: pointer;
            padding: 0.75rem 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-weight: 600;
        }
        .faq-q::-webkit-details-marker { display:none; }
        .faq-chevron { color: var(--text-muted); transform: rotate(90deg); display:inline-block; transition: transform 0.12s ease; }
        details[open] .faq-chevron { transform: rotate(-90deg); }
        .faq-a {
            padding: 0.05rem 0.9rem 0.9rem 0.9rem;
            color: var(--text-muted);
            line-height: 1.55;
            font-size: 0.92rem;
        }
        .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
        .form-input { width:100%; padding:0.5rem 0.65rem; border:1px solid var(--border-subtle); border-radius:0.4rem; background:rgba(15,23,42,0.9); color:var(--text-main); font-size:0.95rem; }
        .form-input:focus { outline:none; border-color:var(--zanupf-gold); }
        .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; line-height:1.4; }
        .form-btn-primary { padding:0.55rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.5rem; cursor:pointer; font-weight:700; font-size:0.9rem; }
        .form-btn-primary:hover { filter:brightness(1.1); }
        @media (max-width: 720px) {
            form > div { grid-template-columns: 1fr !important; }
        }
    </style>
@endsection

