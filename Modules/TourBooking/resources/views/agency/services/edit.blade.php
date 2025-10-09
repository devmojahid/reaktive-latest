@extends('agency.master_layout')
@section('title')
    <title>{{ __('translate.Edit Service') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Edit Service') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Edit Service') }}</p>
@endsection

@push('style_section')
    <link rel="stylesheet" href="{{ asset('global/select2/select2.min.css') }}">
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
    <!-- crancy Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <!-- Dashboard Inner -->
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <!-- Product Card -->
                                    <div class="crancy-product-card translation_main_box">

                                        <div class="crancy-customer-filter">
                                            <div
                                                class="crancy-customer-filter__single crancy-customer-filter__single--csearch">
                                                <div class="crancy-header__form crancy-header__form--customer">
                                                    <h4 class="crancy-product-card__title">
                                                        {{ __('translate.Switch to language translation') }}</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="translation_box">
                                            <ul>
                                                @foreach ($language_list as $language)
                                                    <li><a
                                                            href="{{ route('agency.tourbooking.services.edit', ['service' => $service->id, 'lang_code' => $language->lang_code]) }}">
                                                            @if (request()->get('lang_code') == $language->lang_code)
                                                                <i class="fas fa-eye"></i>
                                                            @else
                                                                <i class="fas fa-edit"></i>
                                                            @endif

                                                            {{ $language->lang_name }}
                                                        </a></li>
                                                @endforeach
                                            </ul>

                                            <div class="alert alert-secondary" role="alert">

                                                @php
                                                    $edited_language = $language_list
                                                        ->where('lang_code', request()->get('lang_code'))
                                                        ->first();
                                                @endphp

                                                <p>{{ __('translate.Your editing mode') }} :
                                                    <b>{{ isset($edited_language) ? $edited_language->lang_name : 'Default' }}</b>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- End Product Card -->
                                </div>
                            </div>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End crancy Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <form
                                action="{{ route('agency.tourbooking.services.update', ['service' => $service->id, 'lang_code' => $lang_code]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="translate_id" value="{{ $translation->id ?? '' }}">
                                <input type="hidden" name="lang_code" value="{{ $lang_code }}">

                                <div class="row">
                                    <div class="col-12 mg-top-30">
                                        <div class="crancy-product-card">
                                            <div class="create_new_btn_inline_box">
                                                <h4 class="crancy-product-card__title">
                                                    {{ __('translate.Basic Information') }}</h4>
                                                <a href="{{ route('agency.tourbooking.services.index') }}"
                                                    class="crancy-btn"><i class="fa fa-list"></i>
                                                    {{ __('translate.Service List') }}</a>
                                            </div>

                                            <div class="row mg-top-30">
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Title') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="title"
                                                            id="title"
                                                            value="{{ old('title', $translation->title ?? $service->title) }}"
                                                            required>
                                                        @error('title')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Slug') }}</label>
                                                        <input class="crancy__item-input" type="text" name="slug"
                                                            id="slug" value="{{ old('slug', $service->slug) }}">
                                                        @error('slug')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Service Type') }}</label>
                                                        <select class="crancy__item-input" name="service_type_id" required>
                                                            <option value="">{{ __('translate.Select Type') }}
                                                            </option>
                                                            @foreach ($serviceTypes as $type)
                                                                <option value="{{ $type->id }}"
                                                                    {{ old('service_type_id', $service->service_type_id) == $type->id ? 'selected' : '' }}>
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('service_type_id')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Select Destination') }}</label>
                                                        <select class="crancy__item-input" name="destination_id">
                                                            <option value="">{{ __('translate.Select Destination') }}
                                                            </option>
                                                            @foreach ($destinations as $destination)
                                                                <option value="{{ $destination->id }}"
                                                                    {{ old('destination_id', $service->destination_id) == $destination->id ? 'selected' : '' }}>
                                                                    {{ $destination->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('destination_id')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Location') }}</label>
                                                        <input class="crancy__item-input" type="text" name="location"
                                                            value="{{ old('location', $service->location) }}">
                                                        @error('location')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Duration') }}</label>
                                                        <input class="crancy__item-input" type="text" name="duration"
                                                            value="{{ old('duration', $service->duration) }}"
                                                            placeholder="e.g. 3 hours, 2 days">
                                                        @error('duration')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Group Size') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="group_size"
                                                            value="{{ old('group_size', $service->group_size) }}"
                                                            placeholder="e.g. Up to 10 people">
                                                        @error('group_size')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Adult Count') }}</label>
                                                        <input class="crancy__item-input" type="number"
                                                            name="adult_count"
                                                            value="{{ old('adult_count', $service->adult_count) }}"
                                                            placeholder="1">
                                                        @error('adult_count')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Children Count') }}</label>
                                                        <input class="crancy__item-input" type="number"
                                                            name="children_count"
                                                            value="{{ old('children_count', $service->children_count) }}"
                                                            placeholder="0">
                                                        @error('children_count')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Short Description') }}</label>
                                                        <textarea class="crancy__item-input summernote" name="short_description" rows="3">{{ old('short_description', $translation->short_description ?? $service->short_description) }}</textarea>
                                                        @error('short_description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Description') }}</label>
                                                        <textarea class="crancy__item-input summernote" name="description" rows="6">{{ old('description', $translation->description ?? $service->description) }}</textarea>
                                                        @error('description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Status') }}</label>
                                                        <div
                                                            class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                                <input name="status" type="checkbox" value="1"
                                                                    {{ old('status', $service->status) ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mg-top-30">
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Pricing Details') }}
                                            </h4>

                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label
                                                        class="crancy__item-label">{{ __('translate.Show Per Person Price') }}</label>
                                                    <div
                                                        class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                        <label class="crancy__item-switch">
                                                            <input name="is_per_person" id="show_per_person_price"
                                                                type="checkbox" @checked($service->is_per_person == 1)>
                                                            <span
                                                                class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4 col-md-6 col-12 d-none per_person_price_div">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Price Per Person') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number"
                                                                step="0.01" name="price_per_person"
                                                                value="{{ old('price_per_person', $service->price_per_person) }}">
                                                            <div class="crancy__currency-icon">
                                                                <span>{{ config('settings.currency_icon', '$') }}</span>
                                                            </div>
                                                        </div>
                                                        @error('price_per_person')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12 full_price_div">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Full Price') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number"
                                                                step="0.01" name="full_price"
                                                                value="{{ old('full_price', $service->full_price) }}">
                                                            <div class="crancy__currency-icon">
                                                                <span>{{ config('settings.currency_icon', '$') }}</span>
                                                            </div>
                                                        </div>
                                                        @error('full_price')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12 full_price_div">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Discount Price') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number"
                                                                step="0.01" name="discount_price"
                                                                value="{{ old('discount_price', $service->discount_price) }}">
                                                            <div class="crancy__currency-icon">
                                                                <span>{{ config('settings.currency_icon', '$') }}</span>
                                                            </div>
                                                        </div>
                                                        @error('discount_price')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12 d-none per_person_price_div">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Child Price') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number"
                                                                step="0.01" name="child_price"
                                                                value="{{ old('child_price', $service->child_price) }}">
                                                            <div class="crancy__currency-icon">
                                                                <span>{{ config('settings.currency_icon', '$') }}</span>
                                                            </div>
                                                        </div>
                                                        @error('child_price')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12 d-none">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Infant Price') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number"
                                                                step="0.01" name="infant_price"
                                                                value="{{ old('infant_price', $service->infant_price) }}">
                                                            <div class="crancy__currency-icon">
                                                                <span>{{ config('settings.currency_icon', '$') }}</span>
                                                            </div>
                                                        </div>
                                                        @error('infant_price')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-6 col-12 d-none">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Security Deposit') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number"
                                                                step="0.01" name="security_deposit"
                                                                value="{{ old('security_deposit', $service->security_deposit) }}">
                                                            <div class="crancy__currency-icon">
                                                                <span>{{ config('settings.currency_icon', '$') }}</span>
                                                            </div>
                                                        </div>
                                                        @error('security_deposit')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12 d-none">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Deposit Required') }}</label>
                                                        <div
                                                            class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                                <input name="deposit_required" type="checkbox"
                                                                    value="1"
                                                                    {{ old('deposit_required', $service->deposit_required) ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12 d-none">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Deposit Percentage') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number"
                                                                min="0" max="100" name="deposit_percentage"
                                                                value="{{ old('deposit_percentage', $service->deposit_percentage) }}">
                                                            <div class="crancy__currency-icon">
                                                                <span>%</span>
                                                            </div>
                                                        </div>
                                                        @error('deposit_percentage')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mg-top-30">
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">
                                                {{ __('translate.Additional Information') }}</h4>

                                            <div class="row mg-top-30">
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Check-in Time') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="check_in_time"
                                                            value="{{ old('check_in_time', $service->check_in_time) }}"
                                                            placeholder="e.g. 14:00">
                                                        @error('check_in_time')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Check-out Time') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="check_out_time"
                                                            value="{{ old('check_out_time', $service->check_out_time) }}"
                                                            placeholder="e.g. 10:00">
                                                        @error('check_out_time')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Ticket') }}</label>
                                                        <input class="crancy__item-input" type="text" name="ticket"
                                                            value="{{ old('ticket', $service->ticket) }}"
                                                            placeholder="e.g. Mobile Voucher or Printed Ticket">
                                                        @error('ticket')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Video URL') }}</label>
                                                        <input class="crancy__item-input" type="text" name="video_url"
                                                            value="{{ old('video_url', $service->video_url) }}"
                                                            placeholder="YouTube or Vimeo URL">
                                                        @error('video_url')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Languages') }}</label>
                                                        <select class="crancy__item-input select2" name="languages[]"
                                                            multiple>

                                                            @foreach ($enum_languages as $language)
                                                                <option value="{{ $language->name }}"
                                                                    @selected(is_array($service?->languages) && in_array($language->name, $service?->languages ?? []))>
                                                                    {{ $language->value }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Amenities') }}</label>
                                                        <select class="crancy__item-input select2" name="amenities[]"
                                                            multiple>
                                                            @foreach ($amenities as $amenity)
                                                                <option value="{{ $amenity->translation->id }}"
                                                                    @selected(is_array($translation->amenities ?? null) && in_array($amenity->translation->id, $translation->amenities ?? []))>
                                                                    {{ $amenity->translation->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.What is included') }}</label>
                                                        <textarea name="included" rows="30" placeholder="One item per line">{{ old('included', $translation->included ?? $service->included) }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.What is excluded') }}</label>
                                                        <textarea name="excluded" rows="30" placeholder="One item per line">{{ old('excluded', $translation->excluded ?? $service->excluded) }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Tour Plan Sub Title') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="tour_plan_sub_title"
                                                            value="{{ old('tour_plan_sub_title', $service->tour_plan_sub_title) }}"
                                                            placeholder="Tour Plan Sub Title">
                                                        @error('tour_plan_sub_title')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mg-top-30">
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">
                                                {{ __('translate.Contact Information') }}</h4>

                                            <div class="row mg-top-30">
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Address') }}</label>
                                                        <textarea class="" name="address" rows="40">{{ old('address', $service->address) }}</textarea>
                                                        @error('address')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Email') }}</label>
                                                        <input class="crancy__item-input" type="email" name="email"
                                                            value="{{ old('email', $service->email) }}">
                                                        @error('email')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Phone') }}</label>
                                                        <input class="crancy__item-input" type="text" name="phone"
                                                            value="{{ old('phone', $service->phone) }}">
                                                        @error('phone')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Website') }}</label>
                                                        <input class="crancy__item-input" type="text" name="website"
                                                            value="{{ old('website', $service->website) }}">
                                                        @error('website')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Google map sub title') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="google_map_sub_title"
                                                            value="{{ old('google_map_sub_title', $service->google_map_sub_title) }}"
                                                            placeholder="Google map sub title">
                                                        @error('google_map_sub_title')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Google map embed url') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="google_map_url"
                                                            value="{{ old('google_map_url', $service->google_map_url) }}"
                                                            placeholder="Google map embed url">
                                                        @error('google_map_url')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mg-top-30">
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">{{ __('translate.SEO Information') }}
                                            </h4>

                                            <div class="row mg-top-30">
                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.SEO Title') }}</label>
                                                        <input class="crancy__item-input" type="text" name="seo_title"
                                                            value="{{ old('seo_title', $translation->seo_title ?? $service->seo_title) }}">
                                                        @error('seo_title')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.SEO Description') }}</label>
                                                        <textarea class="crancy__item-input summernote" name="seo_description" rows="3">{{ old('seo_description', $translation->seo_description ?? $service->seo_description) }}</textarea>
                                                        @error('seo_description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.SEO Keywords') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="seo_keywords"
                                                            value="{{ old('seo_keywords', $translation->seo_keywords ?? $service->seo_keywords) }}"
                                                            placeholder="Comma separated keywords">
                                                        @error('seo_keywords')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mg-top-30">
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Display Options') }}
                                            </h4>

                                            <div class="row mg-top-30">
                                                <div class="col-lg-3 col-md-4 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Featured') }}</label>
                                                        <div
                                                            class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                                <input name="is_featured" type="checkbox" value="1"
                                                                    {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-md-4 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Popular') }}</label>
                                                        <div
                                                            class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                                <input name="is_popular" type="checkbox" value="1"
                                                                    {{ old('is_popular', $service->is_popular) ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-md-4 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Show on Homepage') }}</label>
                                                        <div
                                                            class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                                <input name="show_on_homepage" type="checkbox"
                                                                    value="1"
                                                                    {{ old('show_on_homepage', $service->show_on_homepage) ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-md-4 col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Is New') }}</label>
                                                        <div
                                                            class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                                <input name="is_new" type="checkbox" value="1"
                                                                    {{ old('is_new', $service->is_new) ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mg-top-30">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            {{ __('translate.Manage service images and videos in the') }}
                                            <a href="{{ route('agency.tourbooking.services.media', $service->id) }}"
                                                class="alert-link">{{ __('translate.Media Gallery') }}</a>
                                        </div>
                                        <button class="crancy-btn"
                                            type="submit">{{ __('translate.Update Service') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js_section')
    <script src="{{ asset('global/select2/select2.min.js') }}"></script>
    <script src="{{ asset('global/tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>
        (function($) {
            "use strict"
            $(document).ready(function() {
                $("#title").on("keyup", function(e) {
                    let inputValue = $(this).val();
                    let slug = inputValue.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
                    $("#slug").val(slug);
                });

                $('.select2').select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                });

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

                @if ($service->is_per_person == 1)
                    $('.per_person_price_div').removeClass('d-none');
                    $('.full_price_div').addClass('d-none');
                @endif

                $('#show_per_person_price').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('.per_person_price_div').removeClass('d-none');
                        $('.full_price_div').addClass('d-none');
                    } else {
                        $('.per_person_price_div').addClass('d-none');
                        $('.full_price_div').removeClass('d-none');
                    }
                });

            });
        })(jQuery);
    </script>
@endpush
