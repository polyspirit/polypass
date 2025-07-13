<form action="/credentials{{ isset($credential) ? '/' . $credential->id : '' }}" method="POST" class="mt-2">
    @csrf
    @if (isset($method))
        @method($method)
    @endif

    @include('pages.credentials.parts.buttons', [
        'disabled' => $disabled ?? null,
        'credential' => $credential ?? null
    ])
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
        @include('parts.fields.text', [
            'name' => 'url',
            'title' => __('global.link'),
            'value' => isset($credential) ? $credential->url : '',
        ])
    </div>
    <div class="mb-3">
        <div class="d-none">
            @include('parts.fields.textarea', [
                'name' => 'note',
                'title' => __('credentials.note'),
                'value' => isset($credential) ? $credential->note : '{"ops":[{"insert":"\n"}]}',
            ])
        </div>
        <div class="mb-2">{{ __('credentials.note') }}</div>
        <div id="note-editor" data-readonly="{{ empty($disabled) ? 0 : 1 }}" data-initial-content="{{ isset($credential) && $credential->note ? (is_string($credential->note) && json_decode($credential->note) ? $credential->note : json_encode(['ops' => [['insert' => $credential->note]]])) : '{"ops":[{"insert":"\n"}]}' }}"></div>
    </div>
    <div class="mb-3">
        @include('parts.fields.checkbox', [
            'name' => 'favorite',
            'title' => __('credentials.favorite'),
            'value' => isset($credential) ? $credential->favorite : 0,
        ])
    </div>
</form>
