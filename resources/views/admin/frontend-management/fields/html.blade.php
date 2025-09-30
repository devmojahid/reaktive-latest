@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
    'help' => null,
    'height' => 300,
    'toolbar' => 'full',
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

    <textarea id="{{ $name }}" name="{{ $name }}" class="html-editor @error($name) is-invalid @enderror"
        data-height="{{ $height }}" data-toolbar="{{ $toolbar }}" {{ $required ? 'required' : '' }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>


<link href="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/skins/ui/oxide/skin.min.css" rel="stylesheet">
<link href="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/skins/content/default/content.min.css" rel="stylesheet">


<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define toolbar configurations
        const toolbarConfigs = {
            'full': [
                'undo redo | formatselect | bold italic backcolor | \
            alignleft aligncenter alignright alignjustify | \
            bullist numlist outdent indent | removeformat | help'
            ],
            'basic': [
                'bold italic | alignleft aligncenter alignright | bullist numlist'
            ],
            'minimal': [
                'bold italic'
            ]
        };

        // Initialize all HTML editors on the page
        const editorInitPromises = [];

        document.querySelectorAll('.html-editor').forEach(function(editor) {
            const toolbar = editor.dataset.toolbar || 'basic';
            const height = parseInt(editor.dataset.height) || 300;

            // Create a promise for this editor's initialization
            const editorPromise = new Promise((resolve) => {
                const initConfig = {
                    target: editor,
                    height: height,
                    menubar: toolbar === 'full',
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar: toolbarConfigs[toolbar] || toolbarConfigs['basic'],
                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
                    branding: false,
                    promotion: false,
                    paste_data_images: true,
                    relative_urls: false,
                    convert_urls: false,
                    entity_encoding: 'raw',
                    verify_html: false,
                    valid_elements: '*[*]',
                    file_picker_types: 'image',
                    images_upload_handler: function(blobInfo, success, failure) {
                        const formData = new FormData();
                        formData.append('image', blobInfo.blob(), blobInfo.filename());

                        fetch('/admin/upload-image', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.url) {
                                    success(result.url);
                                } else {
                                    failure('Image upload failed');
                                }
                            })
                            .catch(error => {
                                console.error('Error uploading image:', error);
                                failure('Image upload failed');
                            });
                    },
                    setup: function(editor) {
                        editor.on('init', function() {
                            // Mark this editor as initialized
                            resolve();
                        });
                    }
                };

                // Initialize TinyMCE
                tinymce.init(initConfig);
            });

            editorInitPromises.push(editorPromise);
        });

        // Wait for all editors to be initialized
        Promise.all(editorInitPromises)
            .then(() => {
                console.log('All HTML editors initialized successfully');
            })
            .catch(error => {
                console.error('Error initializing HTML editors:', error);
            });
    });
</script>
