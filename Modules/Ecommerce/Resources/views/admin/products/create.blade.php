@extends('admin.master_layout')
@section('title')
    <title>{{ isset($product) ? __('translate.Edit Product') : __('translate.Add New Product') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ isset($product) ? __('translate.Edit Product') : __('translate.Add New Product') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Product') }} >> {{ isset($product) ? __('translate.Edit Product') : __('translate.Add New Product') }}</p>
@endsection

@section('body-content')

    <form action="{{ route('admin.product.store', isset($product) ? $product->id : null) }}" method="POST" enctype="multipart/form-data">
        @csrf

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
                                        <div class="crancy-product-card">
                                            <div class="create_new_btn_inline_box">
                                                <h4 class="crancy-product-card__title">{{ __('translate.Basic Information') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 mg-top-form-20">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="crancy__item-form--group w-100 h-100">
                                                                <label
                                                                    class="crancy__item-label">{{ __('translate.Thumbnail Image') }}
                                                                    * </label>
                                                                <div
                                                                    class="crancy-product-card__upload crancy-product-card__upload--border">
                                                                    <input required accept="image/*" type="file" class="btn-check"
                                                                           name="thumbnail_image" id="input-img1"
                                                                           autocomplete="off"
                                                                           onchange="previewImage(event)">
                                                                    <label class="crancy-image-video-upload__label"
                                                                           for="input-img1">
                                                                        <img id="view_img"
                                                                             src="{{ isset($product) && $product->thumbnail_image ? asset($product->thumbnail_image) : asset($general_setting->placeholder_image) }}">
                                                                        <h4 class="crancy-image-video-upload__title">{{ __('translate.Click here to') }}
                                                                            <span
                                                                                class="crancy-primary-color">{{ __('translate.Choose File') }}</span> {{ __('translate.and upload') }}
                                                                        </h4>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            <div class="col-md-9">
                                                                <div class="crancy__item-form--group mg-top-form-20">
                                                                    <label class="crancy__item-label">{{ __('translate.Title') }}
                                                                        * </label>
                                                                    <input required class="crancy__item-input" type="text"
                                                                        name="name" id="name"
                                                                        value="{{ old('name', $product->name ?? '') }}">
                                                                </div>


                                                                <div class="crancy__item-form--group mg-top-form-20">
                                                                    <label class="crancy__item-label">{{ __('translate.Slug') }} *</label>
                                                                    <input required class="crancy__item-input" type="text"
                                                                        name="slug" id="slug"
                                                                        value="{{ old('slug', $product->slug ?? '') }}">
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                            <label class="crancy__item-label">{{ __('translate.Price') }} *</label>
                                                                            <input class="crancy__item-input" type="number" name="price" id="price" value="{{ old('price', $product->price ?? '') }}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                            <label class="crancy__item-label">{{ __('translate.Offer Price') }}</label>
                                                                            <div class="input-group">
                                                                                <input class="crancy__item-input form-control offer-price" type="number" name="offer_price" id="offer_price" value="{{ old('offer_price', $product->offer_price ?? '') }}">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Is In Stock') }} </label>
                                                        <div class="crancy-ptabs__notify-switch  crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                            <input checked name="is_in_stock" type="checkbox" >
                                                            <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Is Best Selling') }} </label>
                                                        <div class="crancy-ptabs__notify-switch  crancy-ptabs__notify-switch--two">
                                                            <label class="crancy__item-switch">
                                                            <input {{ old('is_best_selling') == 1 ? 'checked' : '' }} name="is_best_selling" type="checkbox" >
                                                            <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Brand') }} * </label>
                                                        <select required class="form-select crancy__item-input" name="brand_id" id="brand_id">
                                                            <option value="" disabled selected>{{ __('translate.Select Brand') }}</option>
                                                            @foreach ($brands as $brand)
                                                                <option value="{{ $brand->id }}" {{ (isset($product) && $product->brand_id == $brand->id) ? 'selected' : '' }}>
                                                                    {{ $brand->translate->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Category') }} * </label>
                                                        <select required class="form-select crancy__item-input" name="category_id" id="category_id">
                                                            <option value="" disabled selected>{{ __('translate.Select Category') }}</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" {{ (isset($product) && $product->category_id == $category->id) ? 'selected' : '' }}>
                                                                    {{ $category->translate->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Tags') }} *</label>
                                                        <input required class="crancy__item-input tags" type="text" name="tags" value="{{ old('tags') }}">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Short Description') }} </label>
                                                        <textarea class="crancy__item-input crancy__item-textarea seo_description_box"
                                                                  name="short_description"
                                                                  id="short_description">{{ old('short_description', $product->translate->short_description ?? '') }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Description') }} *</label>
                                                        <textarea class="crancy__item-input crancy__item-textarea summernote" name="description" id="description">{{ old('description', $product->description ?? '') }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Specification') }}</label>
                                                        <textarea class="crancy__item-input crancy__item-textarea summernote" name="specification" id="specification">{{ old('specification', $product->specification ?? '') }}</textarea>
                                                    </div>
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

        <!-- crancy Dashboard -->
        <section class="crancy-adashboard crancy-show">
            <div class="container container__bscreen">
                <div class="row">
                    <div class="col-12">
                        <div class="crancy-body">
                            <!-- Dashboard Inner -->
                            <div class="crancy-dsinner">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Product Card -->
                                        <div class="crancy-product-card">
                                            <div class="create_new_btn_inline_box">
                                                <h4 class="crancy-product-card__title">{{ __('translate.SEO Information') }}</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.SEO title') }} </label>
                                                        <input class="crancy__item-input" type="text" name="seo_title"
                                                               id="seo_title" value="{{ old('seo_title', $product->translate->seo_title ?? '') }}">
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.SEO Description') }} </label>
                                                        <textarea class="crancy__item-input crancy__item-textarea seo_description_box"
                                                                  name="seo_description"
                                                                  id="seo_description">{{ old('seo_description', $product->translate->seo_description ?? '') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>


                                            <button class="crancy-btn mg-top-25" type="submit">
                                                {{ isset($product) ? __('translate.Update Data') : __('translate.Save Data') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
@endsection


@push('style_section')
    <link rel="stylesheet" href="{{ asset('global/tagify/tagify.css') }}">

    <style>
        .tox .tox-promotion,
        .tox-statusbar__branding{
            display: none !important;
        }
    </style>
    <style>
        .offer-price{
            width: auto !important;
        }
    </style>
@endpush

@push('js_section')
    <script src="{{ asset('global/tagify/tagify.js') }}"></script>
    <script src="{{ asset('global/tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>
        (function ($) {
            "use strict"
            $(document).ready(function () {
                $("#name").on("keyup", function (e) {
                    let inputValue = $(this).val();
                    let slug = inputValue.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
                    $("#slug").val(slug);
                });


                tinymce.init({
                    selector: '.summernote',
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                    tinycomments_mode: 'embedded',
                    tinycomments_author: 'Author name',
                    mergetags_list: [
                        {value: 'First.Name', title: 'First Name'},
                        {value: 'Email', title: 'Email'},
                    ]
                });
                $('.tags').tagify();
            });
        })(jQuery);
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('view_img');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        };
    </script>
@endpush
