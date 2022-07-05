@extends('layouts.app')

@section('content')

<h3>{{ $group->name }}</h3>

@include('pages.credentials.parts.list', ['credentials' => $group->credentials])

@endsection