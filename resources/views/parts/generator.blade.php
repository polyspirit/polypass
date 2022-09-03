<div class="container text-center">
    <div class="row mb-4">
        <div class="col">
            @include('parts.fields.text', [
                'name' => 'generated-password',
                'disabled' => 'disabled',
            ])
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <button class="btn btn-primary w-100">
                {{ __('global.update') }}
            </button>
        </div>
        <div class="col">
            <button class="btn btn-primary w-100">
                {{ __('global.copy') }}
            </button>
        </div>
    </div>
    <div class="row text-start">
        <div class="col">
            <h4>{{ __('generator.symbols-amount') }}: <span class="badge bg-primary">8</span></h4>
            <input type="range" class="form-range" min="4" max="64" value="12" id="customRange2">
        </div>
        <div class="col">
            <h4>{{ __('global.include') }}:</h4>
            <div class="d-flex flex-wrap gap-3">
                @include('parts.fields.checkbox', [
                    'name' => 'digits',
                    'title' => __('generator.digits'),
                    'value' => 1,
                ])
                @include('parts.fields.checkbox', [
                    'name' => 'letters-lower',
                    'title' => __('generator.letters-lower'),
                    'value' => 1,
                ])
                @include('parts.fields.checkbox', [
                    'name' => 'letters-upper',
                    'title' => __('generator.letters-upper'),
                    'value' => 1,
                ])
                @include('parts.fields.checkbox', [
                    'name' => 'symbols',
                    'title' => __('generator.symbols'),
                    'value' => 1,
                ])
                @include('parts.fields.checkbox', [
                    'name' => 'symbols-extended',
                    'title' => __('generator.symbols-extended'),
                    'value' => 0,
                ])
            </div>
        </div>
    </div>
</div>
