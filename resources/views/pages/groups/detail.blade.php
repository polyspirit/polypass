@extends('layouts.app')

@section('content')
    @include('pages.groups.parts.forms.default', [
        'method' => 'PATCH',
        'group' => $group,
        'disabled' => 'disabled',
    ])

    <div class="pb-4"></div>
    <h4>{{ __('entities.credentials') }}</h4>

    @include('pages.credentials.parts.list', ['credentials' => $group->credentials])
@endsection
