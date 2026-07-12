@extends('layouts.dashboard')

@section('title', 'Create party member')
@section('page_heading', 'Create party member')

@section('content')
    <div class="dash-content">
        @if ($errors->any())
            <div class="dash-alert dash-alert--error">{{ $errors->first() }}</div>
        @endif

        <section class="dash-panel" style="grid-column: span 2;">
            <div class="dash-panel-header">
                <div>
                    <div class="dash-panel-title">Create party member account</div>
                    <div class="dash-panel-subtitle">
                        Creates the account immediately and emails a temporary password. Skips the academy exam; certificate payment is still required before a membership number is issued.
                        Not for staff/dashboard users — use Users → Invite backend user for that.
                    </div>
                </div>
                <a href="{{ route('admin.members.index') }}" class="dash-btn-ghost" style="text-decoration:none;">← Members</a>
            </div>

            <form method="POST" action="{{ route('admin.members.store') }}" style="max-width:40rem;">
                @csrf
                <div style="display:grid;gap:1rem;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="name" class="form-label">First name <span style="color:var(--zanupf-red);">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="form-input">
                        </div>
                        <div>
                            <label for="surname" class="form-label">Surname <span style="color:var(--zanupf-red);">*</span></label>
                            <input id="surname" type="text" name="surname" value="{{ old('surname') }}" required class="form-input">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="form-label">Email <span style="color:var(--zanupf-red);">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input">
                    </div>
                    <div>
                        <label for="national_id" class="form-label">National ID @if($requireNationalId)<span style="color:var(--zanupf-red);">*</span>@endif</label>
                        <input id="national_id" type="text" name="national_id" value="{{ old('national_id') }}" class="form-input" @if($requireNationalId) required @endif>
                    </div>
                    <div>
                        <label for="province_id" class="form-label">Province <span style="color:var(--zanupf-red);">*</span></label>
                        <select id="province_id" name="province_id" required class="form-input">
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
                    <button type="submit" class="dash-btn-primary" style="justify-self:start;">Create member</button>
                </div>
            </form>
        </section>
    </div>
@endsection
