<form action="/credentials{{ isset($credential) ? '/' . $credential->id : '' }}" method="POST" class="mt-2">
    @csrf
    @if (isset($method))
        @method($method)
    @endif

    <div class="mb-3">
        @php
            $groupId = $group_id ?? (isset($credential) && $credential->group->name !== 'root' ? $credential->group->id : '');
        @endphp
        @include('parts.fields.select', [
            'name' => 'group_id',
            'title' => __('credentials.group'),
            'options' => $groups,
            'value' => $groupId,
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'name',
            'title' => __('credentials.name'),
            'value' => isset($credential) ? $credential->name : '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'login',
            'title' => __('credentials.login'),
            'value' => isset($credential) ? $credential->login : '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.password', [
            'title' => __('credentials.password'),
            'value' => isset($credential) ? $credential->password : '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.textarea', [
            'name' => 'note',
            'title' => __('credentials.note'),
            'value' => isset($credential) ? $credential->note : '',
        ])
    </div>
    @if(empty($disabled))
        <button type="submit" class="btn btn-primary">
            {{ isset($credential) ? __('global.update') : __('global.submit') }}
        </button>
    @else
        <a href="/credentials/{{ $credential->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a>
    @endif
</form>
