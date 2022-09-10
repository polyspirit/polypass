<form action="/groups{{ isset($group) ? '/' . $group->id : '' }}" method="POST" class="mt-2">
    @csrf
    @if (isset($method))
        @method($method)
    @endif

    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'name',
            'title' => __('groups.name'),
            'value' => isset($group) ? $group->name : '',
        ])
    </div>
    @if(empty($disabled))
        <button type="submit" class="btn btn-primary" title="{{ isset($credential) ? __('global.update') : __('global.submit') }}">
            <i class="fa-solid fa-check"></i>
        </button>
    @else
        <a href="/groups/{{ $group->id }}/edit" class="btn btn-primary" title="{{ __('global.edit') }}">
            <i class="fa-regular fa-pen-to-square"></i>
        </a>
        <a href="/credentials/create?group_id={{$group->id}}" class="btn btn-primary" title="{{ __('credentials.create') }}">
            <i class="fa-solid fa-plus"></i>
            <i class="fa-solid fa-key"></i>
        </a>
    @endif
</form>
