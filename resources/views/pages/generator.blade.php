@extends('layouts.app')

@section('content')

<h3>{{ __('generator.password-generator') }}</h3>
@include('parts.generator')

@endsection