@php
    $home1_hero_section = getContent('theme1_slider.content', true);
    $translatedSlides = getTranslatedSlides($home1_hero_section, 'slides');
@endphp


@if ($home1_hero_section)
    <!-- tg-hero-area-start -->
    <div class="tg-hero-area fix p-relative">
        <div class="tg-hero-top-shadow"></div>
        <div class="shop-slider-wrapper">
            @if (count($translatedSlides) > 0)
                <div class="swiper-container tg-hero-slider-active">
                    <div class="swiper-wrapper">
                        @foreach ($translatedSlides as $key => $slide)
                            <div class="swiper-slide">
                                <div class="tg-hero-bg">
                                    <div class="tg-hero-thumb" data-background="{{ $slide['background_image'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="tg-hero-content-area">
            <div class="container">
                <div class="p-relative">
                    <div class="row justify-content-center">
                        <div class="col-xl-10">
                            <div class="tg-hero-content text-center">
                                <div class="tg-hero-title-box mb-10">
                                    <h5 class="tg-hero-subtitle mb-5 wow fadeInUp" data-wow-delay=".3s"
                                        data-wow-duration=".7s">
                                        {{ getTranslatedValue($home1_hero_section, 'sub_title') }}
                                    </h5>
                                    <h2 class="tg-hero-title wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".9s">
                                        {{ getTranslatedValue($home1_hero_section, 'title') }}
                                    </h2>
                                    <p class="tg-hero-para mb-0  wow fadeInUp" data-wow-delay=".6s"
                                        data-wow-duration="1.1s">
                                        {!! strip_tags(clean(getTranslatedValue($home1_hero_section, 'description')), '<br>') !!}
                                    </p>
                                </div>
                                <div class="tg-hero-price-wrap mb-35 d-flex align-items-center justify-content-center  wow fadeInUp"
                                    data-wow-delay=".7s" data-wow-duration="1.3s">
                                    <p class="mr-15">{{ getTranslatedValue($home1_hero_section, 'price_subtitle') }}
                                    </p>
                                    <div class="tg-hero-price d-flex">
                                        <span
                                            class="hero-dolar">{{ getTranslatedValue($home1_hero_section, 'currency_symbol') }}</span>
                                        <span
                                            class="hero-price">{{ getTranslatedValue($home1_hero_section, 'price') }}</span>
                                        <span
                                            class="night">/{{ getTranslatedValue($home1_hero_section, 'per_time') }}</span>
                                    </div>
                                </div>
                                <div class="tg-hero-btn-box  wow fadeInUp" data-wow-delay=".8s"
                                    data-wow-duration="1.5s">
                                    <a href="{{ getTranslatedValue($home1_hero_section, 'button_url') }}"
                                        class="tg-btn tg-btn-switch-animation">
                                        <span class="d-flex align-items-center justify-content-center">
                                            <span
                                                class="btn-text">{{ getTranslatedValue($home1_hero_section, 'button_text') }}</span>
                                            <span class="btn-icon ml-5">
                                                <svg width="21" height="16" viewBox="0 0 21 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1.0017 8.00001H19.9514M19.9514 8.00001L12.9766 1.02515M19.9514 8.00001L12.9766 14.9749"
                                                        stroke="white" stroke-width="1.77778" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            <span class="btn-icon ml-5">
                                                <svg width="21" height="16" viewBox="0 0 21 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1.0017 8.00001H19.9514M19.9514 8.00001L12.9766 1.02515M19.9514 8.00001L12.9766 14.9749"
                                                        stroke="white" stroke-width="1.77778" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (getTranslatedValue($home1_hero_section, 'show_navigation') == 1)
                        <div class="tg-hero-arrow-box d-none d-sm-block">
                            <button class="tg-hero-next">
                                <svg width="19" height="15" viewBox="0 0 19 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.0274 7.5H0.972625M0.972625 7.5L7.25 1.22263M0.972625 7.5L7.25 13.7774"
                                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button class="tg-hero-prev">
                                <svg width="20" height="15" viewBox="0 0 20 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.47263 7.5H18.5274M18.5274 7.5L12.25 1.22263M18.5274 7.5L12.25 13.7774"
                                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tg-hero-bottom-shape d-none d-md-block">
            <span>
                <svg width="432" height="298" viewBox="0 0 432 298" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path class="line-1" opacity="0.4"
                        d="M39.6062 428.345C4.4143 355.065 -24.2999 203.867 142.379 185.309C350.726 162.111 488.895 393.541 289.171 313.515C129.391 249.494 458.204 85.4772 642.582 11.4713"
                        stroke="white" stroke-width="24" />
                </svg>
            </span>
        </div>
        <div class="tg-hero-bottom-shape-2 d-none d-md-block">
            <span>
                <svg width="154" height="321" viewBox="0 0 154 321" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path class="line-1" opacity="0.4"
                        d="M144.616 328.905C116.117 300.508 62.5986 230.961 76.5162 179.949C93.9132 116.184 275.231 7.44493 -65.0181 12.8762"
                        stroke="white" stroke-width="24" />
                </svg>
            </span>
        </div>
    </div>
    <!-- tg-hero-area-end -->
@endif
