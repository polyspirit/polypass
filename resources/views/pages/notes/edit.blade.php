@extends('layouts.app')

@section('content')
    @can('update', $note)
        @include('pages.notes.parts.forms.default', ['method' => 'PATCH', 'note' => $note])
    @else
        <h3>{{ __('notes.error-update') }}</h3>
    @endcan

    @can('delete', $note)
        <form action="/notes/{{ $note->id }}" method="POST" class="hidden-form mt-3" id="note-{{ $note->id }}-delete">
            @csrf
            @method('DELETE')
        </form>
    @endcan
@endsection
