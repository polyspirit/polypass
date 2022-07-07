@extends('layouts.app')

@section('content')

@can('create', App\Models\Group::class)
<a href="groups/create" class="btn btn-primary mb-4">{{__('groups.create') }}</a>
@endcan
<div class="table-responsive">
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col" width="50">{{ __('global.type') }}</th>
                <th scope="col">{{ __('groups.name') }}</th>
                <th scope="col" width="400" class="text-end">{{ __('global.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $group)
            <tr>
                <th scope="row">
                    <i class="fa-regular fa-folder" title="{{ __('entities.group') }}"></i>
                </th>
                <td><a href="/groups/{{ $group->id }}">{{ $group->name }}</a></td>
                <td class="d-flex align-items-stretch align-items-md-center justify-content-end gap-1">
                    <form action="/credentials" method="POST" class="d-flex">
                        @csrf
                        <input type="hidden" name="group_id" value="{{$group->id}}">
                        <button class="btn btn-primary" title="{{ __('credentials.create') }}">
                            {{ __('credentials.create') }}
                        </button>
                    </form>
                    <a href="/groups/{{ $group->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a>
                    <form action="/groups/{{ $group->id }}" method="POST" class="d-flex">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" title="{{ __('global.delete') }}">
                            {{ __('global.delete') }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection