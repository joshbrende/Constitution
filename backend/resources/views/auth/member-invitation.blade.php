@extends('layouts.auth')

@section('title', 'Accept membership invitation – ZANU PF Academy')

@section('content')
    <h1 class="auth-title">Accept membership invitation</h1>
    <p class="auth-subtitle">
        Create your account for <strong>{{ $invitation->email }}</strong>. You will skip the academy exam and complete certificate payment to receive your membership number.
    </p>

    @if ($errors->any())
        <div class="helper-text" role="alert" style="color:#fecaca;margin-bottom:0.75rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ url('/invitations/member/'.$token) }}">
        @csrf

        <div class="form-group">
            <label for="email_display">Email</label>
            <input id="email_display" type="email" value="{{ $invitation->email }}" disabled autocomplete="username">
        </div>

        <div class="form-group">
            <label for="name">First name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $invitation->name) }}" required autocomplete="given-name">
        </div>

        <div class="form-group">
            <label for="surname">Surname</label>
            <input id="surname" type="text" name="surname" value="{{ old('surname', $invitation->surname) }}" required autocomplete="family-name">
        </div>

        <div class="form-group">
            <label for="national_id">National ID @if($requireNationalId)*@endif</label>
            <input id="national_id" type="text" name="national_id" value="{{ old('national_id', $invitation->national_id) }}" @if($requireNationalId) required @endif autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            <p class="helper-text" style="margin-top:0.35rem;">
                At least 8 characters, with upper and lower case letters and a number.
            </p>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label style="display:flex;gap:0.5rem;align-items:flex-start;">
                <input type="checkbox" name="accept_terms" value="1" required style="margin-top:0.25rem;">
                <span>I accept the terms of use and privacy policy.</span>
            </label>
        </div>

        <div class="actions-row">
            <button type="submit" class="btn-primary">
                <span>Create account</span>
            </button>
            <a href="{{ route('login') }}" class="text-link">Back to login</a>
        </div>
    </form>
@endsection
