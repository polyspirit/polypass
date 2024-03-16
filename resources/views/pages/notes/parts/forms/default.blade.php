<form action="/notes{{ isset($note) ? '/' . $note->id : '' }}" method="POST" class="mt-2">
    @csrf
    @if (isset($method))
        @method($method)
    @endif

    @include('pages.notes.parts.buttons', [
        'disabled' => $disabled ?? null,
        'note' => $note ?? null
    ])

    <div class="mb-3">
        @php
            $groupId = $group_id ?? (isset($note) && $note->group->name !== 'root' ? $note->group->id : '');
        @endphp
        @include('parts.fields.select', [
            'name' => 'group_id',
            'title' => __('notes.group'),
            'options' => $groups,
            'value' => $groupId,
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'name',
            'title' => __('notes.name'),
            'value' => $note?->name ?? '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.textarea', [
            'name' => 'note',
            'title' => __('notes.note'),
            'value' => isset($note) ? $note->note : '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.checkbox', [
            'name' => 'favorite',
            'title' => __('notes.favorite'),
            'value' => isset($note) ? $note->favorite : 0,
        ])
    </div>
</form>
