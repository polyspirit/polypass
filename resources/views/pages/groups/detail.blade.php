@extends('layouts.app')

@section('content')
    @include('pages.groups.parts.forms.default', [
        'method' => 'PATCH',
        'group' => $group,
        'disabled' => 'disabled',
    ])

    @if($group->type === 'credential' || $group->type === 'root')
        <div class="pb-4"></div>
        <div class="d-flex justify-content-between align-items-center">
            <h4>{{ __('entities.credentials') }}</h4>
            <a href="/credentials/create?group_id={{ $group->id }}" class="btn btn-primary"
                title="{{ __('credentials.create') }}">
                <i class="fa-solid fa-plus"></i>
                <i class="fa-solid fa-key"></i>
            </a>
        </div>

        @include('pages.credentials.parts.list', ['credentials' => $group->credentials->sortBy('name')])
    @endif

    @if($group->type === 'note' || $group->type === 'root')
        <div class="pb-4"></div>
        <div class="d-flex justify-content-between align-items-center">
            <h4>{{ __('entities.notes') }}</h4>
            <a href="/notes/create?group_id={{ $group->id }}" class="btn btn-primary"
                title="{{ __('notes.create') }}">
                <i class="fa-solid fa-plus"></i>
                <i class="fa-solid fa-sticky-note"></i>
            </a>
        </div>

        @include('pages.notes.parts.list', ['notes' => $group->notes->sortBy('name')])
    @endif
@endsection
