@php
    $items = $productionChecklist ?? [];
    $pendingCount = collect($items)->whereIn('status', ['warn', 'fail', 'info'])->count();
@endphp

<div class="production-checklist">
    <div class="section-heading" style="margin:22px 0 10px;">
        <h3 style="margin:0;font-size:15px;">Production checklist</h3>
        @include('setup.partials.field-tip', [
            'tip' => 'Complete these after the wizard. Items marked <strong>warn</strong> should be addressed before go-live. The wizard does not configure mail, CORS, or cron automatically.',
            'aria' => 'Production checklist help',
        ])
    </div>

    @if ($pendingCount > 0)
        <div class="hint" style="margin-top:0;">
            {{ $pendingCount }} item(s) still need attention from your hosting or technical team.
        </div>
    @endif

    <ul class="prod-check-list">
        @foreach ($items as $item)
            <li class="prod-check-item prod-check-item--{{ $item['status'] }}">
                <span class="status {{ $item['status'] === 'info' ? 'warn' : $item['status'] }}">{{ $item['status'] }}</span>
                <div class="prod-check-body">
                    <strong>{{ $item['label'] }}</strong>
                    <span>{{ $item['message'] }}</span>

                    @if (! empty($item['command']))
                        <div class="prod-check-action">
                            <code class="prod-check-cmd" id="prod-cmd-{{ $item['key'] }}">{{ $item['command'] }}</code>
                            <button type="button" class="env-copy-btn" data-copy-target="prod-cmd-{{ $item['key'] }}">Copy command</button>
                        </div>
                    @endif

                    @if (! empty($item['env_block']))
                        <div class="prod-check-action">
                            <pre class="prod-check-env" id="prod-env-{{ $item['key'] }}">{{ $item['env_block'] }}</pre>
                            <button type="button" class="env-copy-btn" data-copy-target="prod-env-{{ $item['key'] }}">Copy .env lines</button>
                        </div>
                    @endif

                    @if ($item['key'] === 'invite_admins')
                        <p class="prod-check-link">
                            After completing installation:
                            <strong>Admin → Users → Invite backend user</strong>
                            @if (\Illuminate\Support\Facades\Route::has('admin.users.invite.create'))
                                (<code>/admin/users/invite/create</code>)
                            @endif
                        </p>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>

@once
    @push('head')
        <style>
            .prod-check-list{
                list-style:none;
                margin:12px 0 0;
                padding:0;
                border:1px solid var(--line);
                border-radius:4px;
                overflow:hidden;
            }
            .prod-check-item{
                display:flex;
                gap:12px;
                align-items:flex-start;
                padding:12px 14px;
                border-bottom:1px solid var(--line);
                font-size:13px;
                background:#fff;
            }
            .prod-check-item:last-child{ border-bottom:none; }
            .prod-check-item:nth-child(even){ background:#f8fafc; }
            .prod-check-body strong{ display:block; margin-bottom:4px; font-size:13px; }
            .prod-check-body > span{ display:block; color:var(--muted); font-size:12px; line-height:1.45; }
            .prod-check-action{
                margin-top:10px;
                display:flex;
                flex-wrap:wrap;
                gap:8px;
                align-items:flex-start;
            }
            .prod-check-cmd,
            .prod-check-env{
                flex:1 1 100%;
                margin:0;
                padding:10px 12px;
                border-radius:4px;
                background:#0f172a;
                color:#e2e8f0;
                font-size:11px;
                line-height:1.5;
                overflow-x:auto;
                border:1px solid #334155;
            }
            .prod-check-link{
                margin:10px 0 0;
                font-size:12px;
                color:var(--ink);
                line-height:1.45;
            }
            .status.info{
                background:#e0f2fe;
                color:#0369a1;
            }
        </style>
    @endpush
@endonce
