@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Edit Menu') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Edit Menu') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Content') }} >> {{ __('translate.Menus') }} >>
        {{ __('translate.Edit') }}</p>
@endsection

@push('style_section')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css">
    <style>
        .dd {
            max-width: 100%;
        }

        .dd-item {
            padding: 10px;
        }

        .dd-empty,
        .dd-placeholder {
            height: 40px;
        }

        .dd-handle {
            background: #f7f7f7;
            border: 1px solid #ddd;
            padding: 5px 10px;
            height: auto;
            min-height: 30px;
            cursor: move;
            width: 30px;
            text-align: center;
            border-radius: 3px;
        }

        .dd-handle:hover {
            background: #f0f0f0;
        }

        .menu-item-actions {
            margin-left: 10px;
            display: flex;
            gap: 5px;
        }

        #saveMenuStructure {
            width: 50%;
        }

        .btn-back-list {
            display: block;
            margin-top: 5px;
        }

        .menu-item-title {
            font-weight: bold;
            font-size: 14px;
        }

        .nestable-menu {
            margin-top: 20px;
        }

        .menu-item-content {
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 3px;
        }

        .d-flex.align-items-center {
            background-color: #fff;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #eee;
        }

    </style>
@endpush

@section('body-content')
    <section class="crancy-adashboard crancy-show mg-top-30">
        <div class="container container__bscreen">
            <div class="crancy-product-card">
                <div class="row">
                    <div class="col-12">
                        <div class="crancy-body">
                            <div class="crancy-dsinner">
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- Menu Settings Form -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5>{{ __('translate.Menu Settings') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('translate.Name') }} *</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ $menu->name }}" required>
                                                        @error('name')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('translate.Description') }}</label>
                                                        <textarea name="description" class="form-control" rows="3">{{ $menu->description }}</textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('translate.Menu Location') }}</label>
                                                        <select name="location" class="form-select">
                                                            <option value="">{{ __('translate.None') }}</option>
                                                            @foreach ($locations as $location => $details)
                                                                <option value="{{ $location }}"
                                                                    {{ $menu->location === $location ? 'selected' : '' }}>
                                                                    {{ $details['name'] }} - {{ $details['description'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="status"
                                                                id="statusSwitch" {{ $menu->status ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="statusSwitch">{{ __('translate.Active') }}</label>
                                                        </div>
                                                    </div>

                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('translate.Update Menu') }}</button>
                                                    <a href="{{ route('admin.menus.index') }}"
                                                        class="btn btn-secondary btn-back-list">{{ __('translate.Back to List') }}</a>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Add Menu Item Form -->
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('translate.Add Menu Item') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('admin.menus.add-item', $menu->id) }}"
                                                    method="POST" class="add-menu-item-form">
                                                    @csrf

                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('translate.Title') }} *</label>
                                                        <input type="text" name="title" class="form-control" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('translate.URL') }}</label>
                                                        <input type="text" name="url" class="form-control"
                                                            placeholder="e.g., /about-us">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('translate.Item Type') }}</label>
                                                        <select name="type" class="form-select" id="menuItemType">
                                                            <option value="custom">{{ __('translate.Custom Link') }}
                                                            </option>
                                                            <option value="page">{{ __('translate.Page') }}</option>
                                                            <option value="category">{{ __('translate.Category') }}
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3" id="typeIdField" style="display: none;">
                                                        <label class="form-label">{{ __('translate.Select Item') }}</label>
                                                        <select name="type_id" class="form-select">
                                                            <option value="">{{ __('translate.Select Item') }}
                                                            </option>
                                                            <!-- Dynamic options will be populated by JS -->
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('translate.Icon Class') }}</label>
                                                        <input type="text" name="icon_class" class="form-control"
                                                            placeholder="e.g., fas fa-home">
                                                        <small
                                                            class="text-muted">{{ __('translate.FontAwesome or other icon classes') }}</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('translate.Target') }}</label>
                                                        <select name="target" class="form-select">
                                                            <option value="_self">{{ __('translate.Same Window') }}
                                                            </option>
                                                            <option value="_blank">{{ __('translate.New Window') }}
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('translate.Add Item') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <!-- Menu Structure -->
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5>{{ __('translate.Menu Structure') }}</h5>
                                                <button id="saveMenuStructure"
                                                    class="btn btn-primary">{{ __('translate.Save Menu Structure') }}</button>
                                            </div>
                                            <div class="card-body">
                                                @if (count($menuItems) > 0)
                                                    <p>{{ __('translate.Drag and drop items to reorder. Click on an item to edit its properties.') }}
                                                    </p>

                                                    <div class="nestable-menu">
                                                        <div class="dd" id="nestable">
                                                            <ol class="dd-list">
                                                                @foreach ($menuItems as $item)
                                                                    <li class="dd-item" data-id="{{ $item->id }}">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="dd-handle me-2">
                                                                                <i class="fas fa-grip-vertical"></i>
                                                                            </div>
                                                                            <div class="menu-item-content flex-grow-1">
                                                                                <span class="menu-item-title">
                                                                                    @if ($item->icon_class)
                                                                                        <i
                                                                                            class="{{ $item->icon_class }}"></i>
                                                                                    @endif
                                                                                    {{ $item->title }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="menu-item-actions">
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-info edit-menu-item"
                                                                                    data-id="{{ $item->id }}"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#editItemModal">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </button>
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-danger delete-menu-item"
                                                                                    data-id="{{ $item->id }}">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        @if (count($item->children) > 0)
                                                                            <ol class="dd-list">
                                                                                @foreach ($item->children as $child)
                                                                                    <li class="dd-item"
                                                                                        data-id="{{ $child->id }}">
                                                                                        <div
                                                                                            class="d-flex align-items-center">
                                                                                            <div class="dd-handle me-2">
                                                                                                <i
                                                                                                    class="fas fa-grip-vertical"></i>
                                                                                            </div>
                                                                                            <div
                                                                                                class="menu-item-content flex-grow-1">
                                                                                                <span
                                                                                                    class="menu-item-title">
                                                                                                    @if ($child->icon_class)
                                                                                                        <i
                                                                                                            class="{{ $child->icon_class }}"></i>
                                                                                                    @endif
                                                                                                    {{ $child->title }}
                                                                                                </span>
                                                                                            </div>
                                                                                            <div class="menu-item-actions">
                                                                                                <button type="button"
                                                                                                    class="btn btn-sm btn-info edit-menu-item"
                                                                                                    data-id="{{ $child->id }}"
                                                                                                    data-bs-toggle="modal"
                                                                                                    data-bs-target="#editItemModal">
                                                                                                    <i
                                                                                                        class="fas fa-edit"></i>
                                                                                                </button>
                                                                                                <button type="button"
                                                                                                    class="btn btn-sm btn-danger delete-menu-item"
                                                                                                    data-id="{{ $child->id }}">
                                                                                                    <i
                                                                                                        class="fas fa-trash"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ol>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ol>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        {{ __('translate.No menu items found. Add your first menu item using the form.') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Menu Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('translate.Edit Menu Item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm" action="" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">{{ __('translate.Title') }} *</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('translate.URL') }}</label>
                            <input type="text" name="url" id="edit_url" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('translate.CSS Class') }}</label>
                            <input type="text" name="css_class" id="edit_css_class" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('translate.Icon Class') }}</label>
                            <input type="text" name="icon_class" id="edit_icon_class" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('translate.Target') }}</label>
                            <select name="target" id="edit_target" class="form-select">
                                <option value="_self">{{ __('translate.Same Window') }}</option>
                                <option value="_blank">{{ __('translate.New Window') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="edit_status">
                                <label class="form-check-label" for="edit_status">{{ __('translate.Active') }}</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                    <button type="button" class="btn btn-primary"
                        id="saveMenuItem">{{ __('translate.Save Changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('translate.Delete Menu Item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('translate.Are you sure you want to delete this menu item? All sub-items will also be deleted.') }}
                    </p>
                </div>
                <div class="modal-footer">
                    <form id="deleteItemForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('translate.Delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js_section')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
    <script>
        $(function() {
            // Initialize Nestable with the handle option
            var updateOutput = function(e) {
                var list = e.length ? e : $(e.target);
                if (window.JSON) {
                    // Get the Nestable list data
                    var data = window.JSON.stringify(list.nestable('serialize'));
                    console.log(data);
                }
            };

            $('#nestable').nestable({
                group: 1,
                maxDepth: 2,
                handleClass: 'dd-handle' // Specify that only elements with dd-handle class are draggable
            }).on('change', updateOutput);

            // Menu Item Type Change
            $('#menuItemType').on('change', function() {
                var type = $(this).val();
                if (type !== 'custom') {
                    $('#typeIdField').show();
                    // You can load dynamic options based on the selected type
                } else {
                    $('#typeIdField').hide();
                }
            });

            // Edit Menu Item - ensure event delegation for dynamically added elements
            $(document).on('click', '.edit-menu-item', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling to parent elements

                var id = $(this).data('id');

                // Fetch menu item data via AJAX
                $.ajax({
                    url: '{{ url('admin/menu-items') }}/' + id + '/edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Populate the form with the menu item data
                        $('#edit_title').val(data.title);
                        $('#edit_url').val(data.url);
                        $('#edit_css_class').val(data.css_class);
                        $('#edit_icon_class').val(data.icon_class);
                        $('#edit_target').val(data.target);
                        $('#edit_status').prop('checked', data.status == 1);

                        // Set the form action URL
                        $('#editItemForm').attr('action', '{{ url('admin/menu-items') }}/' +
                            id);

                        // Show modal
                        $('#editItemModal').modal('show');
                    },
                    error: function() {
                        alert('{{ __('translate.Error fetching menu item data') }}');
                    }
                });
            });

            // Save Menu Item (Submit form)
            $('#saveMenuItem').on('click', function() {
                $('#editItemForm').submit();
            });

            // Delete Menu Item - ensure event delegation
            $(document).on('click', '.delete-menu-item', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling

                var id = $(this).data('id');
                $('#deleteItemForm').attr('action', '{{ url('admin/menu-items') }}/' + id);
                $('#deleteItemModal').modal('show');
            });

            // Save Menu Structure
            $('#saveMenuStructure').on('click', function() {
                var data = $('#nestable').nestable('serialize');

                $.ajax({
                    url: '{{ route('admin.menus.update-structure') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        menu_items: JSON.stringify(data)
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('{{ __('translate.Menu structure updated successfully') }}');
                            location.reload();
                        } else {
                            alert('{{ __('translate.Error updating menu structure') }}');
                        }
                    },
                    error: function() {
                        alert('{{ __('translate.Error updating menu structure') }}');
                    }
                });
            });
        });
    </script>
@endpush
