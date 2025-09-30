@php
    $home1_about = getContent('theme1_about_us.content', true);
@endphp


@if (!empty($home1_about))
    <!-- tg-about-area-start -->
    <div class="tg-about-area pb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="tg-about-thumb-wrap mb-30">
                        <img class="w-100 tg-round-15 mb-85 wow fadeInLeft" data-wow-delay=".3s" data-wow-duration=".7s"
                            src="{{ asset(getSingleImage($home1_about, 'left_top_image')) }}" alt="about">
                        <img class="tg-about-thumb-2 tg-round-15 wow fadeInLeft" data-wow-delay=".4s"
                            data-wow-duration=".9s" src="{{ asset(getSingleImage($home1_about, 'left_bottom_image')) }}"
                            alt="about">
                    </div>
                </div>
                <div class="col-lg-6 mb-30">
                    <div class="tg-about-content text-center">
                        <div class="tg-about-logo mb-30 wow fadeInUp" data-wow-delay=".3s" data-wow-duration=".5s">
                            <img src="{{ asset(getSingleImage($home1_about, 'center_image')) }}" alt="logo">
                        </div>
                        <div class="tg-about-section-title mb-25">
                            <h5 class="tg-section-subtitle wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".6s">
                                {{ getTranslatedValue($home1_about, 'sub_title') }}
                            </h5>
                            <h2 class="mb-15 wow fadeInUp" data-wow-delay=".5s" data-wow-duration=".7s">
                                {{ getTranslatedValue($home1_about, 'title') }}
                            </h2>
                            <p class="text-capitalize wow fadeInUp" data-wow-delay=".6s" data-wow-duration=".8s">
                                {!! strip_tags(clean(getTranslatedValue($home1_about, 'description')), '<br>') !!}
                            </p>
                        </div>
                        <div class="tp-about-btn-wrap wow fadeInUp" data-wow-delay=".7s" data-wow-duration=".9s">
                            <a href="{{ getTranslatedValue($home1_about, 'button_url') }}"
                                class="tg-btn tg-btn-transparent tg-btn-switch-animation">
                                <span class="d-flex align-items-center justify-content-center">
                                    <span class="btn-text">{{ getTranslatedValue($home1_about, 'button_text') }}</span>
                                    <span class="btn-icon ml-5">
                                        <svg width="21" height="16" viewBox="0 0 21 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M1.0017 8.00001H19.9514M19.9514 8.00001L12.9766 1.02515M19.9514 8.00001L12.9766 14.9749"
                                                stroke="currentColor" stroke-width="1.77778" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span class="btn-icon ml-5">
                                        <svg width="21" height="16" viewBox="0 0 21 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M1.0017 8.00001H19.9514M19.9514 8.00001L12.9766 1.02515M19.9514 8.00001L12.9766 14.9749"
                                                stroke="currentColor" stroke-width="1.77778" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="tg-about-thumb-wrap  mb-30">
                        <img class="w-100 tg-round-15 mb-85 wow fadeInRight" data-wow-delay=".3s"
                            data-wow-duration=".7s" src="{{ asset(getSingleImage($home1_about, 'right_top_image')) }}"
                            alt="about">
                        <img class="tg-about-thumb-4 tg-round-15 wow fadeInRight" data-wow-delay=".4s"
                            data-wow-duration=".9s" src="{{ asset(getSingleImage($home1_about, 'right_bottom_image')) }}"
                            alt="about">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-about-area-end -->
@endif
