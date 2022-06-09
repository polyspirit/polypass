<form action="/credentials{{ isset($credential) ? '/' . $credential->id : '' }}" method="POST" class="mt-2">
    @csrf
    @if (isset($method))
        @method($method)
    @endif

    <div class="container-fluid g-0">
        <div class="row mb-3">
            <div class="col">
                @include('parts.fields.text', [
                    'name' => 'name',
                    'title' => __('credentials.name'),
                    'value' => isset($credential) ? $credential->name : '',
                ])
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                @include('parts.fields.text', [
                    'name' => 'host',
                    'title' => __('remote.host'),
                    'value' => isset($credential->remote) ? $credential->remote->host : '',
                ])
            </div>
            <div class="col-3">
                @include('parts.fields.text', [
                    'name' => 'port',
                    'title' => __('remote.port'),
                    'value' => isset($credential->remote) ? $credential->remote->port : '',
                ])
            </div>
            <div class="col-3">
                @include('parts.fields.select', [
                    'name' => 'protocol',
                    'title' => __('remote.protocol'),
                    'options' => \App\Models\Remote::PROTOCOLS,
                    'optionsLocalization' => 'remote.protocols.',
                    'value' => isset($credential->remote) ? $credential->remote->protocol : '',
                ])
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                @include('parts.fields.text', [
                    'name' => 'login',
                    'title' => __('credentials.login'),
                    'value' => isset($credential) ? $credential->login : '',
                ])
            </div>
            <div class="col">
                @include('parts.fields.password', [
                    'title' => __('signin.Password'),
                    'value' => isset($credential) ? $credential->password : '',
                ])
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                @include('parts.fields.textarea', [
                    'name' => 'note',
                    'title' => __('credentials.note'),
                    'value' => isset($credential) ? $credential->note : '',
                ])
            </div>
        </div>
    </div>
    <input type="hidden" name="remote" value="1">
    <button type="submit" class="btn btn-primary">{{ __('global.submit') }}</button>
</form>
