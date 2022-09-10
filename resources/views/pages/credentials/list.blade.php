@extends('layouts.app')

@section('content')

@can('create', App\Models\Credential::class)
<a href="/credentials/create" class="btn btn-primary mb-4" title="{{__('credentials.create') }}">
    <i class="fa-solid fa-plus"></i>
    <i class="fa-solid fa-key"></i>
</a>
@endcan

@include('pages.credentials.parts.list', ['credentials' => $credentials])


@endsection