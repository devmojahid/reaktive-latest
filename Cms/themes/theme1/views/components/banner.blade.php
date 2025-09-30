@php
    $home1_banner = getContent('theme1_banner.content', true);
@endphp


@if (!empty($home1_banner))
    <!-- tg-banner-area-start -->
    <div class="tg-banner-area tg-banner-space">
        <div class="container">
            <div class="row gx-0">
                <div class="col-lg-7">
                    <div class="tg-banner-video-wrap include-bg"
                        data-background="{{ asset(getSingleImage($home1_banner, 'background_image')) }}">
                        <div class="tg-banner-video-inner text-center">
                            <a class="tg-video-play popup-video tg-pulse-border"
                                href="{{ getTranslatedValue($home1_banner, 'youtube_video_link') }}">
                                <span class="p-relative z-index-11">
                                    <svg width="19" height="21" viewBox="0 0 19 21" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.3616 8.34455C19.0412 9.31425 19.0412 11.7385 17.3616 12.7082L4.13504 20.3445C2.45548 21.3142 0.356021 20.1021 0.356021 18.1627L0.356022 2.89C0.356022 0.950609 2.45548 -0.261512 4.13504 0.708185L17.3616 8.34455Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="tg-banner-content p-relative z-index-1 text-center">
                        <img class="tg-banner-shape" src="{{ asset('frontend/assets/img/shape/tree-2.png') }}"
                            alt="shape">
                        <h4 class="tg-banner-subtitle mb-10">{{ getTranslatedValue($home1_banner, 'sub_title') }}</h4>
                        <h2 class="tg-banner-title mb-25">{{ getTranslatedValue($home1_banner, 'title') }}</h2>
                        <div class="tg-banner-btn">
                            <a href="{{ getTranslatedValue($home1_banner, 'button_url') }}"
                                class="tg-btn tg-btn-switch-animation">
                                <span class="d-flex align-items-center justify-content-center">
                                    <span class="btn-text">{{ getTranslatedValue($home1_banner, 'button_text') }}</span>
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
        </div>
    </div>
    <span class="tg-banner-transparent-bg"></span>
    <!-- tg-banner-area-end -->
@endif
