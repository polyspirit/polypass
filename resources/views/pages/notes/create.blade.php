@extends('layouts.app')

@section('content')

@can('create', App\Models\Note::class)

@include('pages.notes.parts.forms.default')

@else
<h3>{{__('notes.error-create') }}</h3>
@endcan

@endsection
