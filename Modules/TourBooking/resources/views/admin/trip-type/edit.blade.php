@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Edit Trip Type') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Edit Trip Type') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Edit Trip Type') }}</p>
@endsection

@section('body-content')
    <!-- crancy Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <!-- Dashboard Inner -->
                        <div class="crancy-dsinner">
                            <form action="{{ route('admin.tourbooking.trip-type.update', $tripType->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-12 mg-top-30">
                                        <!-- Product Card -->
                                        <div class="crancy-product-card">
                                            <div class="create_new_btn_inline_box">
                                                <h4 class="crancy-product-card__title">
                                                    {{ __('translate.Edit Trip Type') }}</h4>

                                                <a href="{{ route('admin.tourbooking.trip-type.index') }}"
                                                    class="crancy-btn "><i class="fa fa-list"></i>
                                                    {{ __('translate.Trip Type List') }}</a>
                                            </div>


                                            <div class="row mg-top-30">
                                                <div class="col-12 col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Name') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input class="crancy__item-input" type="text" name="name"
                                                            id="name" required
                                                            value="{{ old('name', $tripType->translation->name ?? $tripType->name) }}">
                                                        @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Slug') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input class="crancy__item-input" type="text" name="slug"
                                                            id="slug" required
                                                            value="{{ old('slug', $tripType->slug) }}">
                                                        @error('slug')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Image') }}</label>
                                                        <input class="crancy__item-input" type="file" name="image"
                                                            accept="image/*">
                                                        @error('image')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                        @if ($tripType->image)
                                                            <div class="mt-2">
                                                                <img src="{{ asset($tripType->image) }}"
                                                                    alt="{{ $tripType->name }}"
                                                                    style="max-width: 100px; max-height: 100px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Description') }}</label>
                                                        <textarea class="crancy__item-input crancy__item-textarea summernote" name="description" rows="3">{!! clean(html_decode(old('description', $tripType->translation->description ?? $tripType->description))) !!}</textarea>
                                                        @error('description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4 mg-top-form-20">
                                                    <div class="crancy__item-form--group">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Status') }}</label>
                                                        <div class="crancy-ptabs__notify-switch">
                                                            <label class="crancy__item-switch">
                                                                <input name="status" type="checkbox"
                                                                    {{ $tripType->status ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4 mg-top-form-20">
                                                    <div class="crancy__item-form--group">
                                                        <label
                                                            class="crancy__item-label">{{ __('translate.Featured') }}</label>
                                                        <div class="crancy-ptabs__notify-switch">
                                                            <label class="crancy__item-switch">
                                                                <input name="is_featured" type="checkbox"
                                                                    {{ $tripType->is_featured ? 'checked' : '' }}>
                                                                <span
                                                                    class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="crancy-btn mg-top-25"
                                                type="submit">{{ __('translate.Update') }}</button>

                                        </div>
                                        <!-- End Product Card -->
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- End crancy Dashboard -->
@endsection

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
        })(jQuery);
    </script>
@endpush
