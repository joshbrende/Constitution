@extends('errors.layout-simple')

@section('title', 'Something went wrong')

@section('content')
    <h1>Something went wrong</h1>
    <p>We could not complete your request. Please try again in a few minutes.</p>
    <a href="{{ url('/') }}">Back to home</a>
@endsection

@section('code')
    Error 500
@endsection
