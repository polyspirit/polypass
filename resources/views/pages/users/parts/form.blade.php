<form action="/users/{{ $user->id }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'name',
            'title' => __('users.name'),
            'value' => $user->name,
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.text', [
            'name' => 'email',
            'type' => 'email',
            'title' => __('users.email'),
            'value' => $user->email,
        ])
    </div>

    @if (!config('app.demo') && empty($disabled))
    <div class="mb-3">
        @include('parts.fields.password', [
            'title' => __('signin.new_password'),
        ])
    </div>
    <div class="mb-3">
        @include('parts.fields.password', [
            'name' => 'password_confirmation',
            'title' => __('signin.confirm_password'),
        ])
    </div>
    @endif

    @can('users-change-roles-any')
        <div class="mb-3">
            @php
                $rolesCollection = collect(config('roles.roles'));
                $roles = $rolesCollection->keys()->all();
            @endphp
            @include('parts.fields.select', [
                'name' => 'role',
                'title' => __('users.role'),
                'options' => $roles,
                'optionsLocalization' => 'roles.',
                'value' => $user->getRoleNames()->first(),
            ])
        </div>
    @endcan

    @can('update', $user)
        @if(empty($disabled))
            <button type="submit" class="btn btn-primary">{{ __('global.update') }}</button>
        @else
            <a href="/users/{{ $user->id }}/edit" class="btn btn-primary">{{ __('global.edit') }}</a>
        @endif
    @endcan
</form>
