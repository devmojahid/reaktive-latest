@props([
    'name',
    'label',
    'value' => null,
    'options' => [],
    'multiple' => true,
    'searchable' => true,
    'taggable' => false,
    'placeholder' => null,
    'required' => false,
    'help' => null,
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

    <select id="{{ $name }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        class="form-control select2-field @error($name) is-invalid @enderror" {{ $multiple ? 'multiple' : '' }}
        {{ $required ? 'required' : '' }} data-searchable="{{ $searchable ? 'true' : 'false' }}"
        data-taggable="{{ $taggable ? 'true' : 'false' }}" data-placeholder="{{ $placeholder ?? $label }}">
        @if (!$multiple)
            <option value="">{{ __('Select...') }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}"
                {{ $multiple
                    ? (is_array($value) && in_array($optionValue, $value)
                        ? 'selected'
                        : '')
                    : ($value == $optionValue
                        ? 'selected'
                        : '') }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border-color: var(--tg-border-1);
        min-height: 45px;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--tg-primary);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--tg-primary);
        border: none;
        color: white;
        padding: 2px 8px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.select2-field').each(function() {
            const $select = $(this);
            const config = {
                placeholder: $select.data('placeholder'),
                allowClear: !$select.prop('required'),
            };

            if ($select.data('searchable')) {
                config.minimumResultsForSearch = 0;
            } else {
                config.minimumResultsForSearch = Infinity;
            }

            if ($select.data('taggable')) {
                config.tags = true;
                config.tokenSeparators = [',', ' '];
            }

            $select.select2(config);
        });
    });
</script>
