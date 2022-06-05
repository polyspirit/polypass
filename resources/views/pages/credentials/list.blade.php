@extends('layouts.app')

@section('content')

@can('create', App\Models\Credential::class)
<a href="credentials/create" class="btn btn-primary mb-4">{{__('credentials.create') }}</a>
@endcan
<div class="table-responsive">
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col" width="50">id</th>
                <th scope="col">{{ __('credentials.name') }}</th>
                <th scope="col" width="100">{{ __('global.edit') }}</th>
                <th scope="col" width="100">{{ __('global.delete') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($credentials as $credential)
            <tr>
                <th scope="row">{{ $credential->id }}</th>
                <td><a href="/credentials/{{ $credential->id }}">{{ $credential->name }}</a></td>
                <td><a href="/credentials/{{ $credential->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a></td>
                <td>
                    <form action="/credentials/{{ $credential->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" title="{{ __('global.delete') }}">{{ __('global.delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection