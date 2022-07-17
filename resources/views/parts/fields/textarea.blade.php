@php
$value = $value ?? '';
$disabled = $disabled ?? '';
$attrTitleValue = $disabled ? __('global.copy') . ' ' . $title : $title;
@endphp
<div class="form-control-wrapper">
    <label for="textarea-{{ $name }}" class="form-label" data-copied="{{ __('global.copied') }}">{{ $title }}</label>
    <textarea name="{{ $name }}" id="textarea-{{ $name }}" rows="10"
        class="form-control @error($name) is-invalid @enderror" aria-describedby="{{ $name }}Help"
        title="{{ $attrTitleValue }}" {{ $disabled }}>
        {{ $value }}
    </textarea>
</div>
@error($name)
    <div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror
