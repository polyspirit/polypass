<label for="select-{{ $name }}" class="form-label">{{ $title }}</label>
<select class="form-select @error($name) is-invalid @enderror" name="{{ $name }}" aria-describedby="statusHelp" id="select-{{ $name }}">
    @foreach ($options as $option)
    <option value="{{ $option }}" @selected($loop->first)>
        {{ __($optionsLocalization . $option) }}
    </option>
    @endforeach
</select>
@error($name)
<div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror