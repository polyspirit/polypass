@extends('layouts.app')

@section('content')

@can('create', App\Models\Credential::class)
<a href="credentials/create" class="btn btn-primary mb-4">{{__('credentials.create') }}</a>
@endcan

@include('pages.credentials.parts.list', ['credentials' => $credentials])


@endsection