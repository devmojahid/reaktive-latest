@props([
    'name',
    'label',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'help' => null,
    'rows' => 4,
    'maxlength' => null,
])

<div class="crancy__item-form--group">
    <label for="{{ $name }}" class="crancy__item-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
        @if ($help)
            <span data-toggle="tooltip" data-placement="top" class="fa fa-info-circle text--primary"
                title="{{ $help }}"></span>
        @endif
    </label>

    <div class="position-relative">
        <textarea id="{{ $name }}" name="{{ $name }}"
            class="crancy__item-input crancy__item-textarea @error($name) is-invalid @enderror"
            placeholder="{{ $placeholder ?? $label }}" rows="{{ $rows }}" {{ $maxlength ? "maxlength=$maxlength" : '' }}
            {{ $required ? 'required' : '' }}>{{ old($name, $value) }}</textarea>

        @if ($maxlength)
            <small class="character-count position-absolute bottom-0 end-0 pe-2 pb-1 text-muted">
                <span class="current">{{ strlen(old($name, $value) ?? '') }}</span>/{{ $maxlength }}
            </small>
        @endif
    </div>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<style>
    .crancy__item-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .character-count {
        font-size: 12px;
        pointer-events: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('textarea[maxlength]').forEach(function(textarea) {
            const counter = textarea.parentElement.querySelector('.character-count .current');
            if (counter) {
                textarea.addEventListener('input', function() {
                    counter.textContent = this.value.length;
                });
            }
        });
    });
</script>
