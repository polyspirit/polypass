@php
$value = $value ?? 0;
$title = $title ?? '';
$disabled = $disabled ?? '';
$checked = $value ? 'checked' : '';
$randomId = rand(1, 999);
@endphp
<div class="form-check">
    <input class="form-check-input" type="checkbox" name="{{ $name }}" id="checkbox-{{ $name }}-{{ $randomId }}" {{ $disabled }} {{ $checked }}>
    <label class="form-check-label" for="checkbox-{{ $name }}-{{ $randomId }}">
        {{ $title }}
    </label>
</div>