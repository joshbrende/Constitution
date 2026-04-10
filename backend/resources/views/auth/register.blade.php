@extends('layouts.auth')

@section('title', 'Register – ZANU PF Academy')

@section('content')
    <h1 class="auth-title">Create your account</h1>
    <p class="auth-subtitle">
        Register to access the Constitution of ZANU PF, Academy learning, and administrative tools.
    </p>

    @if ($errors->any())
        <div class="helper-text" style="color:#fecaca;margin-bottom:0.75rem;">
            Please correct the highlighted fields and try again.
        </div>
    @endif

    <form method="POST" action="{{ url('/register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autocomplete="given-name"
            >
            @error('name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="surname">Surname</label>
            <input
                id="surname"
                type="text"
                name="surname"
                value="{{ old('surname') }}"
                required
                autocomplete="family-name"
            >
            @error('surname')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
            >
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="password-toggle" data-toggle-password="password">
                    Show
                </button>
            </div>
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
            <div class="helper-text">
                Minimum 8 characters. Use a strong, private password.
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Retype Password</label>
            <div class="password-wrapper">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="password-toggle" data-toggle-password="password_confirmation">
                    Show
                </button>
            </div>
        </div>

        <div class="checkbox-row">
            <input
                id="accept_terms"
                type="checkbox"
                name="accept_terms"
                value="1"
                {{ old('accept_terms') ? 'checked' : '' }}
                required
            >
            <label for="accept_terms" style="margin-bottom:0;">
                I accept all terms and conditions governing the use of this system and the handling of party information.
            </label>
        </div>
        @error('accept_terms')
            <div class="error-text">{{ $message }}</div>
        @enderror

        <div class="actions-row">
            <button type="submit" class="btn-primary">
                <span>Sign up</span>
            </button>

            <a href="{{ route('login') }}" class="text-link">
                Already registered? Log in
            </a>
        </div>
    </form>
@endsection

