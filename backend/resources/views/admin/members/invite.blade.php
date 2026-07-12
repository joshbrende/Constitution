@extends('layouts.dashboard')

@section('title', 'Invite party member')
@section('page_heading', 'Invite party member')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Invite party member (bypass exam)</div>
                    <div class="dash-panel-subtitle">
                        Sends an email link. They skip the academy exam but must still complete certificate payment and Presidium approval before receiving a membership number.
                        This is not a staff/dashboard invitation.
                    </div>
                </div>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.members.create') }}" class="dash-btn-ghost" style="text-decoration:none;">Create account instead</a>
                    <a href="{{ route('admin.members.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Members</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.members.invite.store') }}" style="max-width:40rem;">
                @csrf
                <div style="display:grid;gap:1rem;">
                    <div>
                        <label for="email" class="form-label">Email <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="name" class="form-label">First name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-input">
                        </div>
                        <div>
                            <label for="surname" class="form-label">Surname</label>
                            <input id="surname" type="text" name="surname" value="{{ old('surname') }}" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label for="national_id" class="form-label">National ID @if($requireNationalId)<span style="color:var(--zanupf-red);">*</span>@endif</label>
                        <input id="national_id" type="text" name="national_id" value="{{ old('national_id') }}" class="form-input" @if($requireNationalId) required @endif>
                    </div>
                    <div>
                        <label for="province_id" class="form-label">Province</label>
                        <select id="province_id" name="province_id" class="form-input">
                            <option value="">—</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->id }}" @selected(old('province_id') == $province->id)>{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="wing" class="form-label">Wing</label>
                        <select id="wing" name="wing" class="form-input">
                            <option value="">—</option>
                            @foreach ($wings as $wing)
                                <option value="{{ $wing }}" @selected(old('wing') === $wing)>{{ ucfirst($wing) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="dash-btn-primary" style="justify-self:start;">Send invitation</button>
                </div>
            </form>
        </section>
    </div>
@endsection
