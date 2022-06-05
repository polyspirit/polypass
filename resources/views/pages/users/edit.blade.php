@extends('layouts.app')

@section('content')

@can('update', $user)
<form action="/users/{{ $user->id }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="user-name" class="form-label">Name</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="user-name" value="{{ $user->name }}" aria-describedby="nameHelp">
        @error('name')
        <div id="nameHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="user-email" class="form-label">Email address</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="user-email" value="{{ $user->email }}" aria-describedby="emailHelp">
        @error('email')
        <div id="emailHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="user-password" class="form-label">Password</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="user-password" aria-describedby="passwordHelp">
        @error('password')
        <div id="passwordHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    @can('users-change-roles-any')
    <div class="mb-3">
        <label for="user-role" class="form-label">Status</label>
        <select id="user-role" class="form-select @error('role') is-invalid @enderror" name="role" aria-describedby="roleHelp">
            @foreach (config('roles.roles') as $role => $permissions)
            <option value="{{ $role }}" @selected($user->hasRole($role))>
                {{ __('roles.' . $role) }}
            </option>
            @endforeach
        </select>
        @error('role')
        <div id="roleHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    @endcan
    
    @can('update', $user)
    <button type="submit" class="btn btn-primary">Update</button>
    @endcan
</form>
@else
    <h3>You can't modify this user</h3>
@endcan

@can('delete', $user)
<form action="/users/{{ $user->id }}" method="POST" class="mt-3">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-danger">Delete</button>
</form>
@endcan

@endsection