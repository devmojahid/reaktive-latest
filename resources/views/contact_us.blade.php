@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- tg-contact-area-start -->
    <div class="tg-contact-area pt-130 p-relative z-index-1 pb-100">
        <img class="tg-team-shape-2 d-none d-md-block" src="assets/img/banner/banner-2/shape.png" alt="">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="tg-team-details-contant tg-contact-info-wrap mb-30">
                        <h6 class="mb-15">{{ __('translate.Information') }}:</h6>
                        <p class="mb-25">{{ __('translate.Brendan Fraser, renowned actor of the silver screen') }}</p>
                        <div class="tg-team-details-contact-info mb-35">
                            <div class="tg-team-details-contact">
                                <div class="item">
                                    <span>{{ __('translate.Phone') }} :</span>
                                    <a href="tel:{{ $contact_us->phone }}">{{ $contact_us->phone }}</a>
                                </div>
                                <div class="item">
                                    <span>{{ __('translate.Website') }} : </span>
                                    <a target="__blank" href="{{ getLink($contact_us->website) }}">{{ $contact_us->website }}</a>
                                </div>
                                <div class="item">
                                    <span>{{ __('translate.E-mail') }} : </span>
                                    <a href="mailto:{{ $contact_us->email }}">{{ $contact_us->email }}</a>
                                </div>
                                <div class="item">
                                    <span>{{ __('translate.Address') }} :</span>
                                    <a href="#"> {{ $contact_us->address }} </a>
                                </div>
                            </div>
                        </div>
                        <div class="tg-contact-map h-100">
                            <iframe id="map" src="{{ html_decode($contact_us->map_code) }}"
                                allowfullscreen=""></iframe>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="tg-contact-content-wrap ml-40 mb-30">
                        <h3 class="tg-contact-title mb-15">{{ __("translate.Let's connect and get to know") }} <br>
                            {{ __('translate.each other') }}</h3>
                        <p class="mb-30">
                            {{ __('translate.Brendan Fraser, renowned actor of the silver screen, has taken on a new') }}
                        </p>
                        <div class="tg-contact-form tg-tour-about-review-form">
                            <form id="contact-form" method="POST">
                                @csrf
                                <input type="hidden" name="instructor_id" value="0">
                                <div class="row">
                                    <div class="col-lg-6 mb-25">
                                        <input class="input" type="text" name="name"
                                            placeholder="{{ __('translate.Full Name') }} *"
                                            value="{{ html_decode(old('name')) }}">
                                        <span class="text-danger error-name"></span>
                                    </div>
                                    <div class="col-lg-6 mb-25">
                                        <input class="input" type="email" name="email"
                                            placeholder="{{ __('translate.Email') }}  *"
                                            value="{{ html_decode(old('email')) }}">
                                        <span class="text-danger error-email"></span>
                                    </div>
                                    <div class="col-lg-6 mb-25">
                                        <input class="input" type="text" name="website" placeholder="Website">
                                        <span class="text-danger error-website"></span>
                                    </div>
                                    <div class="col-lg-6 mb-25">
                                        <input class="input" type="text" name="subject"
                                            placeholder="{{ __('translate.Subject') }} *"
                                            value="{{ html_decode(old('subject')) }}">
                                        <span class="text-danger error-subject"></span>
                                    </div>
                                    <div class="col-lg-12">
                                        <textarea class="textarea  mb-5" placeholder="{{ __('translate.Message') }} *" name="message">{{ html_decode(old('message')) }}</textarea>
                                        <span class="text-danger error-message"></span>
                                        <div class="review-checkbox d-flex align-items-center mb-25">
                                            <input name="checkbox" class="tg-checkbox" type="checkbox" id="australia">
                                            <label for="australia"
                                                class="tg-label">{{ __('translate.Save my name, email, and website in this browser for the next time I comment.') }}</label>
                                        </div>
                                        @if ($general_setting->recaptcha_status == 1)
                                            <div class="contact_modal_form_item">
                                                <div class="g-recaptcha"
                                                    data-sitekey="{{ $general_setting->recaptcha_site_key }}"></div>
                                            </div>
                                        @endif
                                        <button type="submit" class="tg-btn">
                                            <span class="loader-text d-none">{{ __('translate.Please wait') }}</span>
                                            <span class="button-text">{{ __('translate.Send Message') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-contact-area-end -->
@endsection

@push('js_section')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#contact-form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this)[0];
                let formData = new FormData(form);

                // Clear previous errors
                $('.text-danger').text('');
                $('.tg-btn').attr('disabled', 'disabled');
                $('.tg-btn .button-text').addClass('d-none');
                $('.tg-btn .loader-text').removeClass('d-none');

                axios.post("{{ route('store-contact-message') }}", formData)
                    .then(function(response) {
                        if (response.data.alert_type == 'success') {
                            toastr.success(response.data.message);
                            form.reset(); // Reset form
                        }
                    })
                    .catch(function(error) {
                        if (error.response && error.response.data && error.response.data.errors) {
                            let errors = error.response.data.errors;
                            $.each(errors, function(field, messages) {
                                $('.error-' + field).text(messages[0]);
                            });
                        } else {
                            $('.ajax-response').html(
                                '<span style="color:red;">An error occurred. Please try again.</span>'
                            );
                        }
                    }).finally(function() {
                        $('.tg-btn').removeAttr('disabled');
                        $('.tg-btn .button-text').removeClass('d-none');
                        $('.tg-btn .loader-text').addClass('d-none');
                    });
            });
        });
    </script>
@endpush
