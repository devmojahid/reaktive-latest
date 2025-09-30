@props([
    'name',
    'label',
    'value' => [],
    'required' => false,
    'help' => null,
    'fields' => [],
    'min' => 0,
    'max' => null,
    'sortable' => true
])

<div class="crancy__item-form--group repeater-field" 
    data-min="{{ $min }}" 
    data-max="{{ $max }}"
    data-name="{{ $name }}"
>
    <label class="crancy__item-label">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
        @if($help)
            <span data-toggle="tooltip" data-placement="top" class="fa fa-info-circle text--primary" title="{{ $help }}"></span>
        @endif
    </label>

    <div class="repeater-items">
        @php
            // Ensure value is an array
            if(!is_array($value)) {
                $value = [];
            }
            
            // If empty, create one default item
            if(empty($value)) {
                $value = [[]];
            }
        @endphp
        
        @foreach($value as $index => $item)
            <div class="repeater-item card mb-3" data-index="{{ $index }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="repeater-title">{{ __('translate.Item') }} #{{ $index + 1 }}</span>
                    <div class="repeater-actions">
                        @if($sortable)
                            <button type="button" class="btn btn-sm btn-light handle me-2" title="{{ __('translate.Drag to reorder') }}">
                                <i class="fas fa-grip-vertical"></i>
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-danger remove-item" title="{{ __('translate.Remove item') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($fields as $fieldName => $field)
                        @php
                            // Get the field value or null if not set
                            $fieldValue = $item[$fieldName] ?? null;
                            
                            // Determine field type, default to text if not specified
                            $fieldType = $field['type'] ?? 'text';
                            
                            // Prepare field properties
                            $fieldProps = [
                                'name' => "{$name}[{$index}][{$fieldName}]",
                                'label' => $field['label'] ?? str_replace('_', ' ', ucfirst($fieldName)),
                                'value' => $fieldValue,
                                'required' => $field['required'] ?? false,
                                'help' => $field['help'] ?? null
                            ];
                            
                            // Add options if they exist
                            if (isset($field['options'])) {
                                $fieldProps['options'] = $field['options'];
                            }
                            
                            // Add fields for nested repeaters
                            if (isset($field['fields'])) {
                                $fieldProps['fields'] = $field['fields'];
                            }
                        @endphp
                        
                        @include("admin.frontend-management.fields.{$fieldType}", $fieldProps)
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-end mt-3">
        <button type="button" class="crancy-btn crancy-btn--secondary add-item">
            <i class="fas fa-plus me-2"></i>{{ __('translate.Add Item') }}
        </button>
    </div>
</div>

<style>
.repeater-field .repeater-item {
    position: relative;
    border: 1px solid var(--tg-border-1);
    border-radius: 8px;
    background: var(--tg-white);
    transition: all 0.3s ease;
}

.repeater-field .repeater-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.repeater-field .repeater-item.dragging {
    opacity: 0.5;
    background: var(--tg-bg-1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.repeater-field .card-header {
    background: var(--tg-bg-1);
    border-bottom: 1px solid var(--tg-border-1);
    padding: 1rem;
    border-radius: 7px 7px 0 0;
}

.repeater-field .handle {
    cursor: move;
    transition: transform 0.2s;
}

.repeater-field .handle:hover {
    transform: scale(1.1);
}

.repeater-field .card-body {
    padding: 1.5rem;
}

.repeater-title {
    font-weight: 600;
    color: var(--tg-heading-color);
}

.repeater-actions .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.repeater-actions .btn:hover {
    transform: scale(1.1);
}

.repeater-actions .btn-danger:hover {
    background-color: var(--tg-error);
    border-color: var(--tg-error);
}

.add-item {
    transition: all 0.2s;
}

.add-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Animations */
.repeater-item {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-10px);
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.repeater-field').forEach(function(repeater) {
        const itemsContainer = repeater.querySelector('.repeater-items');
        const addButton = repeater.querySelector('.add-item');
        const min = parseInt(repeater.dataset.min) || 0;
        const max = parseInt(repeater.dataset.max) || Infinity;
        const name = repeater.dataset.name;
        
        // Initialize sortable if enabled
        if (repeater.querySelector('.handle')) {
            new Sortable(itemsContainer, {
                handle: '.handle',
                animation: 150,
                onStart: function(evt) {
                    evt.item.classList.add('dragging');
                },
                onEnd: function(evt) {
                    evt.item.classList.remove('dragging');
                    updateIndexes(repeater);
                }
            });
        }
        
        // Add new item
        addButton.addEventListener('click', function() {
            const items = repeater.querySelectorAll('.repeater-item');
            if (items.length >= max) {
                alert(`Maximum ${max} items allowed`);
                return;
            }
            
            const index = items.length;
            
            // Create a new item based on the first one
            const firstItem = repeater.querySelector('.repeater-item');
            if (firstItem) {
                const newItem = firstItem.cloneNode(true);
                
                // Update index and title
                newItem.setAttribute('data-index', index);
                newItem.querySelector('.repeater-title').textContent = `Item #${index + 1}`;
                
                // Reset all input values
                newItem.querySelectorAll('input, textarea, select').forEach(input => {
                    // Store the original ID before changing it
                    const originalId = input.id;
                    
                    // Update name attribute
                    const newName = input.name.replace(/\[\d+\]/, `[${index}]`);
                    input.name = newName;
                    
                    // Reset values
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = false;
                    } else if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = '';
                    }
                    
                    // Update IDs if present
                    if (input.id) {
                        // Replace the index in array-style IDs (e.g., name[0][field] -> name[1][field])
                        const newId = input.id.replace(/\[(\d+)\]/, `[${index}]`);
                        input.id = newId;
                        
                        // Find and update any labels that reference this input using the original ID
                        const labels = newItem.querySelectorAll(`label[for="${originalId}"]`);
                        labels.forEach(label => {
                            label.setAttribute('for', newId);
                        });
                    }
                });
                
                // Reset image previews
                newItem.querySelectorAll('img[id^="view_img_"]').forEach(img => {
                    // Update image ID with new index
                    const imgId = img.id;
                    const newImgId = imgId.replace(/\[\d+\]/, `[${index}]`);
                    img.id = newImgId;
                    img.src = '{{ asset('backend/img/placeholder-image.jpg') }}';
                });
                
                // Update image field onchange and reset button onclick attributes
                newItem.querySelectorAll('input[type="file"]').forEach(fileInput => {
                    // Update onchange attribute for preview function
                    const onchangeAttr = fileInput.getAttribute('onchange');
                    if (onchangeAttr && onchangeAttr.includes('previewImage')) {
                        const newOnchange = onchangeAttr.replace(/\[\d+\]/, `[${index}]`);
                        fileInput.setAttribute('onchange', newOnchange);
                    }
                });
                
                // Update reset buttons
                newItem.querySelectorAll('button[onclick*="resetImage"]').forEach(button => {
                    const onclickAttr = button.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes('resetImage')) {
                        const newOnclick = onclickAttr.replace(/\[\d+\]/, `[${index}]`);
                        button.setAttribute('onclick', newOnclick);
                    }
                });
                
                // Initialize the new item
                initializeItem(newItem);
                
                // Add to container
                itemsContainer.appendChild(newItem);
                
                // Scroll to the new item
                newItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        // Initialize existing items
        repeater.querySelectorAll('.repeater-item').forEach(initializeItem);
        
        function initializeItem(item) {
            const removeBtn = item.querySelector('.remove-item');
            removeBtn.addEventListener('click', function() {
                const items = repeater.querySelectorAll('.repeater-item');
                if (items.length <= min) {
                    alert(`Minimum ${min} items required`);
                    return;
                }
                
                // Animate removal
                item.style.animation = 'slideUp 0.3s ease-out forwards';
                setTimeout(() => {
                    item.remove();
                    updateIndexes(repeater);
                }, 300);
            });
        }
        
        function updateIndexes(repeater) {
            repeater.querySelectorAll('.repeater-item').forEach((item, idx) => {
                item.dataset.index = idx;
                item.querySelector('.repeater-title').textContent = `Item #${idx + 1}`;
                
                // Update all field names inside this item
                item.querySelectorAll('[name]').forEach(field => {
                    // Store original ID before changing for label reference
                    const originalId = field.id;
                    
                    // Update name attribute
                    const oldName = field.getAttribute('name');
                    const newName = oldName.replace(/\[\d+\]/, `[${idx}]`);
                    field.setAttribute('name', newName);
                    
                    // Update id if it exists
                    if (field.id) {
                        const newId = field.id.replace(/\[\d+\]/, `[${idx}]`);
                        field.id = newId;
                        
                        // Update any labels that point to this field using the original ID
                        if (originalId) {
                            const labels = item.querySelectorAll(`label[for="${originalId}"]`);
                            labels.forEach(label => {
                                label.setAttribute('for', newId);
                            });
                        }
                        
                        // Update onchange attribute for file inputs
                        if (field.type === 'file') {
                            const onchangeAttr = field.getAttribute('onchange');
                            if (onchangeAttr && onchangeAttr.includes('previewImage')) {
                                const newOnchange = onchangeAttr.replace(/\[\d+\]/, `[${idx}]`);
                                field.setAttribute('onchange', newOnchange);
                            }
                        }
                    }
                });
                
                // Update image IDs
                item.querySelectorAll('img[id^="view_img_"]').forEach(img => {
                    const imgId = img.id;
                    const newImgId = imgId.replace(/\[\d+\]/, `[${idx}]`);
                    img.id = newImgId;
                });
                
                // Update reset buttons
                item.querySelectorAll('button[onclick*="resetImage"]').forEach(button => {
                    const onclickAttr = button.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes('resetImage')) {
                        const newOnclick = onclickAttr.replace(/\[\d+\]/, `[${idx}]`);
                        button.setAttribute('onclick', newOnclick);
                    }
                });
            });
        }
    });
});
</script>
