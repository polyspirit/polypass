<form action="/credentials{{ isset($credential) ? '/' . $credential->id : '' }}" method="POST" class="mt-2">
    @csrf
    @if (isset($method))
        @method($method)
    @endif

    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'name',
            'title' => __('credentials.name'),
            'value' => isset($credential) ? $credential->name : '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'login',
            'title' => __('credentials.login'),
            'value' => isset($credential) ? $credential->login : '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.password', [
            'title' => __('signin.Password'),
            'value' => isset($credential) ? $credential->password : '',
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.textarea', [
            'name' => 'note',
            'title' => __('credentials.note'),
            'value' => isset($credential) ? $credential->note : '',
        ])
    </div>
    <button type="submit" class="btn btn-primary">{{ __('global.submit') }}</button>
</form>
