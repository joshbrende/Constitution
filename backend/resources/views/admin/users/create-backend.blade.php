@extends('layouts.dashboard')

@section('title', 'Create backend user')
@section('page_heading', 'Create backend user')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Create account now</div>
                    <div class="dash-panel-subtitle">Creates the user immediately and emails login credentials plus role-specific duties. Use invite instead if the person should choose their own password.</div>
                </div>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.users.invite.create') }}" class="dash-btn-ghost" style="text-decoration:none;">Invite by link instead</a>
                    <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Users</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.store-backend') }}" style="max-width:48rem;">
                @csrf
                <div style="display:grid;gap:1rem;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div>
                            <label for="name" class="form-label">First name <span style="color:var(--zanupf-red);">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="form-input">
                            @error('name')
                                <p class="form-help" style="color:#fecaca;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="surname" class="form-label">Surname <span style="color:var(--zanupf-red);">*</span></label>
                            <input id="surname" type="text" name="surname" value="{{ old('surname') }}" required class="form-input">
                            @error('surname')
                                <p class="form-help" style="color:#fecaca;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email" class="form-label">Email <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input" autocomplete="off" placeholder="staff@example.org.zw">
                        @error('email')
                            <p class="form-help" style="color:#fecaca;">{{ $message }}</p>
                        @enderror
                    </div>

                    <fieldset style="border:none;padding:0;margin:0;">
                        <legend class="form-label" style="padding:0;">Backend roles <span style="color:var(--zanupf-red);">*</span></legend>
                        <p class="form-help" style="margin-top:0;">Scoped access only — see areas each role may use.</p>
                        @include('admin.users.partials.role-duty-checkboxes', ['roles' => $roles, 'roleDutyBriefs' => $roleDutyBriefs])
                        @error('roles')
                            <p class="form-help" style="color:#fecaca;">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div style="display:flex;gap:0.75rem;">
                        <button type="submit" class="form-btn-primary">Create user &amp; send welcome email</button>
                        <a href="{{ route('admin.users.index') }}" class="dash-btn-ghost" style="text-decoration:none;padding:0.5rem 1rem;">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

        @include('admin.users.partials.provision-form-styles')
    </div>
@endsection
