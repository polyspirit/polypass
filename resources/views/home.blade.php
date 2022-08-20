@extends('layouts.app')

@section('content')

<h3>{{ __('credentials.favorite') }}</h3>
@include('pages.credentials.parts.list', ['credentials' => $credentials])

@endsection