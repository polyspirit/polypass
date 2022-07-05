@extends('layouts.app')

@section('content')

@can('create', App\Models\Group::class)
<a href="groups/create" class="btn btn-primary mb-4">{{__('groups.create') }}</a>
@endcan
<div class="table-responsive">
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col" width="50">id</th>
                <th scope="col">{{ __('groups.name') }}</th>
                <th scope="col" width="100">{{ __('global.edit') }}</th>
                <th scope="col" width="100">{{ __('global.delete') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $group)
            <tr>
                <th scope="row">{{ $group->id }}</th>
                <td><a href="/groups/{{ $group->id }}">{{ $group->name }}</a></td>
                <td><a href="/groups/{{ $group->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a></td>
                <td>
                    <form action="/groups/{{ $group->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" title="{{ __('global.delete') }}">{{ __('global.delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection