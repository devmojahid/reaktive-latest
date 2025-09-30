@extends('admin.master_layout')
@section('title')
    <title>{{ $page_title }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ $page_title }}</h3>
    <p class="crancy-header__text">{{ __('translate.Frontend Section') }} >> {{ $page_title }}</p>
@endsection

@section('body-content')
    <!-- Language Selection Section -->
    <section class="crancy-adashboard crancy-show language_box">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <div class="crancy-product-card translation_main_box">
                                        <div class="crancy-customer-filter">
                                            <div class="crancy-customer-filter__single crancy-customer-filter__single--csearch">
                                                <div class="crancy-header__form crancy-header__form--customer">
                                                    <h4 class="crancy-product-card__title">{{ __('translate.Switch to language translation') }}</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="translation_box">
                                            <ul>
                                                @foreach ($language_list as $language)
                                                    <li>
                                                        <a href="{{ route('admin.front-end.section', ['id'=> $key,'lang_code' => $language->lang_code] ) }}">
                                                            @if (request()->get('lang_code') == $language->lang_code)
                                                                <i class="fas fa-eye"></i>
                                                            @else
                                                                <i class="fas fa-edit"></i>
                                                            @endif
                                                            {{ $language->lang_name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="alert alert-secondary" role="alert">
                                                @php
                                                    $edited_language = $language_list->where('lang_code', request()->get('lang_code'))->first();
                                                @endphp
                                                <p>{{ __('translate.Your editing mode') }}: <b>{{ $edited_language->lang_name }}</b></p>
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

    <!-- Content Edit Section -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="crancy-product-card mg-top-30">
                                <form action="{{ route('admin.front-end.store', ['key' => $key, 'id' => $frontend->id ?? null]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="type" value="{{ $contentType }}">
                                    <input type="hidden" name="lang_code" value="{{ request()->get('lang_code') }}">

                                    <div class="row">
                                        @php
                                            $hasImages = isset($content['images']) && is_array($content['images']) && count($content['images']) > 0;
                                            $isDefaultLanguage = $lang_code === 'en';
                                        @endphp

                                        @if($isDefaultLanguage && $hasImages)
                                            <div class="col-md-3 pr-md-4">
                                                @foreach($content['images'] as $imageKey => $imageDetails)
                                                    @php
                                                        $existingImagePath = $dataValues['images'][$imageKey] ?? null;
                                                    @endphp
                                                    
                                                    <div class="crancy__item-form--group mb-4">
                                                        <label for="{{ $imageKey }}" class="crancy__item-label">
                                                            {{ str_replace('_', ' ', ucfirst($imageKey)) }}
                                                            @if(isset($imageDetails['required']) && $imageDetails['required'])
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                            @if(isset($imageDetails['size']))
                                                                <span data-toggle="tooltip" data-placement="top" class="fa fa-info-circle text--primary"
                                                                    title="{{ __('translate.Recommended image size') }}: {{ $imageDetails['size'] }}"></span>
                                                            @endif
                                                        </label>

                                                        <div class="crancy-product-card__upload crancy-product-card__upload--border">
                                                            <input type="file" id="{{ $imageKey }}" name="{{ $imageKey }}"
                                                                class="custom-file-input d-none @error($imageKey) is-invalid @enderror"
                                                                accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(event, '{{ $imageKey }}')"
                                                                {{ (isset($imageDetails['required']) && $imageDetails['required'] && !$existingImagePath) ? 'required' : '' }}>

                                                            <label class="crancy-image-video-upload__label" for="{{ $imageKey }}">
                                                                <img id="view_img_{{ $imageKey }}"
                                                                    src="{{ $existingImagePath ? asset($existingImagePath) : asset('backend/img/placeholder-image.jpg') }}"
                                                                    alt="{{ str_replace('_', ' ', ucfirst($imageKey)) }}">
                                                                <h4 class="crancy-image-video-upload__title">
                                                                    {{ __('translate.Click here to') }}
                                                                    <span class="crancy-primary-color">{{ __('translate.Choose File') }}</span>
                                                                    {{ __('translate.and upload') }}
                                                                </h4>
                                                            </label>

                                                            @if ($existingImagePath)
                                                                <input type="hidden" name="images_{{ $imageKey }}_existing" value="{{ $existingImagePath }}">
                                                                <div class="d-flex justify-content-end mt-2">
                                                                    <button type="button" class="btn btn-sm btn-danger" onclick="resetImage('{{ $imageKey }}')">
                                                                        <i class="fas fa-times"></i> {{ __('translate.Remove') }}
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @error($imageKey)
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="{{ $isDefaultLanguage && $hasImages ? 'col-md-9 pl-md-4' : 'col-12' }}">
                                            @if($content)
                                                @foreach($content as $field => $value)
                                                    @if($field !== 'images')
                                                        @if(is_array($value) && !isset($value['type']))
                                                            @foreach($value as $subField => $subValue)
                                                                @php
                                                                    $subFieldValue = $dataValues[$field][$subField] ?? (is_scalar($subValue) ? $subValue : json_encode($subValue));
                                                                @endphp
                                                                
                                                                @include('admin.frontend-management.fields.text', [
                                                                    'name' => $field . '[' . $subField . ']',
                                                                    'label' => str_replace('_', ' ', ucfirst($field)) . ' - ' . str_replace('_', ' ', ucfirst($subField)),
                                                                    'value' => $subFieldValue,
                                                                    'required' => false
                                                                ])
                                                            @endforeach
                                                        @else
                                                            @php
                                                                $fieldType = is_array($value) ? ($value['type'] ?? 'text') : 'text';
                                                                $fieldConfig = is_array($value) ? $value : ['type' => 'text', 'required' => false];
                                                                $fieldValue = is_array($value) ? ($dataValues[$field] ?? null) : ($dataValues[$field] ?? $value);
                                                                
                                                                // Skip image fields in the default language if we have a separate image column
                                                                $skipField = $isDefaultLanguage && $fieldType === 'image' && $hasImages;
                                                            @endphp

                                                            @if(!$skipField)
                                                                @include('admin.frontend-management.fields.' . $fieldType, [
                                                                    'name' => $field,
                                                                    'label' => str_replace('_', ' ', ucfirst($field)),
                                                                    'value' => $fieldValue,
                                                                    'required' => $fieldConfig['required'] ?? false,
                                                                    'help' => $fieldConfig['help'] ?? null,
                                                                    'options' => $fieldConfig['options'] ?? [],
                                                                    'fields' => $fieldConfig['fields'] ?? []
                                                                ])
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <p>{{ __('translate.Nothing to display') }}</p>
                                            @endif

                                            <button type="submit" class="crancy-btn mg-top-25">{{ __('translate.Update') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style_section')
<style>
    .crancy-product-card__upload--border img {
        max-height: 200px !important;
    }

    .crancy__item-form--group{
        margin-bottom: 20px;
    }
</style>
@endpush

@push('js_section')
<script>
"use strict";

function previewImage(event, target_view_id) {
    if (!event || !event.target || !event.target.files || !event.target.files[0]) {
        return;
    }
    
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById(`view_img_${target_view_id}`);
        if (output) {
            output.src = reader.result;
        }
    }
    reader.readAsDataURL(event.target.files[0]);
    
    // If we have a hidden input for existing image, mark it for deletion
    const existingInput = document.querySelector(`input[name="images_${target_view_id}_existing"]`) || 
                          document.querySelector(`input[name="${target_view_id}_existing"]`);
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
    const existingInput = document.querySelector(`input[name="images_${target_view_id}_existing"]`) || 
                          document.querySelector(`input[name="${target_view_id}_existing"]`);
    if (existingInput) {
        existingInput.value = '';
    }
}
</script>
@endpush
