<!-- tg-listing-area-start -->
<div class="tg-listing-area pt-90 pb-115 p-relative z-index-9">
    <img class="tg-listing-3-shape tg-listing-4-shape d-none d-xl-block"
        src="{{ asset('frontend/assets/img/shape/tree.png') }}" alt="">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-lg-9">
                <div class="tg-location-section-title mb-40">
                    <h5 class="tg-section-subtitle mb-15 wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".9s">
                        {{ __('translate.Most Popular Tour Packages') }}
                    </h5>
                    <h2 class="mb-15 text-capitalize wow fadeInUp" data-wow-delay=".5s" data-wow-duration=".9s">
                        {{ __('translate.Our Popular Tours') }}
                    </h2>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="tg-location-3-btn text-end wow fadeInUp mb-40" data-wow-delay=".6s" data-wow-duration=".9s">
                    <a href="{{ route('front.tourbooking.services') }}"
                        class="tg-btn tg-btn-gray tg-btn-switch-animation">
                        <span class="d-flex align-items-center justify-content-center">
                            <span class="btn-text">{{ __('translate.See All Deal') }}</span>
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
        <div class="row">
            <div class="col-12">
                <div class="swiper-container tg-listing-slider p-relative fix">
                    <div class="swiper-wrapper mb-35">
                        @foreach ($popularServices as $key => $service)
                            <div class="swiper-slide">
                                <div class="tg-listing-card-item tg-listing-4-card-item mb-25">
                                    <div class="tg-listing-card-thumb tg-listing-2-card-thumb mb-15 fix p-relative">
                                        <a
                                            href="{{ route('front.tourbooking.services.show', ['slug' => $service?->slug]) }}">
                                            <img class="tg-card-border w-100"
                                                src="{{ asset($service?->thumbnail?->file_path) }}"
                                                alt="{{ $service?->thumbnail?->caption ?? $service?->translation?->title }}">

                                            @if ($service?->is_new == 1)
                                                <span class="tg-listing-item-price-discount shape"
                                                    style="background-image: url('{{ asset('frontend/assets/img/shape/price-shape-2.png') }}')">New</span>
                                            @endif

                                            @if ($service?->is_featured == 1)
                                                <span class="tg-listing-item-price-discount shape-3"
                                                    style="background-image: url('{{ asset('frontend/assets/img/shape/featured.png') }}')">
                                                    <svg width="12" height="14" viewBox="0 0 12 14"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M6.60156 1L0.601562 8.2H6.00156L5.40156 13L11.4016 5.8H6.00156L6.60156 1Z"
                                                            stroke="white" stroke-width="0.857143"
                                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    Featured
                                                </span>
                                            @endif

                                            @if ($service?->discount_price)
                                                <span class="tg-listing-item-price-discount offer-btm shape-2"
                                                    style="background-image: url('{{ asset('frontend/assets/img/shape/offter.png') }}')">Sale
                                                    Offer</span>
                                            @endif

                                        </a>
                                        <div class="tg-listing-2-price">
                                            {!! $service->price_display !!}
                                        </div>
                                    </div>
                                    <div class="tg-listing-card-content p-relative">
                                        <h4 class="tg-listing-card-title mb-5"><a
                                                href="{{ route('front.tourbooking.services.show', ['slug' => $service?->slug]) }}">
                                                {{ Str::limit($service?->translation?->title, 45) }}
                                            </a></h4>

                                        @if ($service?->location)
                                            <span class="tg-listing-card-duration-map d-inline-block">
                                                <svg width="13" height="16" viewBox="0 0 13 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.3329 6.7071C12.3329 11.2324 6.55512 15.1111 6.55512 15.1111C6.55512 15.1111 0.777344 11.2324 0.777344 6.7071C0.777344 5.16402 1.38607 3.68414 2.46962 2.59302C3.55316 1.5019 5.02276 0.888916 6.55512 0.888916C8.08748 0.888916 9.55708 1.5019 10.6406 2.59302C11.7242 3.68414 12.3329 5.16402 12.3329 6.7071Z"
                                                        stroke="currentColor" stroke-width="1.15556"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M6.55512 8.64649C7.61878 8.64649 8.48105 7.7782 8.48105 6.7071C8.48105 5.636 7.61878 4.7677 6.55512 4.7677C5.49146 4.7677 4.6292 5.636 4.6292 6.7071C4.6292 7.7782 5.49146 8.64649 6.55512 8.64649Z"
                                                        stroke="currentColor" stroke-width="1.15556"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                {{ $service?->location }}
                                            </span>
                                        @endif



                                        @include('tourbooking::front.services.ratting', [
                                            'avgRating' => $service?->active_reviews_avg_rating ?? 0,
                                            'ratingCount' => $service?->active_reviews_count ?? 0,
                                        ])

                                        <div class="tg-listing-avai d-flex align-items-center justify-content-between">
                                            <a class="tg-listing-avai-btn"
                                                href="{{ route('front.tourbooking.services.show', ['slug' => $service?->slug]) }}">Check
                                                Availability</a>
                                            <div @class([
                                                'tg-listing-item-wishlist',
                                                'active' => $service?->my_wishlist_exists == 1,
                                            ])
                                                data-url="{{ route('user.wishlist.store') }}"
                                                onclick="addToWishlist({{ $service->id }}, this, 'service')">
                                                <a href="javascript:void(0);">
                                                    <svg width="20" height="18" viewBox="0 0 20 18"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M10.5167 16.3416C10.2334 16.4416 9.76675 16.4416 9.48341 16.3416C7.06675 15.5166 1.66675 12.075 1.66675 6.24165C1.66675 3.66665 3.74175 1.58331 6.30008 1.58331C7.81675 1.58331 9.15841 2.31665 10.0001 3.44998C10.8417 2.31665 12.1917 1.58331 13.7001 1.58331C16.2584 1.58331 18.3334 3.66665 18.3334 6.24165C18.3334 12.075 12.9334 15.5166 10.5167 16.3416Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="tg-listing-4-pagination swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- tg-listing-area-end -->
