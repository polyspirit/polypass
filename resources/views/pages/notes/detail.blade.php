@extends('layouts.app')

@section('content')
    @include('pages.notes.parts.forms.default', [
        'method' => 'PATCH',
        'note' => $note,
        'disabled' => 'disabled',
    ])
@endsection
