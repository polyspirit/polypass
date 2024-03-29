@php
    $value = $value ?? '';
    $disabled = $disabled ?? '';
    $attrTitleValue = $disabled ? __('global.copy') . ' ' . $title : $title;
    $cssClass = !empty($css_class) ? 'form-select ' . $css_class : 'form-select';
@endphp
<div class="form-control-wrapper">
    <label for="select-{{ $name }}" class="form-label"
        data-copied="{{ __('global.copied') }}">{{ $title }}</label>
    <select class="{{ $cssClass }} @error($name) is-invalid @enderror" name="{{ $name }}"
        aria-describedby="statusHelp" id="select-{{ $name }}" title="{{ $attrTitleValue }}" {{ $disabled }}>
        @if (isset($handler) && is_callable($handler))
            @foreach ($options as $key => $option)
                {!! $handler($key, $option) !!}
            @endforeach
        @elseif (isAssociative($options))
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
</div>
@error($name)
    <div id="{{ $name }}Help" class="form-text alert alert-danger">{{ $message }}</div>
@enderror
