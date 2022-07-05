@php
$value = $value ?? '';
@endphp
<label for="select-{{ $name }}" class="form-label">{{ $title }}</label>
<select class="form-select @error($name) is-invalid @enderror" name="{{ $name }}" aria-describedby="statusHelp"
    id="select-{{ $name }}">
    @if (isAssociative($options))
        @foreach ($options as $option => $optionName)
            @if ($value)
                <option value="{{ $option }}" @selected($value == $option)>
                @else
                <option value="{{ $option }}" @selected($loop->first)>
            @endif
            {{ $optionName }}
            </option>
        @endforeach
    @else
        @foreach ($options as $option)
            @if ($value)
                <option value="{{ $option }}" @selected($value == $option)>
                @else
                <option value="{{ $option }}" @selected($loop->first)>
            @endif
            {{ isset($optionsLocalization) ? __($optionsLocalization . $option) : $option }}
            </option>
        @endforeach
    @endif
</select>
@error($name)
    <div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror
