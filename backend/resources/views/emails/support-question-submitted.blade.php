<div style="font-family: system-ui, -apple-system, Segoe UI, Arial, sans-serif; line-height: 1.5;">
    <h2 style="margin: 0 0 10px 0;">New FAQ question</h2>

    <p style="margin: 0 0 10px 0;">
        <strong>From:</strong> {{ $question->name }} &lt;{{ $question->email }}&gt;<br>
        <strong>Subject:</strong> {{ $question->subject ?: '—' }}<br>
        <strong>Submitted:</strong> {{ $question->created_at?->toDayDateTimeString() }}
    </p>

    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; background: #f9fafb;">
        <div style="white-space: pre-wrap;">{{ $question->message }}</div>
    </div>

    <p style="margin: 12px 0 0 0; color: #6b7280; font-size: 12px;">
        Source: {{ $question->source }} · IP: {{ $question->ip_address ?: '—' }}
    </p>
</div>

