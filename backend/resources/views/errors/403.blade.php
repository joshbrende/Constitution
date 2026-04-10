@extends('errors.layout-simple')

@section('title', 'Access denied')

@section('content')
    <h1>Access denied</h1>
    <p>You do not have permission to view this page.</p>
    <a href="{{ url('/') }}">Back to home</a>
@endsection

@section('code')
    Error 403
@endsection
