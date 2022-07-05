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
            @if (isset($groups))
                @foreach ($groups as $group)
                <tr>
                    <th scope="row">{{ $group->id }}</th>
                    <td><a href="/groups/{{ $group->id }}"><b>{{ $group->name }}</b></a></td>
                    <td><a href="/groups/{{ $group->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a></td>
                    <td>
                        <form action="/groups/{{ $group->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" title="{{ __('global.delete') }}">{{ __('global.delete') }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            @endif

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