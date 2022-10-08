@extends('layouts.app')

@section('content')

@can('create', App\Models\Credential::class)
<a href="/credentials/create" class="btn btn-primary mb-4" title="{{__('credentials.create') }}">
    <i class="fa-solid fa-plus"></i>
    <i class="fa-solid fa-key"></i>
</a>
@endcan

<h3>{{ __('credentials.favorite') }}</h3>
@include('pages.credentials.parts.list', ['credentials' => $favorites, 'groups' => null])

<hr>
<h3>{{ __('entities.credentials') }}</h3>
@include('pages.credentials.parts.list', ['credentials' => $credentials])

@endsection