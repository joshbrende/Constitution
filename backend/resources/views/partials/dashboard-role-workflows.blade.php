{{--
  Role workflows: how Content Editor → Presidium approval is executed (constitution amendments).
  Data from DashboardWorkflowService + config/role_workflows.php
--}}
@if(!empty($workflowAlerts ?? []))
    <div class="dash-workflow-alerts" style="grid-column:1/-1;margin-bottom:1rem;">
        @foreach($workflowAlerts as $line)
            <div style="padding:0.65rem 0.85rem;border-radius:0.5rem;border:1px solid rgba(250,204,21,0.35);background:rgba(250,204,21,0.08);font-size:0.88rem;margin-bottom:0.5rem;">
                <strong style="color:var(--zanupf-gold);">Action:</strong> {{ $line }}
            </div>
        @endforeach
    </div>
@endif

@if(!empty($workflowPanels ?? []))
    <div class="dash-workflow-panels" style="grid-column:1/-1;display:grid;gap:1rem;margin-bottom:1.25rem;">
        <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);">Your responsibilities (by assigned role)</div>
        @foreach($workflowPanels as $panel)
            <div style="padding:1rem 1.1rem;border-radius:0.65rem;border:1px solid var(--border-subtle);background:var(--bg-panel);">
                <div style="font-weight:700;font-size:1rem;margin-bottom:0.35rem;color:var(--zanupf-gold);">{{ $panel['title'] }}</div>
                @if(!empty($panel['summary']))
                    <p style="margin:0 0 0.75rem;font-size:0.88rem;color:var(--text-muted);">{{ $panel['summary'] }}</p>
                @endif
                @if(!empty($panel['steps']))
                    <ol style="margin:0;padding-left:1.2rem;font-size:0.85rem;line-height:1.55;color:var(--text-main);">
                        @foreach($panel['steps'] as $step)
                            <li style="margin-bottom:0.35rem;">{{ $step }}</li>
                        @endforeach
                    </ol>
                @endif
            </div>
        @endforeach
    </div>
@endif

@if(!empty($pendingCounts ?? []) && auth()->user()?->roles?->isNotEmpty())
    @php
        $showPresidiumQueue = app(\App\Services\AdminAccessService::class)->canAccessSection(auth()->user(), 'constitution');
    @endphp
    @if($showPresidiumQueue && (($pendingCounts['pending_presidium_approvals'] ?? 0) > 0 || ($pendingCounts['draft_amendments'] ?? 0) > 0))
        <div style="grid-column:1/-1;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem;">
            Amendment pipeline:
            <strong>{{ $pendingCounts['draft_amendments'] ?? 0 }}</strong> draft
            ·
            <strong>{{ $pendingCounts['pending_presidium_approvals'] ?? 0 }}</strong> in review (Presidium)
            —
            <a href="{{ route('admin.constitution.index') }}" style="color:var(--zanupf-gold);">Open constitution admin</a>
        </div>
    @endif
@endif
