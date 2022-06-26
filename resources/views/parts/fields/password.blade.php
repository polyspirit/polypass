@php
$name = $name ?? 'password';
$value = $value ?? '';
@endphp

<label for="password-{{ $name }}" class="form-label">{{ $title }}</label>
<div class="password-wrapper">
    <div class="password-eye">
        <i class="password-eye-icon fa-regular fa-eye"></i>
    </div>
    <input type="password" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
        id="password-{{ $name }}" aria-describedby="{{ $name }}Help" value="{{ $value }}">
</div>
@error($name)
    <div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror
