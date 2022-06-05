@extends('layouts.app')

@section('content')

@can('update', $credential)
<form action="/credentials/{{ $credential->id }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="credential-name" class="form-label">{{__('credentials.name') }}</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="credential-name" value="{{ $credential->name }}" aria-describedby="nameHelp">
        @error('name')
        <div id="nameHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="credential-login" class="form-label">{{__('credentials.login') }}</label>
        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror" id="credential-login" value="{{ $credential->login }}" aria-describedby="loginHelp">
        @error('login')
        <div id="loginHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="credential-password" class="form-label">{{__('credentials.password') }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="credential-password" value="{{ $credential->password }}" aria-describedby="passwordHelp">
        @error('password')
        <div id="passwordHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="project-note" class="form-label">{{__('credentials.note') }}</label>
        <textarea name="note" id="project-note" rows="10" class="form-control @error('note') is-invalid @enderror" aria-describedby="noteHelp">{{ $credential->note }}</textarea>
        @error('note')
        <div id="noteHelp" class="form-text alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">{{__('global.submit') }}</button>
</form>
@else
    <h3>{{__('credentials.error-update') }}</h3>
@endcan

@can('delete', $credential)
<form action="/credentials/{{ $credential->id }}" method="POST" class="mt-3">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-danger">{{__('global.delete') }}</button>
</form>
@endcan

@endsection