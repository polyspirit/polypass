@extends('layouts.app')

@section('content')

<h3>{{ __('credentials.favorite') }}</h3>
@include('pages.credentials.parts.list', ['credentials' => $credentials])

<hr>

<h3>{{ __('generator.password-generator') }}</h3>
@include('parts.generator', ['credentials' => $credentials])

@endsection