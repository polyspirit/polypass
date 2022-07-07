@extends('layouts.app')

@section('content')

@can('create', App\Models\Credential::class)
<ul class="nav nav-tabs" id="credentialTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="base-tab" data-bs-toggle="tab" data-bs-target="#base" type="button" role="tab" aria-controls="base" aria-selected="true">
            <i class="fa-solid fa-key"></i>
            {{__('credentials.detail') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="remote-tab" data-bs-toggle="tab" data-bs-target="#remote" type="button" role="tab" aria-controls="remote" aria-selected="false">
            <i class="fa-solid fa-server"></i>
            {{__('credentials.remote') }}
        </button>
    </li>
</ul>
<div class="tab-content" id="credentialTabsContent">
    <div class="tab-pane fade show active" id="base" role="tabpanel" aria-labelledby="base-tab">
        @include('pages.credentials.parts.forms.base')
    </div>
    <div class="tab-pane fade" id="remote" role="tabpanel" aria-labelledby="remote-tab">
        @include('pages.credentials.parts.forms.remote')
    </div>
</div>

@else
<h3>{{__('credentials.error-create') }}</h3>
@endcan

@endsection