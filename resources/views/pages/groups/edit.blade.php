@extends('layouts.app')

@section('content')
    @can('update', $group)
        @include('pages.groups.parts.forms.default', ['method' => 'PATCH', 'group' => $group])
    @else
        <h3>{{ __('groups.error-update') }}</h3>
    @endcan

    @can('delete', $group)
        <form action="/groups/{{ $group->id }}" method="POST" class="mt-3">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger">{{ __('global.delete') }}</button>
        </form>
    @endcan
@endsection
