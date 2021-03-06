@php
$value = $value ?? '';
$type = $type ?? 'text';
$disabled = $disabled ?? '';
$attrTitleValue = $disabled ? __('global.copy') . ' ' . $title : $title;
@endphp
<div class="form-control-wrapper">
    <label for="text-{{ $name }}" class="form-label" data-copied="{{ __('global.copied') }}">{{ $title }}</label>
    <input type="{{ $type }}" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
        id="text-{{ $name }}" aria-describedby="{{ $name }}Help" value="{{ $value }}"
        title="{{ $attrTitleValue }}" {{ $disabled }}>
</div>
@error($name)
    <div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror
