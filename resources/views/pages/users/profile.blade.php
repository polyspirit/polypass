@extends('layouts.app')

@section('content')

<h3>{{ $user->name }}</h3>
<ul class="list-group">
  <li class="list-group-item"><b>{{ __('users.email') }}:</b> {{ $user->email }}</li>
  <li class="list-group-item"><b>{{ __('users.roles') }}:</b> {{ $user->getRoleNames()->implode('items', ', ') }}</li>
</ul>

@endsection