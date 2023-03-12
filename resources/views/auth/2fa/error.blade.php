@extends('layouts.app')

@section('content')
    <h4>
        <div class="alert alert-danger" role="alert">
            {{ $message }}
        </div>
    </h4>
    <a href="{{ route('2fa') }}">{{ __('signin.try_again') }}</a>
@endsection
