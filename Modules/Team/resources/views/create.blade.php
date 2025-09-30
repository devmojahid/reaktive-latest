@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Create Team Member') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Create Team Member') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Project') }} >> {{ __('translate.Create Team Member') }}</p>
@endsection

@section('body-content')
    <form action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                                                <h4 class="crancy-product-card__title">
                                                    {{ __('translate.Basic Information') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 mg-top-form-20">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="crancy__item-form--group w-100 h-100">
                                                                <label
                                                                    class="crancy__item-label">{{ __('translate.Thumbnail Image') }}
                                                                    (170px X 222px)
                                                                    * </label>
                                                                <div
                                                                    class="crancy-product-card__upload crancy-product-card__upload--border">
                                                                    <input type="file" class="btn-check" name="image"
                                                                        id="input-img1" autocomplete="off"
                                                                        onchange="previewImage(event)">
                                                                    <label class="crancy-image-video-upload__label"
                                                                        for="input-img1">
                                                                        <img id="view_img"
                                                                            src="{{ asset($general_setting->placeholder_image) }}">
                                                                        <h4 class="crancy-image-video-upload__title">
                                                                            {{ __('translate.Click here to') }} <span
                                                                                class="crancy-primary-color">{{ __('translate.Choose File') }}</span>
                                                                            {{ __('translate.and upload') }} </h4>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="crancy__item-form--group w-100 h-100">
                                                                <label
                                                                class="crancy__item-label">{{ __('translate.Team Member Detail Image') }}
                                                                    (496px X 550px)
                                                                </label>
                                                                <div
                                                                    class="crancy-product-card__upload crancy-product-card__upload--border">
                                                                    <input type="file" class="btn-check" name="image_detail"
                                                                        id="image_detail" autocomplete="off"
                                                                        onchange="previewImageDetail(event)">
                                                                    <label class="crancy-image-video-upload__label"
                                                                        for="image_detail">
                                                                        <img id="image_detail_view"
                                                                            src="{{ asset($general_setting->placeholder_image) }}">
                                                                        <h4 class="crancy-image-video-upload__title">
                                                                            {{ __('translate.Click here to') }} <span
                                                                                class="crancy-primary-color">{{ __('translate.Choose File') }}</span>
                                                                            {{ __('translate.and upload') }} </h4>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Name') }} *
                                                        </label>
                                                        <input class="crancy__item-input" type="text" name="name"
                                                            id="name" value="{{ old('name') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Slug') }} *
                                                        </label>
                                                        <input class="crancy__item-input" type="text" name="slug"
                                                            id="slug" value="{{ old('slug') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Designation') }}
                                                            * </label>
                                                        <input class="crancy__item-input" type="text" name="designation"
                                                            id="designation" value="{{ old('designation') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Personal Mail') }}
                                                            * </label>
                                                        <input class="crancy__item-input" type="text" name="mail"
                                                            id="mail" value="{{ old('mail') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Phone Number') }} *
                                                        </label>
                                                        <input class="crancy__item-input" type="text" name="phone_number"
                                                            id="phone_number" value="{{ old('phone_number') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Facebook URL') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="facebook"
                                                            id="facebook" value="{{ old('facebook') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Address') }}</label>
                                                        <input class="crancy__item-input" type="text" name="address"
                                                            id="address" value="{{ old('address') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Website') }}</label>
                                                        <input class="crancy__item-input" type="text" name="website"
                                                            id="website" value="{{ old('website') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Twitter URL') }}
                                                        </label>
                                                        <input class="crancy__item-input" type="text" name="twitter"
                                                            id="twitter" value="{{ old('twitter') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.LinkedIn URL') }}
                                                        </label>
                                                        <input class="crancy__item-input" type="text" name="linkedin"
                                                            id="linkedin" value="{{ old('linkedin') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Pinterest URL') }}
                                                        </label>
                                                        <input class="crancy__item-input" type="text" name="pinterest"
                                                            id="pinterest" value="{{ old('pinterest') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Instagram URL') }}
                                                        </label>
                                                        <input class="crancy__item-input" type="text" name="instagram"
                                                            id="instagram" value="{{ old('instagram') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-12 border-top mt-4">
                                                    <div id="skill-container">
                                                        <div class="row skill-item align-items-end">
                                                            <div class="col-md-5">
                                                                <div class="crancy__item-form--group mg-top-form-20">
                                                                    <label
                                                                        class="crancy__item-label">{{ __('translate.Skill Title') }}</label>
                                                                    <input class="crancy__item-input" type="text"
                                                                        name="skill_title[]">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="crancy__item-form--group mg-top-form-20">
                                                                    <label
                                                                        class="crancy__item-label">{{ __('translate.Skill Percentage') }}</label>
                                                                    <input class="crancy__item-input" type="number"
                                                                        name="skill_percentage[]">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 text-end">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm remove-skill-btn"
                                                                    style="margin-bottom: 10px;">Remove</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <button id="add-skill-btn" class="crancy-btn mg-top-25"
                                                            type="button">{{ __('translate.Add Skill') }}</button>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.About Me') }} *
                                                        </label>
                                                        <textarea class="crancy__item-input crancy__item-textarea summernote" name="description" id="description">{{ old('description') }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Skill short description') }}
                                                        </label>
                                                        <textarea class="crancy__item-input crancy__item-textarea summernote" name="skill_short_description"
                                                            id="skill_short_description">{{ old('skill_short_description') }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Information') }}
                                                        </label>
                                                        <textarea class="crancy__item-input crancy__item-textarea summernote" name="information" id="information">{{ old('information') }}</textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <button class="crancy-btn mg-top-25"
                                                type="submit">{{ __('translate.Save Data') }}</button>

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
        .tox-statusbar__branding {
            display: none !important;
        }
    </style>
@endpush

@push('js_section')
    <script src="{{ asset('global/tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>
        (function($) {
            "use strict"
            $(document).ready(function() {
                $("#name").on("keyup", function(e) {
                    let inputValue = $(this).val();
                    let slug = inputValue.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
                    $("#slug").val(slug);
                })

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

                $(document).on('click', '#add-skill-btn', function() {
                    let skillItem = $('.skill-item').first().clone();
                    skillItem.find('input').val(''); // clear input values
                    $('#skill-container').append(skillItem);
                });

                // Remove skill item
                $(document).on('click', '.remove-skill-btn', function() {
                    // Ensure at least one remains
                    if ($('.skill-item').length > 1) {
                        $(this).closest('.skill-item').remove();
                    } else {
                        alert('At least one skill entry is required.');
                    }
                });

            });
        })(jQuery);

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('view_img');
                output.src = reader.result;
            }

            reader.readAsDataURL(event.target.files[0]);
        };

        function previewImageDetail(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('image_detail_view');
                output.src = reader.result;
            }

            reader.readAsDataURL(event.target.files[0]);
        };
    </script>
@endpush
