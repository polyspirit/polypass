@extends('layouts.app')

@section('content')

<h3>{{ $credential->name }}</h3>
<ul class="list-group mb-4">
    <li class="list-group-item"><b>{{__('credentials.login') }}:</b> {{ $credential->login }}</li>
    <li class="list-group-item"><b>{{__('credentials.password') }}:</b> {{ $credential->password }}</li>
    <li class="list-group-item"><b>{{__('credentials.note') }}:</b> {{ $credential->note }}</li>
</ul>

@endsection