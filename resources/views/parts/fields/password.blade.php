@php
$name = $name ?? 'password';
$value = $value ?? '';
$disabled = $disabled ?? '';
$attrTitleValue = $disabled ? __('global.copy') . ' ' . $title : $title;
@endphp

<div class="form-control-wrapper">
    <label for="password-{{ $name }}" class="form-label"
        data-copied="{{ __('global.copied') }}">{{ $title }}</label>
    <div class="password-wrapper">
        <div class="password-icons">
            @if (!$disabled)
                <i class="password-gen-icon fa-solid fa-shuffle"></i>
            @endif
            <i class="password-eye-icon fa-regular fa-eye"></i>
        </div>
        <input type="password" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
            id="password-{{ $name }}" aria-describedby="{{ $name }}Help" value="{{ $value }}"
            title="{{ $attrTitleValue }}" {{ $disabled }}>
    </div>
</div>
@error($name)
    <div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror
