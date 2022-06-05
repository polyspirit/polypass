@extends('layouts.app')

@section('content')

<h3>{{ $user->name }}</h3>
<ul class="list-group">
  <li class="list-group-item"><b>E-mail:</b> {{ $user->email }}</li>
  <li class="list-group-item"><b>Roles:</b> {{ $user->getRoleNames()->implode('items', ', ') }}</li>
</ul>

@endsection