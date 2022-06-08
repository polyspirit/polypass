@extends('layouts.app')

@section('content')

<h3>{{ $credential->name }}</h3>
<ul class="list-group mb-4">
    @if($credential->remote)
    <li class="list-group-item"><b>{{__('remote.host') }}:</b> {{ $credential->remote->host }}</li>
    <li class="list-group-item"><b>{{__('remote.port') }}:</b> {{ $credential->remote->port }}</li>
    <li class="list-group-item"><b>{{__('remote.protocol') }}:</b> {{ $credential->remote->protocol }}</li>
    @endif

    <li class="list-group-item"><b>{{__('credentials.login') }}:</b> {{ $credential->login }}</li>
    <li class="list-group-item"><b>{{__('credentials.password') }}:</b> {{ $credential->password }}</li>
    <li class="list-group-item"><b>{{__('credentials.note') }}:</b> {{ $credential->note }}</li>
</ul>

@endsection