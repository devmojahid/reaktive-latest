<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Validator;

class FrontendFieldService
{
    protected $fieldTypes;
    
    public function __construct()
    {
        $this->fieldTypes = Config::get('frontend-fields.field_types', []);
    }
    
    /**
     * Render a field based on its type and configuration
     */
    public function renderField(string $type, string $name, $value = null, array $options = [], array $attributes = [])
    {
        if (!isset($this->fieldTypes[$type])) {
            throw new \InvalidArgumentException("Unsupported field type: {$type}");
        }
        
        $fieldConfig = $this->fieldTypes[$type];
        $viewName = $fieldConfig['view'];
        
        // Merge field options with defaults
        $mergedOptions = array_merge(
            $fieldConfig['options'] ?? [],
            $options
        );
        
        // Generate a label from the name if not provided
        $label = $attributes['label'] ?? Str::title(str_replace(['_', '-'], ' ', $name));
        
        return View::make($viewName, [
            'name' => $name,
            'label' => $label,
            'value' => $value,
            'options' => $mergedOptions,
            'attributes' => $attributes,
            'required' => $attributes['required'] ?? false,
            'help' => $attributes['help'] ?? null
        ])->render();
    }
    
    /**
     * Get validation rules for a field
     */
    public function getValidationRules(string $type, array $options = []): array
    {
        if (!isset($this->fieldTypes[$type])) {
            throw new \InvalidArgumentException("Unsupported field type: {$type}");
        }
        
        $fieldConfig = $this->fieldTypes[$type];
        $rules = [];
        
        // Add required rule if specified
        if ($options['required'] ?? false) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        // Add base validation rules
        if (is_string($fieldConfig['validation'])) {
            $rules = array_merge($rules, explode('|', $fieldConfig['validation']));
        } elseif (is_array($fieldConfig['validation'])) {
            $rules = array_merge($rules, $fieldConfig['validation']);
        }
        
        // Add min/max rules if specified
        if (isset($options['min'])) {
            $rules[] = 'min:' . $options['min'];
        }
        if (isset($options['max'])) {
            $rules[] = 'max:' . $options['max'];
        }
        
        // Add specific rules based on field type
        switch ($type) {
            case 'email':
                $rules[] = 'email:rfc,dns';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'image':
                $rules = array_merge($rules, [
                    'image',
                    'mimes:jpeg,png,jpg,gif,webp',
                    'max:2048' // 2MB
                ]);
                break;
            case 'file':
                $rules = array_merge($rules, [
                    'file',
                    'mimes:' . implode(',', Config::get('frontend-fields.file_types.document')),
                    'max:10240' // 10MB
                ]);
                break;
            case 'color':
                $rules[] = 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/';
                break;
            case 'number':
                $rules[] = 'numeric';
                if (isset($options['min_value'])) {
                    $rules[] = 'min:' . $options['min_value'];
                }
                if (isset($options['max_value'])) {
                    $rules[] = 'max:' . $options['max_value'];
                }
                break;
            case 'date':
                $rules[] = 'date';
                if (isset($options['min_date'])) {
                    $rules[] = 'after:' . $options['min_date'];
                }
                if (isset($options['max_date'])) {
                    $rules[] = 'before:' . $options['max_date'];
                }
                break;
            case 'repeater':
                if (isset($options['min_items'])) {
                    $rules[] = 'array|min:' . $options['min_items'];
                }
                if (isset($options['max_items'])) {
                    $rules[] = 'array|max:' . $options['max_items'];
                }
                break;
        }
        
        return array_unique($rules);
    }
    
    /**
     * Process field value before saving
     */
    public function processFieldValue($value, string $type, array $options = [])
    {
        if ($value === null) {
            return null;
        }
        
        switch ($type) {
            case 'image':
                return $this->processImageUpload($value, $options);
            case 'file':
                return $this->processFileUpload($value, $options);
            case 'repeater':
                return $this->processRepeaterValue($value, $options);
            case 'color':
                return $this->processColorValue($value, $options);
            case 'html':
                return $this->processHtmlValue($value, $options);
            case 'select':
            case 'select2':
                return $this->processSelectValue($value, $options);
            default:
                return $value;
        }
    }
    
    /**
     * Process image upload
     */
    protected function processImageUpload($image, array $options = [])
    {
        // If value is not a file (e.g., it's a path string), return it as is
        if (!is_object($image) || !method_exists($image, 'isValid')) {
            return $image; // Return existing path
        }

        if (!$image->isValid()) {
            return null;
        }
        
        // Store the image in the public directory
        $filename = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
        $path = 'uploads/website-images/' . $filename;
        
        // Create directory if it doesn't exist
        if (!file_exists(public_path('uploads/website-images'))) {
            mkdir(public_path('uploads/website-images'), 0755, true);
        }
        
        $image->move(public_path('uploads/website-images'), $filename);
        
        // Generate different sizes if specified
        if ($options['dimensions'] ?? false) {
            $sizes = Config::get('frontend-fields.image_sizes', []);
            foreach ($sizes as $size => [$width, $height]) {
                $resizedFilename = "{$size}_{$filename}";
                $resizedPath = "uploads/website-images/{$size}";
                
                // Create directory if it doesn't exist
                if (!file_exists(public_path($resizedPath))) {
                    mkdir(public_path($resizedPath), 0755, true);
                }
                
                try {
                    // Resize and save image
                    $img = Image::make(public_path($path));
                    $img->fit($width, $height)->save(public_path("{$resizedPath}/{$resizedFilename}"));
                } catch (\Exception $e) {
                    // Log error but continue
                    \Log::error('Failed to resize image: ' . $e->getMessage());
                }
            }
        }
        
        return $path;
    }
    
    /**
     * Process file upload
     */
    protected function processFileUpload($file, array $options = [])
    {
        // If value is not a file object, return it as is (existing path)
        if (!is_object($file) || !method_exists($file, 'isValid')) {
            return $file;
        }

        if (!$file->isValid()) {
            return null;
        }
        
        // Store the file in the public directory
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/website-files/' . $filename;
        
        // Create directory if it doesn't exist
        if (!file_exists(public_path('uploads/website-files'))) {
            mkdir(public_path('uploads/website-files'), 0755, true);
        }
        
        $file->move(public_path('uploads/website-files'), $filename);
        
        return $path;
    }
    
    /**
     * Process repeater values
     */
    protected function processRepeaterValue($value, array $options = [])
    {
        if (!is_array($value)) {
            return [];
        }
        
        $processedValues = [];
        foreach ($value as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            
            $processedItem = [];
            
            // Process each item based on field type even if field configs aren't available
            foreach ($item as $fieldName => $fieldValue) {
                if (isset($options['fields']) && is_array($options['fields']) && isset($options['fields'][$fieldName])) {
                    // If we have field config, use it
                    $field = $options['fields'][$fieldName];
                    $fieldType = $field['type'] ?? 'text';
                    $fieldOptions = $field['options'] ?? [];
                    
                    $processedItem[$fieldName] = $this->processFieldValue(
                        $fieldValue,
                        $fieldType,
                        $fieldOptions
                    );
                } else {
                    // If no field config, detect type and process accordingly
                    if (is_object($fieldValue) && method_exists($fieldValue, 'isValid')) {
                        // This is a file upload (likely an image)
                        $processedItem[$fieldName] = $this->processImageUpload($fieldValue, []);
                    } else {
                        // Default behavior for other types
                        $processedItem[$fieldName] = $fieldValue;
                    }
                }
            }
            
            $processedValues[$index] = $processedItem;
        }
        
        return array_values($processedValues); // Re-index array
    }
    
    /**
     * Process color value
     */
    protected function processColorValue($value, array $options = [])
    {
        if (empty($value)) {
            return $options['defaultColor'] ?? null;
        }
        
        // Ensure color value starts with #
        return '#' . ltrim($value, '#');
    }
    
    /**
     * Process HTML value
     */
    protected function processHtmlValue($value, array $options = [])
    {
        if (empty($value)) {
            return null;
        }
        
        // Sanitize HTML if needed
        return clean($value, [
            'HTML.Allowed' => 'p,b,i,u,strong,em,a[href|title],ul,ol,li,br,img[src|alt|title|width|height],h1,h2,h3,h4,h5,h6',
            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
        ]);
    }
    
    /**
     * Process select/multi-select value
     */
    protected function processSelectValue($value, array $options = [])
    {
        if ($options['multiple'] ?? false) {
            return is_array($value) ? array_values(array_filter($value)) : [];
        }
        
        return $value;
    }
    
    /**
     * Get available field types
     */
    public function getAvailableFieldTypes(): array
    {
        return array_keys($this->fieldTypes);
    }
    
    /**
     * Check if a field type is translatable
     */
    public function isFieldTranslatable(string $type): bool
    {
        return $this->fieldTypes[$type]['translatable'] ?? false;
    }
} 