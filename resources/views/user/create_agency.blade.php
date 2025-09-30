@extends('user.master_layout')
@section('title')
    <title>{{ __('translate.Create a agency') }}</title>
@endsection
@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Create a agency') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Dashboard') }} >> {{ __('translate.Create a agency') }}</p>
@endsection
@section('body-content')
    <form action="{{ route('user.agency-application') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <!-- crancy Dashboard -->
        <section class="crancy-adashboard crancy-show">
            <div class="container container__bscreen">
                <div class="row">

                    @if ($user->instructor_joining_request == 'pending')
                        <div class="col-12  mg-top-30">
                            <div class="crancy-body">
                                <!-- Dashboard Inner -->
                                <div class="crancy-dsinner">
                                    <div class="crancy-product-card">
                                        <div class="alert alert-warning alert-has-icon">
                                            <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                                            <div class="alert-body">
                                                <div class="alert-title">{{ __('translate.Notice') }}</div>
                                                <p>{{ __('translate.Your agency application under the review. please wait sometimes. you will get notify after the application approval') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($user->instructor_joining_request == 'not_yet' || $user->instructor_joining_request == 'rejected')
                        <div class="col-12">
                            <div class="crancy-body">
                                <!-- Dashboard Inner -->
                                <div class="crancy-dsinner">
                                    <div class="row">
                                        <div class="col-12 mg-top-30">
                                            <!-- Product Card -->
                                            <div class="crancy-product-card">
                                                <h4 class="crancy-product-card__title">
                                                    {{ __('translate.Agency Information') }}
                                                </h4>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="crancy__item-form--group mg-top-25 w-100">
                                                            <span
                                                                class="crancy__item-label crancy__item-label-product">{{ __('translate.Agency Logo') }}
                                                                *</span>
                                                            <div
                                                                class="crancy-product-card__upload crancy-product-card__upload--border">
                                                                <input accept="image/*" type="file" class="btn-check"
                                                                    name="agency_logo" id="input-img1" autocomplete="off"
                                                                    onchange="reviewImage(event)">
                                                                <label class="crancy-image-video-upload__label"
                                                                    for="input-img1">

                                                                    <img id="view_img">

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
                                                            value="{{ old('agency_name') }}">
                                                    </div>
                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Agency Slug') }}
                                                            * </label>
                                                        <input class="crancy__item-input" type="text" name="agency_slug"
                                                            value="{{ old('agency_slug') }}">
                                                    </div>

                                                </div>

                                                <div class="crancy__item-form--group mg-top-25">
                                                    <label
                                                        class="crancy__item-label crancy__item-label-product">{{ __('translate.Agency Description') }}
                                                        *</label>
                                                    <textarea class="crancy__item-input crancy__item-textarea seo_description_box" name="about_me" id="about_me">{{ old('about_me') }}</textarea>

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
                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Country') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="country"
                                                            value="{{ old('country') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.State') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="state"
                                                            value="{{ old('state') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.City') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="city"
                                                            value="{{ old('city') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Address') }}
                                                            *</label>
                                                        <input class="crancy__item-input" type="text" name="address"
                                                            value="{{ old('address') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Website') }}</label>
                                                        <input class="crancy__item-input" type="text" name="website"
                                                            value="{{ old('website') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Google Map Link') }}</label>
                                                        <input class="crancy__item-input" type="text"
                                                            name="location_map" value="{{ old('location_map') }}">
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
                                                <h4 class="crancy-product-card__title">{{ __('translate.Social Media') }}
                                                </h4>


                                                <div class="row">
                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Facebook') }}</label>
                                                        <input class="crancy__item-input" type="text" name="facebook"
                                                            value="{{ old('facebook') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Linkedin') }}</label>
                                                        <input class="crancy__item-input" type="text" name="linkedin"
                                                            value="{{ old('linkedin') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Twitter') }}</label>
                                                        <input class="crancy__item-input" type="text" name="twitter"
                                                            value="{{ old('twitter') }}">
                                                    </div>

                                                    <div class="crancy__item-form--group mg-top-25 col-md-6">
                                                        <label
                                                            class="crancy__item-label crancy__item-label-product">{{ __('translate.Instagram') }}</label>
                                                        <input class="crancy__item-input" type="text" name="instagram"
                                                            value="{{ old('instagram') }}">
                                                    </div>
                                                </div>


                                                <button class="crancy-btn mg-top-25"
                                                    type="submit">{{ __('translate.Apply Now') }}</button>
                                            </div>
                                            <!-- End Product Card -->
                                        </div>
                                    </div>

                                </div>
                                <!-- End Dashboard Inner -->
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </section>
        <!-- End crancy Dashboard -->
    </form>
@endsection


@push('js_section')
    <script>
        "use strict";


        document.addEventListener('DOMContentLoaded', function() {
            const agencyNameInput = document.querySelector('input[name="agency_name"]');
            const agencySlugInput = document.querySelector('input[name="agency_slug"]');

            agencyNameInput.addEventListener('input', function() {
                const slug = agencyNameInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '') // Remove invalid characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-'); // Collapse multiple hyphens
                agencySlugInput.value = slug;
            });
        });

        function reviewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('view_img');
                output.src = reader.result;
            }

            reader.readAsDataURL(event.target.files[0]);
        };
    </script>
@endpush
