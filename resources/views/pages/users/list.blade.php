@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col" width="50">id</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col" width="100">Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td><a href="users/{{ $user->id }}">{{ $user->name }}</a></td>
                <td>{{ $user->email }}</td>
                <td>
                    @can('update', $user)
                    <a href="users/{{ $user->id }}/edit" class="btn btn-primary">Edit</a>
                    @endcan
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection