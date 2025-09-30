@extends('agency.master_layout')
@section('title')
    <title>{{ __('translate.Agency Profile') }}</title>
@endsection
@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Agency Profile') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Dashboard') }} >> {{ __('translate.Agency Profile') }}</p>
@endsection
@section('body-content')
    <form action="{{ route('agency.update-agency-profile') }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <!-- crancy Dashboard -->
        <section class="crancy-adashboard crancy-show">
            <div class="container container__bscreen">
                <div class="row">

                    <input type="hidden" name="old_agency_logo" value="{{ $user->agency_logo }}">

                    <div class="col-12">
                        <div class="crancy-body">
                            <!-- Dashboard Inner -->
                            <div class="crancy-dsinner">

                                <div class="row">
                                    <div class="col-12 mg-top-30">
                                        <!-- Product Card -->
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Agency Information') }}
                                            </h4>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="crancy__item-form--group mg-top-25 w-100">
                                                        <span
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Agency Logo') }}
                                                            *</span>
                                                        <div
                                                            class="crancy-product-card__upload crancy-product-card__upload--border">
                                                            <input accept="image/*" type="file" class="btn-check" name="agency_logo"
                                                                id="input-img1" autocomplete="off"
                                                                onchange="reviewImage(event)">
                                                            <label class="crancy-image-video-upload__label"
                                                                for="input-img1">
                                                                @if ($user->agency_logo)
                                                                    <img id="view_img"
                                                                        src="{{ asset($user->agency_logo) }}">
                                                                @else
                                                                    <img id="view_img"
                                                                        src="{{ asset($general_setting->placeholder_image) }}">
                                                                @endif

                                                                <h4 class="crancy-image-video-upload__title">
                                                                    {{ __('translate.Click here to') }} <span
                                                                        class="crancy-primary-color">{{ __('translate.Choose File') }}</span>
                                                                    {{ __('translate.and upload') }} </h4>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                    <label
                                                        class="crancy__item-label crancy__item-label-product">{{ __('translate.Agency Name') }}
                                                        * </label>
                                                    <input class="crancy__item-input" type="text" name="agency_name"
                                                        value="{{ $user->agency_name }}">
                                                </div>
                                                <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                    <label
                                                        class="crancy__item-label crancy__item-label-product">{{ __('translate.Agency Slug') }}
                                                        * </label>
                                                    <input class="crancy__item-input" type="text" name="agency_slug"
                                                        value="{{ $user->agency_slug }}">
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="crancy__item-form--group mg-top-25 ">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Short Bio') }}
                                                            *</label>
                                                        <textarea class="crancy__item-input crancy__item-textarea seo_description_box" name="about_me" id="about_me">{{ html_decode($user->about_me) }}</textarea>

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

                    <div class="col-12">
                        <div class="crancy-body">
                            <!-- Dashboard Inner -->
                            <div class="crancy-dsinner">

                                <div class="row">
                                    <div class="col-12 mg-top-30">
                                        <!-- Product Card -->
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Location') }}</h4>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Country') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="country"
                                                            value="{{ html_decode($user->country) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.State') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="state"
                                                            value="{{ html_decode($user->state) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.City') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="city"
                                                            value="{{ html_decode($user->city) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Address') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="address"
                                                            value="{{ html_decode($user->address) }}">
                                                    </div>
                                                </div>

                                                <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                    <label
                                                        class="crancy__item-label crancy__item-label-product">{{ __('translate.Website') }}</label>
                                                    <input class="crancy__item-input" type="text" name="website"
                                                        value="{{ $user->website }}">
                                                </div>

                                                <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                    <label
                                                        class="crancy__item-label crancy__item-label-product">{{ __('translate.Google Map Link') }}</label>
                                                    <input class="crancy__item-input" type="text" name="location_map"
                                                        value="{{ $user->location_map }}">
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

                    <div class="col-12">
                        <div class="crancy-body">
                            <!-- Dashboard Inner -->
                            <div class="crancy-dsinner">

                                <div class="row">
                                    <div class="col-12 mg-top-30">
                                        <!-- Product Card -->
                                        <div class="crancy-product-card">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Social Media') }}</h4>


                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Facebook') }}</label>
                                                        <input class="crancy__item-input" type="text" name="facebook"
                                                            value="{{ html_decode($user->facebook) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Linkedin') }}</label>
                                                        <input class="crancy__item-input" type="text" name="linkedin"
                                                            value="{{ html_decode($user->linkedin) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Twitter') }}</label>
                                                        <input class="crancy__item-input" type="text" name="twitter"
                                                            value="{{ html_decode($user->twitter) }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-25">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Instagram') }}</label>
                                                        <input class="crancy__item-input" type="text" name="instagram"
                                                            value="{{ html_decode($user->instagram) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="crancy-btn mg-top-25"
                                                type="submit">{{ __('translate.Update Now') }}</button>
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
    </form>

    <div id="new_dynamic_content" class="d-none">
        <div class="row new_dynamic_skill_body">
            <div class="col-md-6">
                <div class="crancy__item-form--group mg-top-25">
                    <label class="crancy__item-label crancy__item-label-product">{{ __('translate.Skill') }} </label>
                    <input class="crancy__item-input" type="text" name="skills[]">
                </div>
            </div>
            <div class="col-md-4">
                <div class="crancy__item-form--group mg-top-25">
                    <label class="crancy__item-label crancy__item-label-product">{{ __('translate.Expertise(%)') }}
                    </label>
                    <input class="crancy__item-input" type="text" name="expertises[]">
                </div>
            </div>
            <div class="col-md-2">
                <button class="crancy-btn mg-top-25 remove_dynamic_area_btn" type="button"> <i
                        class="fas fa-trash"></i>{{ __('translate.Remove') }}</button>
            </div>
        </div>

    </div>
@endsection

@push('style_section')
    <style>
        .remove_dynamic_area_btn {
            margin-top: 70px !important;
            background: #ff0808 !important;
        }
    </style>
@endpush
@push('js_section')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {

                // start new skill
                $("#add_new_skill_btn").on("click", function() {

                    let new_skill_item = $("#new_dynamic_content").html()

                    $("#dyanmic_skill_wrapper").append(new_skill_item)
                });

                $(document).on('click', '.remove_dynamic_area_btn', function() {
                    $(this).closest('.new_dynamic_skill_body').remove();
                });

                // end new skill

            })
        })(jQuery);
    </script>
@endpush
