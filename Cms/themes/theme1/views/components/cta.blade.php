@php
    $theme1_cta = getContent('theme1_cta.content', true);
@endphp

@if (!empty($theme1_cta))
    <!-- tg-cta-area-start -->
    <div class="tg-cta-area-area tg-cta-space z-index-9 p-relative">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tg-cta-wrap include-bg"
                        data-background="{{ asset('frontend/assets/img/shape/cta-bg.jpeg') }}">
                        <div class="row align-items-end">
                            <div class="col-lg-3 d-none d-lg-block">
                                <div class="tg-cta-thumb pt-50 ml-60">
                                    <img src="{{ asset(getSingleImage($theme1_cta, 'image')) }}" alt="">
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6">
                                <div class="tg-cta-content">
                                    <h5 class="tg-section-subtitle text-white mb-10">
                                        {{ getTranslatedValue($theme1_cta, 'sub_title') }}
                                    </h5>
                                    <h2 class="mb-15 tg-cta-title text-white text-capitalize">
                                        {!! strip_tags(clean(getTranslatedValue($theme1_cta, 'title')), '<br>') !!}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="tg-cta-apps">
                                    <a target="_blank" class="mb-20 d-inline-block mr-5"
                                        href="{{ getTranslatedValue($theme1_cta, 'google_play_link') }}">
                                        <img src="{{ asset('frontend/assets/img/shape/google.png') }}" alt="">
                                    </a>
                                    <a target="_blank" class="mb-20 d-inline-block"
                                        href="{{ getTranslatedValue($theme1_cta, 'apple_store_link') }}"><img
                                            src="{{ asset('frontend/assets/img/shape/app.png') }}" alt="">
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
@endif
