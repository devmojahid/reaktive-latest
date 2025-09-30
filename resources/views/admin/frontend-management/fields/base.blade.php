@props([
    'name',
    'label' => null,
    'value' => null,
    'help' => null,
    'error' => null,
    'required' => false,
    'translatable' => false,
    'attributes' => [],
    'wrapperClass' => 'crancy__item-form--group',
    'labelClass' => 'crancy__item-label',
    'inputClass' => 'crancy__item-input'
])

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $name }}" class="{{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
            @if($translatable)
                <span class="badge bg-info">{{ __('translate.Translatable') }}</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if($help)
        <small class="form-text text-muted">{{ $help }}</small>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div> 