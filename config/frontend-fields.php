<?php

return [
    'field_types' => [
        'text' => [
            'component' => 'text-input',
            'view' => 'admin.frontend-management.fields.text',
            'validation' => 'string|max:255',
            'translatable' => true
        ],
        'textarea' => [
            'component' => 'textarea',
            'view' => 'admin.frontend-management.fields.textarea',
            'validation' => 'string|max:65535',
            'translatable' => true
        ],
        'email' => [
            'component' => 'email-input',
            'view' => 'admin.frontend-management.fields.email',
            'validation' => 'email',
            'translatable' => false
        ],
        'url' => [
            'component' => 'url-input',
            'view' => 'admin.frontend-management.fields.url',
            'validation' => 'url',
            'translatable' => false
        ],
        'number' => [
            'component' => 'number-input',
            'view' => 'admin.frontend-management.fields.number',
            'validation' => 'numeric',
            'translatable' => false
        ],
        'date' => [
            'component' => 'date-input',
            'view' => 'admin.frontend-management.fields.date',
            'validation' => 'date',
            'translatable' => false
        ],
        'time' => [
            'component' => 'time-input',
            'view' => 'admin.frontend-management.fields.time',
            'validation' => 'nullable|date_format:H:i',
            'translatable' => false
        ],
        'color' => [
            'component' => 'color-input',
            'view' => 'admin.frontend-management.fields.color',
            'validation' => 'string|max:9',
            'translatable' => false
        ],
        'image' => [
            'component' => 'image-input',
            'view' => 'admin.frontend-management.fields.image',
            'validation' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translatable' => false,
            'options' => [
                'dimensions' => true,
                'preview' => true,
                'crop' => true
            ]
        ],
        'file' => [
            'component' => 'file-input',
            'view' => 'admin.frontend-management.fields.file',
            'validation' => 'file|max:10240',
            'translatable' => false
        ],
        'select' => [
            'component' => 'select',
            'view' => 'admin.frontend-management.fields.select',
            'validation' => 'string',
            'translatable' => true,
            'options' => [
                'multiple' => false,
                'searchable' => false
            ]
        ],
        'select2' => [
            'component' => 'select2',
            'view' => 'admin.frontend-management.fields.select2',
            'validation' => 'string',
            'translatable' => true,
            'options' => [
                'multiple' => true,
                'searchable' => true,
                'taggable' => true
            ]
        ],
        'radio' => [
            'component' => 'radio-group',
            'view' => 'admin.frontend-management.fields.radio',
            'validation' => 'string',
            'translatable' => true
        ],
        'checkbox' => [
            'component' => 'checkbox-group',
            'view' => 'admin.frontend-management.fields.checkbox',
            'validation' => 'boolean',
            'translatable' => false
        ],
        'boolean' => [
            'component' => 'toggle-switch',
            'view' => 'admin.frontend-management.fields.boolean',
            'validation' => 'nullable|boolean',
            'translatable' => false
        ],
        'html' => [
            'component' => 'html-editor',
            'view' => 'admin.frontend-management.fields.html',
            'validation' => 'string',
            'translatable' => true,
            'options' => [
                'toolbar' => 'full',
                'height' => 300
            ]
        ],
        'range' => [
            'component' => 'range-slider',
            'view' => 'admin.frontend-management.fields.range',
            'validation' => 'nullable|numeric|between:0,100',
            'translatable' => false,
            'options' => [
                'min' => 0,
                'max' => 100,
                'step' => 1
            ]
        ],
        'repeater' => [
            'component' => 'repeater',
            'view' => 'admin.frontend-management.fields.repeater',
            'validation' => 'array',
            'translatable' => true,
            'options' => [
                'min_items' => 0,
                'max_items' => null,
                'sortable' => true
            ]
        ]
    ],
    
    'default_validations' => [
        'required' => 'required',
        'min' => 'min:2',
        'max' => 'max:255'
    ],
    
    'image_sizes' => [
        'thumb' => [150, 150],
        'medium' => [300, 300],
        'large' => [800, 600],
    ],
    
    'file_types' => [
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'video' => ['mp4', 'webm', 'ogg'],
        'audio' => ['mp3', 'wav', 'ogg'],
    ],
    
    'editor_config' => [
        'toolbar' => [
            'full' => [
                'heading',
                '|',
                'bold', 'italic', 'underline', 'strikethrough',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'link', 'blockQuote', 'imageUpload',
                '|',
                'alignment',
                '|',
                'undo', 'redo'
            ],
            'basic' => [
                'bold', 'italic',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'link'
            ],
        ],
    ],
]; 