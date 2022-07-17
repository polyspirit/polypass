@extends('layouts.app')

@section('content')
    @if ($credential->remote)
        @include('pages.credentials.parts.forms.remote', [
            'method' => 'PATCH',
            'credential' => $credential,
            'disabled' => 'disabled',
        ])
    @else
        @include('pages.credentials.parts.forms.base', [
            'method' => 'PATCH',
            'credential' => $credential,
            'disabled' => 'disabled',
        ])
    @endif
@endsection
