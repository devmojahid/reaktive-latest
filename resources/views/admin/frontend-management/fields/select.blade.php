@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
    'help' => null,
    'options' => [],
    'placeholder' => null,
    'multiple' => false
])

<div class="crancy__item-form--group">
    <label for="{{ $name }}" class="crancy__item-label">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
        @if($help)
            <span data-toggle="tooltip" data-placement="top" class="fa fa-info-circle text--primary" title="{{ $help }}"></span>
        @endif
    </label>
    
    <select
        id="{{ $name }}"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        class="crancy__item-select @error($name) is-invalid @enderror"
        {{ $multiple ? 'multiple' : '' }}
        {{ $required ? 'required' : '' }}
    >
        @if(!$multiple && $placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $optionValue => $optionLabel)
            <option 
                value="{{ $optionValue }}"
                {{ $multiple 
                    ? (is_array($value) && in_array($optionValue, $value) ? 'selected' : '')
                    : ($value == $optionValue ? 'selected' : '') 
                }}
            >
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<style>
.crancy__item-select {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: var(--tg-text-color);
    background-color: var(--tg-white);
    background-clip: padding-box;
    border: 1px solid var(--tg-border-1);
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

.crancy__item-select:focus {
    border-color: var(--tg-primary);
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(var(--tg-primary-rgb), 0.25);
}

.crancy__item-select[multiple] {
    padding-right: 0.75rem;
    background-image: none;
}

.crancy__item-select option {
    padding: 0.5rem;
}

.crancy__item-select option:checked {
    background-color: var(--tg-primary);
    color: white;
}

.crancy__item-select.is-invalid {
    border-color: var(--tg-error);
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.crancy__item-select.is-invalid:focus {
    border-color: var(--tg-error);
    box-shadow: 0 0 0 0.25rem rgba(var(--tg-error-rgb), 0.25);
}
</style>
