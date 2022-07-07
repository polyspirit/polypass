@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col" width="50">id</th>
                <th scope="col">{{ __('users.name') }}</th>
                <th scope="col">{{ __('users.email') }}</th>
                <th scope="col" width="400" class="text-end">{{ __('global.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td><a href="users/{{ $user->id }}">{{ $user->name }}</a></td>
                <td>{{ $user->email }}</td>
                <td class="d-flex align-items-stretch align-items-md-center justify-content-end gap-1">
                    @can('update', $user)
                    <a href="users/{{ $user->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a>
                    @endcan
                    @can('delete', $user)
                    <form action="/users/{{ $user->id }}" method="POST" class="d-flex">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" title="{{ __('global.delete') }}">
                            {{ __('global.delete') }}
                        </button>
                    </form>
                    @endcan
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection