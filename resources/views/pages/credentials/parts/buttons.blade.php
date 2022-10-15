<div class="mb-3">
    @if (empty($disabled))
        <button type="submit" class="btn btn-primary"
            title="{{ isset($credential) ? __('global.update') : __('global.submit') }}">
            <i class="fa-solid fa-check"></i>
        </button>
        @if (isset($credential))
            @can('delete', $credential)
                <button class="btn btn-danger js-fake-button" data-id="credential-{{ $credential->id }}-delete"
                    title="{{ __('global.delete') }}">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            @endcan
        @endif
    @else
        <a href="/credentials/{{ $credential->id }}/edit" class="btn btn-primary" title="{{ __('global.edit') }}">
            <i class="fa-regular fa-pen-to-square"></i>
        </a>
    @endif
</div>
