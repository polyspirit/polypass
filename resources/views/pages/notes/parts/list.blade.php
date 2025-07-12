<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr>
                <th scope="col" width="50">{{ __('global.type') }}</th>
                <th scope="col">{{ __('notes.name') }}</th>
                @if (!isset($groups))
                    <th scope="col">{{ __('notes.group') }}</th>
                @endif
                <th scope="col" width="300" class="text-end">{{ __('global.actions') }}</th>
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
                            <a href="/groups/{{ $group->id }}" title="{{ __('global.view') . ' ' . $group->name }}">
                                <b>{{ $group->name }}</b>
                            </a>
                        </td>
                        <td class="d-flex align-items-stretch align-items-md-center justify-content-end gap-1">
                            <a href="/notes/create?group_id={{ $group->id }}" class="btn btn-primary"
                                title="{{ __('notes.create') }}">
                                <i class="fa-solid fa-plus"></i>
                                <i class="fa-solid fa-sticky-note"></i>
                            </a>
                            <a href="/groups/{{ $group->id }}/edit" class="btn btn-primary"
                                title="{{ __('global.edit') }}">
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

            @foreach ($notes as $note)
                <tr>
                    <th scope="row">
                        <i class="fa-solid fa-note-sticky" title="{{ __('entities.note') }}"></i>
                    </th>
                    <td>
                        <a href="/notes/{{ $note->id }}"
                            title="{{ __('global.view') . ' ' . $note->name }}">
                            {{ $note->name }}
                        </a>
                    </td>
                    @if (!isset($groups))
                        <td>
                            <a href="/groups/{{ $note->group->id }}">
                                @if (!$note->group->isRoot())
                                    {{ $note->group->name }}
                                @endif
                            </a>
                        </td>
                    @endif
                    <td>
                        <div class="d-flex align-items-stretch align-items-md-center justify-content-end gap-1">
                            <a href="/notes/{{ $note->id }}/edit" class="btn btn-primary"
                                title="{{ __('global.edit') }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="/notes/{{ $note->id }}" method="POST" class="d-flex">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" title="{{ __('global.delete') }}">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
