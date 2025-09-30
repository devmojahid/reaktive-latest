@extends('agency.master_layout')
@section('title')
    <title>{{ __('translate.Extra Charges') }} - {{ $service->translation->title ?? $service->title }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Extra Charges') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Services') }} >>
        {{ __('translate.Extra Charges') }}</p>
@endsection

@push('style_section')
    <style>
        /* Currency Input Field Styling */
        .crancy__item-form--currency {
            position: relative;
            display: flex;
            align-items: center;
        }

        .crancy__item-form--currency .crancy__item-input {
            width: 100%;
            padding-right: 40px;
            /* Add space for the currency icon */
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 40px 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .crancy__item-form--currency .crancy__item-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .crancy__currency-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            /* Prevents icon from interfering with input clicks */
            z-index: 2;
        }

        .crancy__currency-icon span {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        /* Optional: Style for better visual hierarchy */
        .crancy__item-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .mg-top-form-20 {
            margin-top: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .crancy__item-form--currency .crancy__item-input {
                padding-right: 35px;
            }

            .crancy__currency-icon {
                right: 10px;
            }

            .crancy__currency-icon span {
                font-size: 13px;
            }
        }
    </style>
@endpush


@section('body-content')
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Extra Charges for') }}:
                                                {{ $service->translation->title ?? $service->title }}</h4>
                                            <div>
                                                <a href="{{ route('agency.tourbooking.services.edit', $service->id) }}"
                                                    class="crancy-btn"><i class="fa fa-edit"></i>
                                                    {{ __('translate.Edit Service') }}</a>
                                                <a href="{{ route('agency.tourbooking.services.index') }}"
                                                    class="crancy-btn"><i class="fa fa-list"></i>
                                                    {{ __('translate.Service List') }}</a>
                                            </div>
                                        </div>

                                        <div class="row mg-top-30">
                                            <div class="col-12">
                                                <div class="accordion" id="extraChargesAccordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                                aria-expanded="true" aria-controls="collapseOne">
                                                                {{ __('translate.Add New Extra Charge') }}
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingOne"
                                                            data-bs-parent="#extraChargesAccordion">
                                                            <div class="accordion-body">
                                                                <form
                                                                    action="{{ route('agency.tourbooking.services.extra-charges.store', $service->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Name') }}
                                                                                    *</label>
                                                                                <input class="crancy__item-input"
                                                                                    type="text" name="name"
                                                                                    value="{{ old('name') }}" required>
                                                                                @error('name')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Price') }}
                                                                                    *</label>
                                                                                <div class="crancy__item-form--currency">
                                                                                    <input class="crancy__item-input"
                                                                                        type="number" step="0.01"
                                                                                        name="price"
                                                                                        value="{{ old('price') }}"
                                                                                        required>
                                                                                    <div class="crancy__currency-icon">
                                                                                        <span>{{ config('settings.currency_icon', '$') }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                @error('price')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-3 col-md-3 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Price Type') }}
                                                                                    *</label>
                                                                                <select class="crancy__item-input"
                                                                                    name="price_type" required>
                                                                                    <option value="flat"
                                                                                        {{ old('price_type') == 'flat' ? 'selected' : '' }}>
                                                                                        {{ __('translate.Flat Fee') }}
                                                                                    </option>
                                                                                    <option value="per_booking"
                                                                                        {{ old('price_type') == 'per_booking' ? 'selected' : '' }}>
                                                                                        {{ __('translate.Per Booking') }}
                                                                                    </option>
                                                                                    <option value="per_person"
                                                                                        {{ old('price_type') == 'per_person' ? 'selected' : '' }}>
                                                                                        {{ __('translate.Per Person') }}
                                                                                    </option>
                                                                                    <option value="per_adult"
                                                                                        {{ old('price_type') == 'per_adult' ? 'selected' : '' }}>
                                                                                        {{ __('translate.Per Adult') }}
                                                                                    </option>
                                                                                    <option value="per_child"
                                                                                        {{ old('price_type') == 'per_child' ? 'selected' : '' }}>
                                                                                        {{ __('translate.Per Child') }}
                                                                                    </option>
                                                                                    <option value="per_infant"
                                                                                        {{ old('price_type') == 'per_infant' ? 'selected' : '' }}>
                                                                                        {{ __('translate.Per Infant') }}
                                                                                    </option>
                                                                                    <option value="per_night"
                                                                                        {{ old('price_type') == 'per_night' ? 'selected' : '' }}>
                                                                                        {{ __('translate.Per Night') }}
                                                                                    </option>
                                                                                </select>
                                                                                @error('price_type')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 col-md-12 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Description') }}</label>
                                                                                <textarea class="crancy__item-input summernote" name="description" rows="3">{{ old('description') }}</textarea>
                                                                                @error('description')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Is Mandatory') }}</label>
                                                                                <div
                                                                                    class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                    <label class="crancy__item-switch">
                                                                                        <input name="is_mandatory"
                                                                                            type="hidden" value="0">
                                                                                        <input name="is_mandatory"
                                                                                            type="checkbox"
                                                                                            {{ old('is_mandatory') ? 'checked' : '' }}>
                                                                                        <span
                                                                                            class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Is Tax') }}</label>
                                                                                <div
                                                                                    class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                    <label class="crancy__item-switch">
                                                                                        <input name="is_tax" type="hidden"
                                                                                            value="0">
                                                                                        <input name="is_tax"
                                                                                            type="checkbox"
                                                                                            {{ old('is_tax') ? 'checked' : '' }}>
                                                                                        <span
                                                                                            class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Status') }}</label>
                                                                                <div
                                                                                    class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                    <label class="crancy__item-switch">
                                                                                        <input name="status"
                                                                                            type="hidden" value="0">
                                                                                        <input name="status"
                                                                                            type="checkbox"
                                                                                            {{ old('status') ? 'checked' : '' }}
                                                                                            value="1">
                                                                                        <span
                                                                                            class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-6 col-md-6 col-12"
                                                                            id="tax_percentage_field"
                                                                            style="display: none;">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Tax Percentage') }}</label>
                                                                                <div class="crancy__item-form--currency">
                                                                                    <input class="crancy__item-input"
                                                                                        type="number" step="0.01"
                                                                                        min="0" max="100"
                                                                                        name="tax_percentage"
                                                                                        value="{{ old('tax_percentage') }}">
                                                                                    <div class="crancy__currency-icon">
                                                                                        <span>%</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div
                                                                                class="crancy__item-form--group mg-top-form-20">
                                                                                <label
                                                                                    class="crancy__item-label">{{ __('translate.Max Quantity') }}</label>
                                                                                <input class="crancy__item-input"
                                                                                    type="number" name="max_quantity"
                                                                                    value="{{ old('max_quantity') }}"
                                                                                    min="1">
                                                                                @error('max_quantity')
                                                                                    <span
                                                                                        class="text-danger">{{ $message }}</span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 mg-top-30">
                                                                            <button type="submit"
                                                                                class="crancy-btn">{{ __('translate.Add Extra Charge') }}</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="crancy-product-table mg-top-25">
                                            <table id="crancy-table__vendor">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translate.Name') }}</th>
                                                        <th>{{ __('translate.Price') }}</th>
                                                        <th>{{ __('translate.Price Type') }}</th>
                                                        <th>{{ __('translate.Mandatory') }}</th>
                                                        <th>{{ __('translate.Tax') }}</th>
                                                        <th>{{ __('translate.Status') }}</th>
                                                        <th>{{ __('translate.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($service->extraCharges as $charge)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $charge->name }}</strong>
                                                                @if ($charge->description)
                                                                    <div class="small text-muted">
                                                                        {!! strip_tags(Str::limit($charge->description, 50)) !!}</div>
                                                                @endif
                                                            </td>
                                                            <td>{{ config('settings.currency_icon', '$') }}
                                                                {{ $charge->price }}</td>
                                                            <td>{{ $charge->price_type_text }}</td>
                                                            <td>
                                                                @if ($charge->is_mandatory)
                                                                    <span
                                                                        class="crancy-badge crancy-badge-success">{{ __('translate.Yes') }}</span>
                                                                @else
                                                                    <span
                                                                        class="crancy-badge crancy-badge-secondary">{{ __('translate.No') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($charge->is_tax)
                                                                    <span
                                                                        class="crancy-badge crancy-badge-info">{{ __('translate.Yes') }}
                                                                        ({{ $charge->tax_percentage }}%)
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="crancy-badge crancy-badge-secondary">{{ __('translate.No') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($charge->status)
                                                                    <span
                                                                        class="crancy-badge crancy-badge-success">{{ __('translate.Active') }}</span>
                                                                @else
                                                                    <span
                                                                        class="crancy-badge crancy-badge-danger">{{ __('translate.Inactive') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="crancy-table__action">
                                                                <div class="crancy-table__action-btn">
                                                                    <a href="#"
                                                                        class="crancy-action__btn crancy-action__edit"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editModal{{ $charge->id }}"><i
                                                                            class="fa fa-edit"></i></a>
                                                                    <a href="#"
                                                                        class="crancy-action__btn crancy-action__delete"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteModal{{ $charge->id }}"><i
                                                                            class="fa fa-trash"></i></a>
                                                                </div>
                                                            </td>

                                                            <!-- Edit Modal -->
                                                            <div class="modal fade" id="editModal{{ $charge->id }}"
                                                                tabindex="-1"
                                                                aria-labelledby="editModalLabel{{ $charge->id }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="editModalLabel{{ $charge->id }}">
                                                                                {{ __('translate.Edit Extra Charge') }}
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <form
                                                                            action="{{ route('agency.tourbooking.services.extra-charges.update', $charge->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="modal-body">
                                                                                <div class="row">
                                                                                    <div class="col-lg-6 col-md-6 col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Name') }}
                                                                                                *</label>
                                                                                            <input
                                                                                                class="crancy__item-input"
                                                                                                type="text"
                                                                                                name="name"
                                                                                                value="{{ $charge->name }}"
                                                                                                required>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-3 col-md-3 col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Price') }}
                                                                                                *</label>
                                                                                            <div
                                                                                                class="crancy__item-form--currency">
                                                                                                <input
                                                                                                    class="crancy__item-input"
                                                                                                    type="number"
                                                                                                    step="0.01"
                                                                                                    name="price"
                                                                                                    value="{{ $charge->price }}"
                                                                                                    required>
                                                                                                <div
                                                                                                    class="crancy__currency-icon">
                                                                                                    <span>{{ config('settings.currency_icon', '$') }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-3 col-md-3 col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Price Type') }}
                                                                                                *</label>
                                                                                            <select
                                                                                                class="crancy__item-input"
                                                                                                name="price_type" required>
                                                                                                <option value="flat"
                                                                                                    {{ $charge->price_type == 'flat' ? 'selected' : '' }}>
                                                                                                    {{ __('translate.Flat Fee') }}
                                                                                                </option>
                                                                                                <option value="per_booking"
                                                                                                    {{ $charge->price_type == 'per_booking' ? 'selected' : '' }}>
                                                                                                    {{ __('translate.Per Booking') }}
                                                                                                </option>
                                                                                                <option value="per_person"
                                                                                                    {{ $charge->price_type == 'per_person' ? 'selected' : '' }}>
                                                                                                    {{ __('translate.Per Person') }}
                                                                                                </option>
                                                                                                <option value="per_adult"
                                                                                                    {{ $charge->price_type == 'per_adult' ? 'selected' : '' }}>
                                                                                                    {{ __('translate.Per Adult') }}
                                                                                                </option>
                                                                                                <option value="per_child"
                                                                                                    {{ $charge->price_type == 'per_child' ? 'selected' : '' }}>
                                                                                                    {{ __('translate.Per Child') }}
                                                                                                </option>
                                                                                                <option value="per_infant"
                                                                                                    {{ $charge->price_type == 'per_infant' ? 'selected' : '' }}>
                                                                                                    {{ __('translate.Per Infant') }}
                                                                                                </option>
                                                                                                <option value="per_night"
                                                                                                    {{ $charge->price_type == 'per_night' ? 'selected' : '' }}>
                                                                                                    {{ __('translate.Per Night') }}
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group mg-top-form-20">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Description') }}</label>
                                                                                            <textarea class="crancy__item-input summernote" name="description" rows="3">{{ $charge->description }}</textarea>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-4 col-md-4 col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group mg-top-form-20">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Is Mandatory') }}</label>
                                                                                            <div
                                                                                                class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                                <label
                                                                                                    class="crancy__item-switch">
                                                                                                    <input
                                                                                                        name="is_mandatory"
                                                                                                        type="hidden"
                                                                                                        value="0">
                                                                                                    <input
                                                                                                        name="is_mandatory"
                                                                                                        type="checkbox"
                                                                                                        {{ $charge->is_mandatory ? 'checked' : '' }}
                                                                                                        value="1">
                                                                                                    <span
                                                                                                        class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-4 col-md-4 col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group mg-top-form-20">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Is Tax') }}</label>
                                                                                            <div
                                                                                                class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                                <label
                                                                                                    class="crancy__item-switch">
                                                                                                    <input name="is_tax"
                                                                                                        type="hidden"
                                                                                                        value="0">
                                                                                                    <input name="is_tax"
                                                                                                        type="checkbox"
                                                                                                        class="tax-toggle"
                                                                                                        {{ $charge->is_tax ? 'checked' : '' }}
                                                                                                        value="1">
                                                                                                    <span
                                                                                                        class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-4 col-md-4 col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group mg-top-form-20">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Status') }}</label>
                                                                                            <div
                                                                                                class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                                                                <label
                                                                                                    class="crancy__item-switch">
                                                                                                    <input name="status"
                                                                                                        type="hidden"
                                                                                                        value="0">
                                                                                                    <input name="status"
                                                                                                        type="checkbox"
                                                                                                        {{ $charge->status ? 'checked' : '' }}
                                                                                                        value="1">
                                                                                                    <span
                                                                                                        class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-6 col-md-6 col-12 tax-percentage-field"
                                                                                        style="{{ $charge->is_tax ? '' : 'display: none;' }}">
                                                                                        <div
                                                                                            class="crancy__item-form--group mg-top-form-20">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Tax Percentage') }}</label>
                                                                                            <div
                                                                                                class="crancy__item-form--currency">
                                                                                                <input
                                                                                                    class="crancy__item-input"
                                                                                                    type="number"
                                                                                                    step="0.01"
                                                                                                    min="0"
                                                                                                    max="100"
                                                                                                    name="tax_percentage"
                                                                                                    value="{{ $charge->tax_percentage }}">
                                                                                                <div
                                                                                                    class="crancy__currency-icon">
                                                                                                    <span>%</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-lg-6 col-md-6 col-12">
                                                                                        <div
                                                                                            class="crancy__item-form--group mg-top-form-20">
                                                                                            <label
                                                                                                class="crancy__item-label">{{ __('translate.Max Quantity') }}</label>
                                                                                            <input
                                                                                                class="crancy__item-input"
                                                                                                type="number"
                                                                                                name="max_quantity"
                                                                                                value="{{ $charge->max_quantity }}"
                                                                                                min="1">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="crancy-btn crancy-btn__default"
                                                                                    data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                                                                                <button type="submit"
                                                                                    class="crancy-btn">{{ __('translate.Update') }}</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Delete Modal -->
                                                            <div class="modal fade" id="deleteModal{{ $charge->id }}"
                                                                tabindex="-1"
                                                                aria-labelledby="deleteModalLabel{{ $charge->id }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="deleteModalLabel{{ $charge->id }}">
                                                                                {{ __('translate.Confirm Delete') }}</h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            {{ __('translate.Are you sure you want to delete this extra charge?') }}
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="crancy-btn crancy-btn__default"
                                                                                data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                                                                            <form
                                                                                action="{{ route('agency.tourbooking.services.extra-charges.destroy', $charge->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="crancy-btn crancy-btn__danger">{{ __('translate.Delete') }}</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">
                                                                {{ __('translate.No extra charges found') }}</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
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
@endsection

@push('js_section')
    <script src="{{ asset('global/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        (function($) {
            "use strict"
            $(document).ready(function() {

                tinymce.init({
                    selector: '.summernote',
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                    tinycomments_mode: 'embedded',
                    tinycomments_author: 'Author name',
                    mergetags_list: [{
                            value: 'First.Name',
                            title: 'First Name'
                        },
                        {
                            value: 'Email',
                            title: 'Email'
                        },
                    ]
                });

                // Show/hide tax percentage field
                $('input[name="is_tax"]').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#tax_percentage_field').show();
                    } else {
                        $('#tax_percentage_field').hide();
                    }
                });

                // For edit modals
                $('.tax-toggle').each(function() {
                    var $this = $(this);
                    $this.on('change', function() {
                        if ($this.is(':checked')) {
                            $this.closest('.modal-body').find('.tax-percentage-field').show();
                        } else {
                            $this.closest('.modal-body').find('.tax-percentage-field').hide();
                        }
                    });
                });

                // Check initial state
                if ($('input[name="is_tax"]').is(':checked')) {
                    $('#tax_percentage_field').show();
                }

                $('#crancy-table__vendor').DataTable({
                    responsive: true,
                    paging: false,
                    info: false,
                    searching: true,
                    ordering: true,
                });


            });
        })(jQuery);
    </script>
@endpush
