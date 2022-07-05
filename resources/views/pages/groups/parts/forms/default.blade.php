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
    <button type="submit" class="btn btn-primary">{{ __('global.submit') }}</button>
</form>
