@extends('layouts.app')

@section('content')

@can('create', App\Models\Group::class)

@include('pages.groups.parts.forms.default')

@else
<h3>{{__('groups.error-create') }}</h3>
@endcan

@endsection