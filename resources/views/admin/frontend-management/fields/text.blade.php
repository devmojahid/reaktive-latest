@props(['name', 'label', 'value' => null, 'placeholder' => null, 'required' => false, 'help' => null])

<div class="crancy__item-form--group">
    <label for="{{ $name }}" class="crancy__item-label">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
        @if($help)
            <span data-toggle="tooltip" data-placement="top" class="fa fa-info-circle text--primary" title="{{ $help }}"></span>
        @endif
    </label>
    
    <input 
        type="text"
        id="{{ $name }}"
        name="{{ $name }}"
        class="crancy__item-input @error($name) is-invalid @enderror"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder ?? $label }}"
        @if($required) required @endif
    >
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div> 