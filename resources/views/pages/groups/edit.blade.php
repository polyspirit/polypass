@extends('layouts.app')

@section('content')
    @can('update', $group)
        @include('pages.groups.parts.forms.default', ['method' => 'PATCH', 'group' => $group])
    @else
        <h3>{{ __('groups.error-update') }}</h3>
    @endcan

    @can('delete', $group)
        <form action="/groups/{{ $group->id }}" method="POST" class="hidden-form mt-3" id="group-{{ $group->id }}-delete">
            @csrf
            @method('DELETE')
        </form>
    @endcan
@endsection
