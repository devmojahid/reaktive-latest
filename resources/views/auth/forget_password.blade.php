@extends('layout_inner_page')

@section('title')
    <title>{{ __('translate.Forget Password') }}</title>
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- forget-password-area-start -->
    <div class="tg-login-area pt-130 pb-130">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-10">
                    <div class="tg-login-wrapper">
                        <div class="tg-login-top text-center mb-30">
                            <h2>{{ __('translate.Forget Password') }}</h2>
                        </div>
                        <div class="tg-login-form">
                            <div class="tg-tour-about-review-form">
                                <form method="POST" action="{{ route('user.send-forget-password') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="email" name="email" required type="email"
                                                placeholder="{{ __('translate.Email') }}">
                                        </div>
                                        @if ($general_setting->recaptcha_status == 1)
                                            <div class="td_mb_10">
                                                <div class="g-recaptcha"
                                                    data-sitekey="{{ $general_setting->recaptcha_site_key }}"></div>
                                            </div>
                                        @endif
                                        <div class="col-lg-12">
                                            @if ($general_setting->recaptcha_status == 1)
                                                <div class="mb-25">
                                                    <div class="g-recaptcha"
                                                        data-sitekey="{{ $general_setting->recaptcha_site_key }}">
                                                    </div>
                                                </div>
                                            @endif

                                            <button type="submit"
                                                class="tg-btn w-100">{{ __('translate.Send Reset Link') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- forget-password-area-end -->
@endsection

@push('js_section')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
