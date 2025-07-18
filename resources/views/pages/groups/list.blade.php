@extends('layouts.app')

@section('content')
    @can('create', App\Models\Group::class)
        <a href="/groups/create" class="btn btn-primary mb-4" title="{{ __('groups.create') }}">
            <i class="fa-solid fa-plus"></i>
            <i class="fa-regular fa-folder"></i>
        </a>
    @endcan

    @php
        $lastGroupType = '';
    @endphp
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col" width="50">{{ __('global.type') }}</th>
                    <th scope="col">{{ __('groups.name') }}</th>
                    <th scope="col" width="400" class="text-end">{{ __('global.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $group)
                    @if ($group->type !== $lastGroupType)
                        <tr>
                            <td class="bg-body-tertiary" colspan="3">
                                <h3 class="mb-0 mt-1">{{  __('groups.for_' . $group->type) }}</h3>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th scope="row">
                            <i class="fa-regular fa-folder" title="{{ __('entities.group') }}"></i>
                        </th>
                        <td><a href="/groups/{{ $group->id }}">{{ $group->name }}</a></td>
                        <td class="d-flex align-items-stretch align-items-md-center justify-content-end gap-1">
                            @if($group->type === 'credential' || $group->type === 'root')
                                <a href="/credentials/create?group_id={{ $group->id }}" class="btn btn-primary"
                                    title="{{ __('credentials.create') }}">
                                    <i class="fa-solid fa-plus"></i>
                                    <i class="fa-solid fa-key"></i>
                                </a>
                            @endif
                            @if($group->type === 'note' || $group->type === 'root')
                                <a href="/notes/create?group_id={{ $group->id }}" class="btn btn-primary"
                                    title="{{ __('notes.create') }}">
                                    <i class="fa-solid fa-plus"></i>
                                    <i class="fa-solid fa-sticky-note"></i>
                                </a>
                            @endif
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
                    @php
                        $lastGroupType = $group->type;
                    @endphp
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
