@extends('layouts.app')

@section('content')

@can('create', App\Models\Credential::class)
<form action="/credentials" method="POST">
    @csrf

    <div class="mb-3">
        <label for="credential-name" class="form-label">{{__('credentials.name') }}</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="credential-name" aria-describedby="nameHelp">
        @error('name')
        <div id="nameHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="credential-login" class="form-label">{{__('credentials.login') }}</label>
        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror" id="credential-login" aria-describedby="loginHelp">
        @error('login')
        <div id="loginHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="credential-password" class="form-label">{{__('credentials.password') }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="credential-password" aria-describedby="passwordHelp">
        @error('password')
        <div id="passwordHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="project-note" class="form-label">{{__('credentials.note') }}</label>
        <textarea name="note" id="project-note" rows="10" class="form-control @error('note') is-invalid @enderror" aria-describedby="noteHelp"></textarea>
        @error('note')
        <div id="noteHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">{{__('global.submit') }}</button>
</form>
@else
    <h3>{{__('credentials.error-create') }}</h3>
@endcan

@endsection