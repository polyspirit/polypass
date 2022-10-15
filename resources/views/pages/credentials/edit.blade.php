@extends('layouts.app')

@section('content')
    @can('update', $credential)
        @if ($credential->remote)
            @include('pages.credentials.parts.forms.remote', [
                'method' => 'PATCH',
                'credential' => $credential,
            ])
        @else
            @include('pages.credentials.parts.forms.base', [
                'method' => 'PATCH',
                'credential' => $credential,
            ])
        @endif
    @else
        <h3>{{ __('credentials.error-update') }}</h3>
    @endcan

    @can('delete', $credential)
        <form action="/credentials/{{ $credential->id }}" method="POST" class="hidden-form mt-3"
            id="credential-{{ $credential->id }}-delete">
            @csrf
            @method('DELETE')
        </form>
    @endcan
@endsection
