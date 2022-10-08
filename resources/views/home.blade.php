@extends('layouts.app')

@section('content')

<h3>{{ __('credentials.favorite') }}</h3>
@include('pages.credentials.parts.list', ['credentials' => $favorites, 'groups' => null])

<hr>
<h3>{{ __('entities.credentials') }}</h3>
@include('pages.credentials.parts.list', ['credentials' => $credentials])

@endsection