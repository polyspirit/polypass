<div class="mb-3">
    @if (empty($disabled))
        <button type="submit" class="btn btn-primary"
            title="{{ isset($note) ? __('global.update') : __('global.submit') }}">
            <i class="fa-solid fa-check"></i>
        </button>
        @if (isset($note))
            @can('delete', $note)
                <button class="btn btn-danger js-fake-button" data-id="note-{{ $note->id }}-delete"
                    title="{{ __('global.delete') }}">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            @endcan
        @endif
    @else
        <a href="/notes/{{ $note->id }}/edit" class="btn btn-primary" title="{{ __('global.edit') }}">
            <i class="fa-regular fa-pen-to-square"></i>
        </a>
    @endif
</div>
