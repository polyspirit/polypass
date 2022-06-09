@php
$value = $value ?? '';
@endphp
<label for="select-{{ $name }}" class="form-label">{{ $title }}</label>
<select class="form-select @error($name) is-invalid @enderror" name="{{ $name }}" aria-describedby="statusHelp"
    id="select-{{ $name }}">
    @foreach ($options as $option)
        @if ($value)
            <option value="{{ $option }}" @selected($value == $option)>
            @else
            <option value="{{ $option }}" @selected($loop->first)>
        @endif
        {{ __($optionsLocalization . $option) }}
        </option>
    @endforeach
</select>
@error($name)
    <div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror
