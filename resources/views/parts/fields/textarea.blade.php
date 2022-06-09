@php
    $value = $value ?? '';
@endphp
<label for="textarea-{{ $name }}" class="form-label">{{ $title }}</label>
<textarea name="{{ $name }}" id="textarea-{{ $name }}" rows="10" class="form-control @error($name) is-invalid @enderror" aria-describedby="{{ $name }}Help">{{ $value }}</textarea>
@error($name)
<div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror