@extends('layouts.app')

@section('content')

@can('update', $user)
<form action="/users/{{ $user->id }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'name',
            'title' => __('users.name'),
            'value' => $user->name,
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'email',
            'type' => 'email',
            'title' => __('users.email'),
            'value' => $user->email,
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.password', [
            'title' => __('signin.new_password')
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.password', [
            'name' => 'password_confirmation',
            'title' => __('signin.confirm_password')
        ])
    </div>
    @can('users-change-roles-any')
    <div class="mb-3">
        <label for="user-role" class="form-label">{{ __('users.role') }}</label>
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
    <button type="submit" class="btn btn-primary">{{ __('global.update') }}</button>
    @endcan
</form>
@else
    <h3>You can't modify this user</h3>
@endcan

@can('delete', $user)
<form action="/users/{{ $user->id }}" method="POST" class="mt-3">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-danger">{{ __('global.delete') }}</button>
</form>
@endcan

@endsection