@php
    $name = $name ?? 'password';
@endphp
<label for="password-{{ $name }}" class="form-label">{{ $title }}</label>
<input type="password" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror" id="password-{{ $name }}" aria-describedby="{{ $name }}Help">
@error($name)
<div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror