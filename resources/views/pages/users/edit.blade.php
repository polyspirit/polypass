@extends('layouts.app')

@section('content')

@can('update', $user)
    @include('pages.users.parts.form')
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