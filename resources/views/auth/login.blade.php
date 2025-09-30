@extends('layout_inner_page')

@section('title')
    <title>{{ __('translate.Sign In') }}</title>
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- login-area-start -->
    <div class="tg-login-area pt-130 pb-130">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-10">
                    <div class="tg-login-wrapper">
                        <div class="tg-login-top text-center mb-30">
                            <h2>{{ __('translate.Sign in to your account') }}</h2>
                            <p>{{ __('translate.Enter your credentials to access your account') }}.</p>
                        </div>
                        <div class="tg-login-form">
                            <div class="tg-tour-about-review-form">
                                <form method="POST" action="{{ route('user.store-login') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="email" name="email" required type="email"
                                                placeholder="{{ __('translate.Email') }}">
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="password" name="password" required type="password"
                                                placeholder="{{ __('translate.Password') }}">
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="review-checkbox d-flex align-items-center mb-25">
                                                    <input class="tg-checkbox" type="checkbox" id="australia">
                                                    <label for="australia"
                                                        class="tg-label">{{ __('translate.Remember me') }}</label>
                                                </div>
                                                <div class="tg-login-navigate mb-25">
                                                    <a
                                                        href="{{ route('user.register') }}">{{ __('translate.Register Now') }}</a>
                                                </div>
                                            </div>

                                            @if ($general_setting->recaptcha_status == 1)
                                                <div class="mb-25">
                                                    <div class="g-recaptcha"
                                                        data-sitekey="{{ $general_setting->recaptcha_site_key }}">
                                                    </div>
                                                </div>
                                            @endif

                                            <button type="submit"
                                                class="tg-btn w-100">{{ __('translate.Sign In') }}</button>


                                            <div class="d-flex gap-3 justify-content-center align-items-center mt-4 mb-4">
                                                <div class="edc-line-sperator"></div>
                                                <p class="td_fs_20 mb-0 td_medium ">{{ __('translate.or sign up with') }}
                                                </p>
                                                <div class="edc-line-sperator"></div>
                                            </div>

                                            <div
                                                class="td_form_social td_fs_20 td_medium d-flex justify-content-center gap-4">

                                                @if ($general_setting->is_gmail == 1)
                                                    <a href="{{ route('user.login-google') }}" class="td_center">
                                                        <i class="fa-brands fa-google"></i>
                                                    </a>
                                                @endif

                                                @if ($general_setting->is_facebook == 1)
                                                    <a href="{{ route('user.login-facebook') }}" class="td_center">
                                                        <i class="fa-brands fa-facebook-f"></i>
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="text-center mt-4"><a href="{{ route('user.forget-password') }}"
                                                    class="td_semibold td_accent_color">{{ __('translate.Forgot Password?') }}</a>
                                            </div>

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
    <!-- login-area-end -->
@endsection

@push('js_section')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
