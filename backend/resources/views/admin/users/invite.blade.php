@extends('layouts.dashboard')

@section('title', 'Invite backend user')
@section('page_heading', 'Invite backend user')

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
                    <div class="dash-panel-title">Invite by email</div>
                    <div class="dash-panel-subtitle">The person receives a welcome email with login URL, assigned roles, and specific duties. They activate the account via a secure link (7 days).</div>
                </div>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.users.create-backend') }}" class="dash-btn-ghost" style="text-decoration:none;">Create with password instead</a>
                    <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Users</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.invite.store') }}" style="max-width:48rem;">
                @csrf
                <div style="display:grid;gap:1rem;">
                    <div>
                        <label for="invite_email" class="form-label">Email <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="invite_email" type="email" name="email" value="{{ old('email') }}" required class="form-input" autocomplete="off" placeholder="staff@example.org.zw">
                        @error('email')
                            <p class="form-help" style="color:#fecaca;">{{ $message }}</p>
                        @enderror
                    </div>

                    <fieldset style="border:none;padding:0;margin:0;">
                        <legend class="form-label" style="padding:0;">Backend roles <span style="color:var(--zanupf-red);">*</span></legend>
                        <p class="form-help" style="margin-top:0;">Only roles with admin dashboard access are listed. Each role is limited to its admin areas — not full system access.</p>
                        @include('admin.users.partials.role-duty-checkboxes', ['roles' => $roles, 'roleDutyBriefs' => $roleDutyBriefs])
                        @error('roles')
                            <p class="form-help" style="color:#fecaca;">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="form-btn-primary">Send invitation email</button>
                        <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

        @include('admin.users.partials.provision-form-styles')
    </div>
@endsection
