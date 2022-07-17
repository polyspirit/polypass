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
        <button type="submit" class="btn btn-primary">
            {{ isset($credential) ? __('global.update') : __('global.submit') }}
        </button>
    @else
        <a href="/groups/{{ $group->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a>
    @endif
</form>
