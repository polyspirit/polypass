@php
    $value = $value ?? '';
@endphp
<label for="text-{{ $name }}" class="form-label">{{ $title }}</label>
<input type="text" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror" id="text-{{ $name }}" aria-describedby="{{ $name }}Help" value="{{ $value }}">
@error($name)
<div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror