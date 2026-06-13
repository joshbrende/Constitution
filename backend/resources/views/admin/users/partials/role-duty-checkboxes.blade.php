@php
    $dutyBySlug = collect($roleDutyBriefs ?? [])->keyBy('slug');
@endphp

<div style="display:grid;gap:0.75rem;margin-top:0.75rem;">
    @foreach ($roles as $role)
        @php
            $brief = $dutyBySlug->get($role->slug);
            $oldRoleIds = collect(old('roles', []))->map(fn ($v) => (int) $v);
        @endphp
        <label style="display:block;padding:0.75rem;border-radius:0.45rem;border:1px solid var(--border-subtle);background:rgba(15,23,42,0.55);cursor:pointer;">
            <div style="display:flex;align-items:flex-start;gap:0.6rem;">
                <input
                    type="checkbox"
                    name="roles[]"
                    value="{{ $role->id }}"
                    {{ $oldRoleIds->contains((int) $role->id) ? 'checked' : '' }}
                    style="margin-top:0.2rem;"
                >
                <div style="flex:1;">
                    <div style="font-weight:700;color:var(--text-main);">{{ $role->name }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.35rem;">({{ $role->slug }})</div>
                    @if ($brief)
                        <p style="margin:0 0 0.35rem;font-size:0.82rem;line-height:1.45;color:var(--text-muted);">{{ $brief['summary'] }}</p>
                        @if (! empty($brief['sections']))
                            <p style="margin:0;font-size:0.78rem;color:var(--zanupf-gold);">
                                <strong>Can access:</strong> {{ implode(' · ', $brief['sections']) }}
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </label>
    @endforeach
</div>
