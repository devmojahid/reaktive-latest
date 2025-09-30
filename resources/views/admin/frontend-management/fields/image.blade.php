@props(['name', 'label', 'value' => null, 'required' => false, 'help' => null, 'options' => []])

<div class="crancy__item-form--group mb-4">
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

    <div class="crancy-product-card__upload crancy-product-card__upload--border">
        <input type="file" id="{{ $name }}" name="{{ $name }}"
            class="custom-file-input d-none @error($name) is-invalid @enderror"
            accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(event, '{{ $name }}')"
            {{ $required && !$value ? 'required' : '' }}>

        <label class="crancy-image-video-upload__label" for="{{ $name }}">
            <img id="view_img_{{ $name }}"
                src="{{ $value ? asset($value) : asset('backend/img/placeholder-image.jpg') }}"
                alt="{{ $label }}">
            <h4 class="crancy-image-video-upload__title">
                {{ __('translate.Click here to') }}
                <span class="crancy-primary-color">{{ __('translate.Choose File') }}</span>
                {{ __('translate.and upload') }}
            </h4>
        </label>

        @if ($value)
            <input type="hidden" name="{{ $name }}_existing" value="{{ $value }}">
            <div class="d-flex justify-content-end mt-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="resetImage('{{ $name }}')">
                    <i class="fas fa-times"></i> {{ __('translate.Remove') }}
                </button>
            </div>
        @endif
    </div>

    @if (isset($options['dimensions']) || isset($options['size']))
        <small class="form-text text-muted">
            {{ __('translate.Recommended image size') }}:
            @if(isset($options['size']))
                {{ $options['size'] }}
            @elseif(isset($options['dimensions']))
                @foreach (config('frontend-fields.image_sizes') as $size => [$width, $height])
                    <br>{{ ucfirst($size) }}: {{ $width }}x{{ $height }}px
                @endforeach
            @endif
        </small>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>


<style>
    .crancy-product-card__upload {
        border: 2px dashed var(--tg-border-1);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .crancy-product-card__upload:hover {
        border-color: var(--tg-primary);
    }

    .crancy-product-card__upload img {
        max-width: 100%;
        max-height: 200px;
        margin-bottom: 15px;
        border-radius: 4px;
        object-fit: contain;
    }

    .crancy-image-video-upload__title {
        font-size: 14px;
        margin: 0;
        color: var(--tg-text-color);
    }

    .crancy-product-card__upload.is-invalid {
        border-color: var(--tg-error);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add drag and drop support
        document.querySelectorAll('.crancy-product-card__upload').forEach(function(dropZone) {
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-primary');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-primary');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-primary');

                const input = this.querySelector('input[type="file"]');
                const files = e.dataTransfer.files;

                if (files.length > 0 && files[0].type.startsWith('image/')) {
                    input.files = files;
                    const event = new Event('change', {
                        bubbles: true
                    });
                    input.dispatchEvent(event);
                }
            });
        });
    });

    function previewImage(event, target_view_id) {
        if (!event || !event.target || !event.target.files || !event.target.files[0]) {
            return;
        }

        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById(`view_img_${target_view_id}`);
            if (output) {
                output.src = reader.result;
            }
        }
        reader.readAsDataURL(event.target.files[0]);

        // If we have a hidden input for existing image, mark it for deletion
        const existingInput = document.querySelector(`input[name="${target_view_id}_existing"]`);
        if (existingInput) {
            existingInput.value = '';
        }
    }

    function resetImage(target_view_id) {
        // Clear the file input
        const input = document.getElementById(target_view_id);
        if (input) {
            input.value = '';
        }

        // Reset the preview image to placeholder
        const output = document.getElementById(`view_img_${target_view_id}`);
        if (output) {
            output.src = '{{ asset('backend/img/placeholder-image.jpg') }}';
        }

        // Mark existing image for deletion
        const existingInput = document.querySelector(`input[name="${target_view_id}_existing"]`);
        if (existingInput) {
            existingInput.value = '';
        }
    }
</script>
