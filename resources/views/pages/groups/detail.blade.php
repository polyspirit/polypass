@extends('layouts.app')

@section('content')
    @include('pages.groups.parts.forms.default', [
        'method' => 'PATCH',
        'group' => $group,
        'disabled' => 'disabled',
    ])

    @if($group->type === 'credential' || $group->type === 'root')
        <div class="pb-4"></div>
        <h4>{{ __('entities.credentials') }}</h4>

        @include('pages.credentials.parts.list', ['credentials' => $group->credentials->sortBy('name')])
    @endif

    @if($group->type === 'note' || $group->type === 'root')
        <div class="pb-4"></div>
        <h4>{{ __('entities.notes') }}</h4>

        @include('pages.notes.parts.list', ['notes' => $group->notes->sortBy('name')])
    @endif
@endsection
