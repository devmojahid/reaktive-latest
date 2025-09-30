@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- tg-faq-area-start -->
    <div class="tg-pricing-area pb-120 pt-125 p-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="tg-faq-content-wrap">
                        <div class="tg-faq-section-title text-center mb-40">
                            <h5 class="tg-section-subtitle mb-15 wow fadeInUp" data-wow-delay=".3s" data-wow-duration=".9s">
                                {{ getTranslatedValue($faq, 'section_subtitle') }}
                            </h5>
                            <h2 class="mb-15 text-capitalize wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".9s">
                                {{ getTranslatedValue($faq, 'section_title') }}
                            </h2>
                        </div>
                        @if (!empty($faq?->data_values['slides']) && count($faq->data_values['slides']) > 0)
                            <div class="tg-faq-content">
                                <div class="accordion tg-custom-accordion" id="accordionExample">
                                    @foreach ($faq->data_values['slides'] as $key => $slide)
                                        <div @class([
                                            'accordion-item mb-10 wow fadeInUp',
                                            'tg-faq-active' => $key == 0,
                                        ]) data-wow-delay=".3s" data-wow-duration=".9s">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button {{ $key != 0 ? 'collapsed' : '' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse_{{ $key }}"
                                                    aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                                    aria-controls="collapse_{{ $key }}">
                                                    {{ $slide['title'] ?? 'No question provided' }}
                                                </button>
                                            </h2>
                                            <div id="collapse_{{ $key }}" @class(['accordion-collapse collapse', 'show' => $key == 0])
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <p class="mb-0">
                                                        {!! strip_tags(clean($slide['description'] ?? 'No answer provided')) !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-faq-area-end -->

    <!-- tg-cta-area-start -->
    <div class="tg-cta-area-area tg-cta-space pt-125 z-index-9 p-relative">
        <img class="tg-cta-price-shape d-none d-xl-block" src="{{ asset('frontend/assets/img/shape/hill.png') }}" alt="">
        <img class="tg-cta-price-shape-2 d-none d-xl-block" src="{{ asset('frontend/assets/img/shape/tree.png') }}" alt="">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tg-cta-wrap include-bg"
                        data-background="{{ asset('frontend/assets/img/shape/cta-banner.jpg') }}">
                        <div class="row align-items-end">
                            <div class="col-lg-3 d-none d-lg-block">
                                <div class="tg-cta-thumb pt-50 ml-60">
                                    <img src="{{ asset(getSingleImage($footer_cta, 'image')) }}" alt="">
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6">
                                <div class="tg-cta-content">
                                    <h5 class="tg-section-subtitle text-white mb-10">
                                        {{ getTranslatedValue($footer_cta, 'subtitle') }}</h5>
                                    <h2 class="mb-15 tg-cta-title text-white text-capitalize">
                                        {!! strip_tags(clean(getTranslatedValue($footer_cta, 'title'))) !!}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="tg-cta-apps">
                                    <a class="mb-20 d-inline-block mr-5"
                                        href="{{ getTranslatedValue($footer_cta, 'apple_store_link') }}">
                                        <img src="{{ asset('frontend/assets/img/shape/google.png') }}" alt="">
                                    </a>
                                    <a class="mb-20 d-inline-block"
                                        href="{{ getTranslatedValue($footer_cta, 'google_play_link') }}">
                                        <img src="{{ asset('frontend/assets/img/shape/app.png') }}" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-cta-area-end -->
@endsection
