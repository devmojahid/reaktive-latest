@php
    $home1_why_choose = getContent('theme1_why_choose_us.content', true);
    $translatedSlides = getTranslatedSlides($home1_why_choose, 'slides');
@endphp

@if ($home1_why_choose)
    <!-- tg-chose-us-area-start -->
    <div class="tg-chose-area p-relative pt-135 pb-100">
        <img class="tg-chose-shape p-absolute" src="{{ asset('frontend/assets/img/shape/map-shape-2.png') }}"
            alt="shape">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="tg-chose-content mb-25">
                        <div class="tg-chose-section-title mb-30">
                            <h5 class="tg-section-subtitle mb-15 wow fadeInUp" data-wow-delay=".3s"
                                data-wow-duration=".1s">
                                {{ getTranslatedValue($home1_why_choose, 'sub_title') }}
                            </h5>
                            <h2 class="mb-15 text-capitalize wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".9s">
                                {!! strip_tags(clean(getTranslatedValue($home1_why_choose, 'title')), '<br>') !!}
                            </h2>
                            <p class="text-capitalize wow fadeInUp" data-wow-delay=".5s" data-wow-duration=".9s">
                                {!! strip_tags(clean(getTranslatedValue($home1_why_choose, 'description')), '<br>') !!}
                            </p>
                        </div>
                        <div class="tg-chose-list-wrap">
                            @if (count($translatedSlides) > 0)
                                @foreach ($translatedSlides as $key => $slide)
                                    <div class="tg-chose-list d-flex mb-15 wow fadeInUp" data-wow-delay=".6s"
                                        data-wow-duration=".9s">
                                        <div class="tg-chose-list-icon mr-20">
                                            <img src="{{ asset($slide['image']) }}" alt="image">
                                        </div>
                                        <div class="tg-chose-list-content">
                                            @isset($slide['title'])
                                                <h4 class="tg-chose-list-title mb-5">{{ $slide['title'] }}</h4>
                                            @endisset
                                            @isset($slide['sub_title'])
                                                <p>{{ $slide['sub_title'] }}</p>
                                            @endisset
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="tg-chose-btn wow fadeInUp" data-wow-delay=".8s" data-wow-duration=".9s">
                                <a href="{{ getTranslatedValue($home1_why_choose, 'button_url') }}"
                                    class="tg-btn tg-btn-switch-animation">
                                    <span class="d-flex align-items-center justify-content-center">
                                        <span
                                            class="btn-text">{{ getTranslatedValue($home1_why_choose, 'button_text') }}</span>
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
                <div class="col-lg-7">
                    <div class="tg-chose-right mb-25">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="tg-chose-thumb">
                                    <img class="tg-chose-shape-2 mb-30 ml-15 d-none d-lg-block"
                                        src="{{ asset('frontend/assets/img/shape/star-2.png') }}" alt="shape">
                                    <img class="w-100 wow fadeInRight" data-wow-delay=".4s" data-wow-duration=".9s"
                                        src="{{ asset(getSingleImage($home1_why_choose, 'small_image')) }}"
                                        alt="chose">
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-6">
                                <div class="tg-chose-thumb-inner p-relative">
                                    <div class="tg-chose-thumb-2 wow fadeInRight" data-wow-delay=".5s"
                                        data-wow-duration=".9s">
                                        <img class="w-100 tg-round-15"
                                            src="{{ asset(getSingleImage($home1_why_choose, 'large_image')) }}"
                                            alt="chose">
                                    </div>
                                    <div class="tg-chose-big-text d-none d-xl-block">
                                        <h2
                                            data-text="{{ getTranslatedValue($home1_why_choose, 'right_side_title') }}">
                                            {{ getTranslatedValue($home1_why_choose, 'right_side_title') }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-chose-us-area-end -->
@endif
