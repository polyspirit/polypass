<form action="/credentials" method="POST" class="mt-2">
    @csrf

    <div class="mb-3">
        @include('parts.fields.text', ['name' => 'name', 'title' => __('credentials.name')])
    </div>
    <div class="mb-3">
        @include('parts.fields.text', ['name' => 'login', 'title' => __('credentials.login')])
    </div>
    <div class="mb-3">
        @include('parts.fields.password', ['title' => __('signin.Password')])
    </div>
    <div class="mb-3">
        @include('parts.fields.textarea', ['name' => 'note', 'title' => __('credentials.note')])
    </div>
    <button type="submit" class="btn btn-primary">{{__('global.submit') }}</button>
</form>