@extends('layouts.app')

@section('content')

<h3>{{ __('credentials.favorite') }}</h3>
@include('pages.credentials.parts.list', ['credentials' => $credentials])

<h3>{{ __('generator.password-generator') }}</h3>

@endsection