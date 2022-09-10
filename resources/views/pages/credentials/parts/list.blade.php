<div class="table-responsive">
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col" width="50">{{ __('global.type') }}</th>
                <th scope="col">{{ __('credentials.name') }}</th>
                @if (!isset($groups))
                    <th scope="col">{{ __('credentials.group') }}</th>
                @endif
                <th scope="col" width="400" class="text-end">{{ __('global.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($groups))
                @foreach ($groups as $group)
                    <tr>
                        <th scope="row">
                            <i class="fa-regular fa-folder" title="{{ __('entities.group') }}"></i>
                        </th>
                        <td>
                            <a href="/groups/{{ $group->id }}">
                                <b>{{ $group->name }}</b>
                            </a>
                        </td>
                        <td class="d-flex align-items-stretch align-items-md-center justify-content-end gap-1">
                            <a href="/credentials/create?group_id={{ $group->id }}" class="btn btn-primary" title="{{ __('credentials.create') }}">
                                <i class="fa-solid fa-plus"></i>
                                <i class="fa-solid fa-key"></i>
                            </a>
                            <a href="/groups/{{ $group->id }}/edit" class="btn btn-primary" title="{{ __('global.edit') }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="/groups/{{ $group->id }}" method="POST" class="d-flex">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" title="{{ __('global.delete') }}">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif

            @foreach ($credentials as $credential)
                <tr>
                    <th scope="row">
                        @if ($credential->remote)
                            <i class="fa-solid fa-server" title="{{ __('credentials.remote') }}"></i>
                        @else
                            <i class="fa-solid fa-key" title="{{ __('entities.credential') }}"></i>
                        @endif
                    </th>
                    <td>
                        <a href="/credentials/{{ $credential->id }}">
                            {{ $credential->name }}
                        </a>
                    </td>
                    @if (!isset($groups))
                        <td>
                            <a href="/groups/{{ $credential->group->id }}">
                                @if (!$credential->group->isRoot())
                                    {{ $credential->group->name }}
                                @endif
                            </a>
                        </td>
                    @endif
                    <td class="d-flex align-items-stretch align-items-md-center justify-content-end gap-1">
                        <a href="/credentials/{{ $credential->id }}/edit" class="btn btn-primary" title="{{ __('global.edit') }}">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <form action="/credentials/{{ $credential->id }}" method="POST" class="d-flex">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger"
                                title="{{ __('global.delete') }}">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
