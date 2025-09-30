@props(['name', 'label', 'value' => null, 'required' => false, 'help' => null, 'options' => [], 'inline' => false])

<div class="crancy__item-form--group frontend_management_system">
    <label class="crancy__item-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
        @if ($help)
            <span data-toggle="tooltip" data-placement="top" class="fa fa-info-circle text--primary"
                title="{{ $help }}"></span>
        @endif
    </label>

    <div class="radio-group {{ $inline ? 'd-flex gap-4' : '' }}">
        @foreach ($options as $optionValue => $optionLabel)
            <div class="crancy-radio {{ $inline ? 'mb-0' : 'mb-0' }}">
                <input type="radio" id="{{ $name }}_{{ $optionValue }}" name="{{ $name }}"
                    value="{{ $optionValue }}" class="@error($name) is-invalid @enderror"
                    {{ old($name, $value) == $optionValue ? 'checked' : '' }} {{ $required ? 'required' : '' }}>
                <label class="crancy-radio-label" for="{{ $name }}_{{ $optionValue }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    </div>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


 <style>
    
    .frontend_management_system .crancy-radio {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .frontend_management_system .crancy-radio input[type="radio"]:checked+.crancy-radio-label:before {
        border-color: var(--tg-primary);
    }

    .frontend_management_system .crancy-radio input[type="radio"] {
       width: 15px;
    }

    .frontend_management_system .crancy-radio-label {
        margin-bottom: 0px;
    }
</style>
