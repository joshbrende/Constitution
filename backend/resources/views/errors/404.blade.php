@extends('errors.layout-simple')

@section('title', 'Page not found')

@section('content')
    <h1>Page not found</h1>
    <p>The page you are looking for does not exist or has been moved.</p>
    <a href="{{ url('/') }}">Back to home</a>
@endsection

@section('code')
    Error 404
@endsection
