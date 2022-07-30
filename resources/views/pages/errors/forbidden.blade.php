@extends('layouts.app')

@section('content')

<div class="container">
    <div class="alert alert-danger" role="alert">
        <h1>{{ __('errors.forbidden') }}</h1>
        <a href="/">{{ __('global.to_main') }}</a>
    </div>
</div>

@endsection