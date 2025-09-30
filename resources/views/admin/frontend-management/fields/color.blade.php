@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
    'help' => null,
    'format' => 'hex', // hex, rgba, hsla
    'defaultColor' => '#000000',
    'showAlpha' => false,
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

    <div class="color-picker-wrapper">
        <input type="text" id="{{ $name }}" name="{{ $name }}"
            class="crancy__item-input color-picker @error($name) is-invalid @enderror"
            value="{{ old($name, $value ?? $defaultColor) }}"
            @if ($required) required @endif
            data-format="{{ $format }}"
            data-alpha="{{ $showAlpha ? 'true' : 'false' }}">
        <div class="color-preview" style="background-color: {{ old($name, $value ?? $defaultColor) }}"></div>
    </div>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if(!isset($colorPickerInitialized))
    @php($colorPickerInitialized = true)
    
    @push('style_section')
    <style>
        .color-picker-wrapper {
            display: flex;
            align-items: center;
        }
        
        .color-preview {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            margin-left: 10px;
            border: 1px solid #ddd;
        }
        
        .sp-replacer {
            border-color: #ddd;
            background: #fff;
            margin-left: 10px;
            padding: 6px;
        }
        
        .sp-preview {
            width: 30px;
            height: 30px;
            border-color: #ddd;
        }
        
        .sp-dd {
            padding: 0 5px;
            height: 30px;
            line-height: 30px;
            color: #666;
        }
        
        .sp-container {
            border-color: #ddd;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
    @endpush
    
    @push('js_section')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css">
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initColorPickers();
            
            function initColorPickers() {
                document.querySelectorAll('.color-picker').forEach(function(el) {
                    var format = el.getAttribute('data-format') || 'hex';
                    var showAlpha = el.getAttribute('data-alpha') === 'true';
                    var defaultColor = el.value || '#000000';
                    
                    var options = {
                        color: defaultColor,
                        showInput: true,
                        showInitial: true,
                        allowEmpty: true,
                        showAlpha: showAlpha,
                        disabled: false,
                        preferredFormat: format,
                        showPalette: true,
                        palette: [
                            ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                            ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                            ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                            ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                            ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                            ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                            ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                            ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
                        ],
                        change: function(color) {
                            var colorValue = color ? color.toString() : '';
                            var previewEl = el.closest('.color-picker-wrapper').querySelector('.color-preview');
                            if (previewEl) {
                                previewEl.style.backgroundColor = colorValue;
                            }
                            el.value = colorValue;
                        }
                    };
                    
                    $(el).spectrum(options);
                    
                    // Update the preview element when the value changes
                    $(el).on('change', function() {
                        var previewEl = this.closest('.color-picker-wrapper').querySelector('.color-preview');
                        if (previewEl) {
                            previewEl.style.backgroundColor = this.value;
                        }
                    });
                });
            }
        });
    </script>
    @endpush
@endif
