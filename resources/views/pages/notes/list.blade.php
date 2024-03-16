@extends('layouts.app')

@section('content')

@can('create', App\Models\Note::class)
<a href="/notes/create" class="btn btn-primary mb-4" title="{{__('notes.create') }}">
    <i class="fa-solid fa-plus"></i>
    <i class="fa-solid fa-note-sticky"></i>
</a>
@endcan

@include('pages.notes.parts.list', ['notes' => $notes])


@endsection
