@extends('layout_inner_page')

@section('title')
    <title>{{ $general_setting->app_name }} || {{ __('translate.404 Error Page') }}</title>
@endsection

@section('front-content')
    @include('breadcrumb', ['breadcrumb_title' => __('translate.404 Error Page')])

    <!-- tg-error-area-start -->
    <div class="tg-error-area-start tg-error-spacing">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-9">
                    <div class="tg-error-content text-center">
                        <img class="mb-40" src="{{ asset( $general_setting->not_found ? $general_setting->not_found : 'frontend/assets/img/shape/not_found.png') }}" alt="error">
                        <h2 class="mb-15">{{ __('translate.Error Page!') }}</h2>
                        <p class="mb-35">{{ __('translate.Sorry! This Page is Not Available!') }}</p>
                        <div class="tg-error-btn">
                            <a class="tg-btn" href="{{ route('home') }}">{{ __('translate.Go Back To Home Page') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-error-area-end -->
@endsection
