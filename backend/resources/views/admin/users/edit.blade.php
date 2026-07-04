@extends('layouts.dashboard')

@section('title', 'Edit User – ' . $user->name)
@section('page_heading', 'Edit User')

@section('content')
    <div class="dash-content">
        @if (session('success'))
            <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">{{ $user->name }} {{ $user->surname }}</div>
                    <div class="dash-panel-subtitle">{{ $user->email }} · Party profile, membership standing, and admin roles.</div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Users</a>
            </div>

            <div style="margin-bottom:1.25rem;padding:1rem;border-radius:0.5rem;border:1px solid var(--border-subtle);background:rgba(250,204,21,0.06);">
                <div style="font-weight:700;font-size:0.9rem;margin-bottom:0.5rem;color:var(--zanupf-gold);">Role assignment guide (System Administrator)</div>
                <p style="margin:0 0 0.75rem;font-size:0.85rem;line-height:1.5;color:var(--text-muted);">This page controls which admin areas this user can access. Follow these steps:</p>
                <ol style="margin:0;padding-left:1.2rem;font-size:0.85rem;line-height:1.7;color:var(--text-muted);">
                    <li><strong>Check or uncheck roles</strong> — Each checkbox corresponds to one role. Check the roles this user should have (e.g. Academy Manager, Content Editor, User Manager). Uncheck any role you want to remove. The user will lose access to that role’s sections after you save.</li>
                    <li><strong>Click “Update roles”</strong> — Changes are not saved until you click the green button. There is no automatic save.</li>
                    <li><strong>Effect is immediate</strong> — Once saved, the user gains or loses access on their next page load. No re-login required.</li>
                    <li><strong>What each role does</strong> — Academy Manager = courses only; User Manager = Users & Members; Presidium = approve constitutional amendments; Content Editor = edit constitution and library; Analytics Viewer = read-only reports; Audit Viewer = read-only audit logs.</li>
                    <li><strong>Restricted roles</strong> — Only a System Administrator can assign <strong>System Admin</strong> or <strong>Presidium</strong>. User Managers cannot assign these two roles.</li>
                </ol>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:1.5rem;padding:1rem;border-radius:0.5rem;border:1px solid var(--border-subtle);">
                    <div style="font-weight:700;font-size:0.9rem;margin-bottom:0.75rem;color:var(--zanupf-gold);">Party profile</div>
                    <div style="display:grid;gap:1rem;max-width:40rem;">
                        <div>
                            <label for="membership_standing" class="form-label">Membership standing</label>
                            <select id="membership_standing" name="membership_standing" class="form-input" style="max-width:20rem;">
                                @foreach ($membershipStandings as $standing)
                                    <option value="{{ $standing->value }}" {{ old('membership_standing', $user->membership_standing?->value ?? 'applicant') === $standing->value ? 'selected' : '' }}>
                                        {{ $standing->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-help">Applicant → Provisional (exam pass) → Full member (certificate issued). Suspended blocks academy and dialogue.</p>
                        </div>
                        <div>
                            <label for="wing" class="form-label">League / wing</label>
                            <select id="wing" name="wing" class="form-input" style="max-width:20rem;">
                                <option value="">— Main structure —</option>
                                @foreach ($wings as $wing)
                                    @if ($wing !== 'main')
                                        <option value="{{ $wing }}" {{ old('wing', $user->wing) === $wing ? 'selected' : '' }}>{{ ucfirst($wing) }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="form-help">Assign after branch verification. Drives Youth / Women's / Veterans academy access.</p>
                        </div>
                        <div>
                            <label for="province_id" class="form-label">Province</label>
                            <select id="province_id" name="province_id" class="form-input" style="max-width:20rem;">
                                <option value="">— Not set —</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}" {{ (string) old('province_id', $user->province_id) === (string) $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="suspension_reason" class="form-label">Suspension reason (if setting Suspended)</label>
                            <input id="suspension_reason" type="text" name="suspension_reason" value="{{ old('suspension_reason') }}" class="form-input" maxlength="500" placeholder="Optional note for audit log">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:1.5rem;padding:1rem;border-radius:0.5rem;border:1px solid var(--border-subtle);">
                    <div style="font-weight:700;font-size:0.9rem;margin-bottom:0.75rem;color:var(--zanupf-gold);">Branch admission &amp; cadre</div>
                    <div style="display:grid;gap:1rem;max-width:40rem;">
                        <div>
                            <input type="hidden" name="branch_admitted" value="0">
                            <label style="display:flex;align-items:flex-start;gap:0.5rem;cursor:pointer;">
                                <input
                                    type="checkbox"
                                    name="branch_admitted"
                                    value="1"
                                    {{ old('branch_admitted', $user->hasBranchAdmission() ? '1' : '0') ? 'checked' : '' }}
                                    style="margin-top:0.2rem;"
                                >
                                <span>
                                    <span style="font-weight:600;">Branch admission confirmed</span>
                                    <span style="display:block;font-size:0.75rem;color:var(--text-muted);margin-top:0.25rem;">
                                        Required before enrolling in Youth / Women's / Veterans league courses. Confirm after provincial branch register verification.
                                    </span>
                                    @if ($user->hasBranchAdmission())
                                        <span style="display:block;font-size:0.72rem;color:var(--zanupf-gold);margin-top:0.35rem;">
                                            Confirmed {{ $user->branch_admitted_at?->format('d M Y H:i') }}
                                            @if ($user->branchAdmittedBy)
                                                by {{ $user->branchAdmittedBy->name }} {{ $user->branchAdmittedBy->surname }}
                                            @endif
                                        </span>
                                    @endif
                                </span>
                            </label>
                        </div>
                        <div>
                            <label for="branch_admission_note" class="form-label">Branch admission note</label>
                            <input id="branch_admission_note" type="text" name="branch_admission_note" value="{{ old('branch_admission_note', $user->branch_admission_note) }}" class="form-input" maxlength="500" placeholder="Optional reference to branch register or provincial office">
                        </div>
                        <div>
                            <input type="hidden" name="cadre_designated" value="0">
                            <label style="display:flex;align-items:flex-start;gap:0.5rem;cursor:pointer;">
                                <input
                                    type="checkbox"
                                    name="cadre_designated"
                                    value="1"
                                    {{ old('cadre_designated', $user->isCadreDesignee() ? '1' : '0') ? 'checked' : '' }}
                                    style="margin-top:0.2rem;"
                                >
                                <span>
                                    <span style="font-weight:600;">Cadre designee</span>
                                    <span style="display:block;font-size:0.75rem;color:var(--text-muted);margin-top:0.25rem;">
                                        Unlocks leadership library documents. Admin-assigned only — not granted by academy exams.
                                    </span>
                                    @if ($user->isCadreDesignee())
                                        <span style="display:block;font-size:0.72rem;color:var(--zanupf-gold);margin-top:0.35rem;">
                                            Designated {{ $user->cadre_designated_at?->format('d M Y H:i') }}
                                            @if ($user->cadreDesignatedBy)
                                                by {{ $user->cadreDesignatedBy->name }} {{ $user->cadreDesignatedBy->surname }}
                                            @endif
                                        </span>
                                    @endif
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div style="display:grid;gap:1rem;max-width:40rem;">
                    <fieldset style="border:none;padding:0;margin:0;">
                        <legend class="form-label" style="padding:0;">Roles</legend>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:0.5rem;">
                            @foreach ($roles as $role)
                                @php
                                    $canAssign = $assignableRoleIds->contains($role->id);
                                @endphp
                                <label style="display:flex;align-items:flex-start;gap:0.5rem;padding:0.5rem;border-radius:0.3rem;background:rgba(15,23,42,0.5);{{ $canAssign ? '' : ';opacity:0.85;' }}">
                                    <input
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $role->id }}"
                                        {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }}
                                        @disabled(! $canAssign)
                                        style="margin-top:0.2rem;"
                                    >
                                    <span>
                                        <span style="font-weight:600;">{{ $role->name }}</span>
                                        <span style="font-size:0.75rem;color:var(--text-muted);"> ({{ $role->slug }})</span>
                                        @php $duty = $roleDutyMap[$role->slug] ?? null; @endphp
                                        @if ($duty && ! empty($duty['sections']))
                                            <span style="display:block;font-size:0.72rem;color:var(--zanupf-gold);margin-top:0.2rem;">{{ implode(' · ', $duty['sections']) }}</span>
                                        @endif
                                        @if (! $canAssign)
                                            <span style="font-size:0.7rem;color:var(--text-muted);"> locked</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="form-help">See the instruction box above for the complete flow.</p>
                    </fieldset>
                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="form-btn-primary">Save user</button>
                        <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

        <style>
            .form-label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-main); margin-bottom:0.35rem; }
            .form-help { font-size:0.75rem; color:var(--text-muted); margin-top:0.35rem; }
            .form-btn-primary { padding:0.5rem 1.25rem; background:var(--zanupf-green); color:#fff; border:none; border-radius:0.4rem; cursor:pointer; font-weight:600; font-size:0.9rem; }
        </style>
    </div>
@endsection
