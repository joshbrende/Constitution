@extends('layouts.app')

@section('title', 'Verify your email')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Verify your email</h1>
                    <p class="text-muted">We sent a verification link to <strong>{{ auth()->user()->email }}</strong>. Please click the link to verify your email.</p>
                    @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <p class="mb-0">If you did not receive the email,</p>
                    <form action="{{ route('verification.send') }}" method="post" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-primary">Resend verification email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
