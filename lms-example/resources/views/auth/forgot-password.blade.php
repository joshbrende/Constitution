@extends('layouts.app')

@section('title', 'Forgot password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <p class="text-muted small mb-2">{{ config('app.name') }}</p>
                    <h2 class="h4 mb-4">Forgot password</h2>
                    <p class="text-muted small mb-4">Enter your email and we’ll send you a link to reset your password.</p>
                    <form action="{{ route('password.email') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send reset link</button>
                    </form>
                    <p class="mt-3 mb-0 text-muted small"><a href="{{ route('login') }}">Back to login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
