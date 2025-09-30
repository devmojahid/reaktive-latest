@extends('layout_inner_page')

@section('title')
    <title>{{ $product->translate->name }}</title>
    <meta name="title" content="{{ $product->translate->name }}">
    <meta name="description" content="{!! strip_tags(clean($product->translate->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- tg-shop-details-area-start -->
    <div class="tg-shop-details-area pt-130 pb-35">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-lg-6">
                    <div class="trips-tab-slider mb-40" data-aos="fade-up" data-aos-duration="800">
                        @if (count($product->galleries) > 0)
                            <div class="trips-tabs-container">
                                <div class="trips-tabs-wrapper">
                                    @foreach ($product->galleries as $gallery)
                                        <div id="item{{ $loop->index + 1 }}" class="tabContent">
                                            <img src="{{ asset($gallery->image) }}" alt="Image">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <ul class="trips-tabs-menu">
                                @foreach ($product->galleries as $gallery)
                                    <li @class(['active' => $loop->first])>
                                        <a href="#item{{ $loop->index + 1 }}">
                                            <img src="{{ asset($gallery->image) }}" alt="Image">
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="trips-tabs-container">
                                <div class="trips-tabs-wrapper">
                                    <div id="item1" class="tabContent active">
                                        <img src="{{ asset($product->thumbnail_image) }}" alt="Default Image">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6">
                    <div class="tg-product-details-wrapper ml-55 mr-115 mb-40">
                        <h3 class="tg-product-details-title mb-5">{{ __($product->translate->name) }}</h3>
                        <div class="tg-product-details-rating mb-20 d-flex align-items-center">
                            <div class="tg-tour-about-cus-review-star d-flex">
                                @foreach (range(1, 5) as $star)
                                    <i
                                        class="fa-sharp fa-solid fa-star {{ $star <= $product->average_rating ? 'active' : '' }}"></i>
                                @endforeach
                            </div>
                            <div class="tg-product-details-rating-count">
                                <span>
                                    ( {{ __($product->reviews_count) }}
                                    {{ __($product->reviews_count > 1 ? __('translate.Reviews') : __('translate.Review')) }}
                                    )
                                </span>
                                @if (Auth::check())
                                    <a href="javascript:void(0);"
                                        id="write-review-link">{{ __('translate.Write a Review') }}</a>
                                @endif
                            </div>
                        </div>
                        <div class="tg-product-details-price">
                            <h6 class="mb-10">{!! $product->price_display !!}</h6>
                        </div>
                        <div class="tg-product-details-availability mb-20">
                            <span class="availability">{{ __('translate.Availability') }}:</span>
                            @if ($product->is_in_stock == 1)
                                <span class="stock">
                                    {{ __('translate.In Stock') }}
                                </span>
                            @else
                                <span class="stock-out">
                                    {{ __('translate.Stock Out') }}
                                </span>
                            @endif
                        </div>
                        <p class="tg-product-details-para mb-20">
                            {!! clean($product->translate?->short_description) !!}
                        </p>

                        @if ($product->is_in_stock == 1)
                            <div class="tg-product-details-quantity mb-30">
                                <span class="quantity mb-5 d-inline-block">{{ __('translate.Quantity') }}</span>
                                <div class="tg-booking-quantity-item">
                                    <span class="decrement decrement2">
                                        <svg width="14" height="2" viewBox="0 0 14 2" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1H13" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <input name="quantity" class="tg-quantity-input" type="text" value="1">
                                    <span class="increment increment2">
                                        <svg width="15" height="14" viewBox="0 0 15 14" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.21924 7H13.3836" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M7.30176 13V1" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="tg-product-details-button mb-25">
                            @if ($product->is_in_stock == 1)
                                <a data-url="{{ route('cart.add') }}" onclick="addToCart({{ $product->id }}, this)"
                                    class="tg-btn mb-10" href="javascript:void(0);">
                                    <span>
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M1.16699 1.66669H4.72255L7.10477 13.5689C7.18605 13.9782 7.40869 14.3458 7.7337 14.6074C8.05871 14.869 8.46539 15.008 8.88255 15H17.5225C17.9397 15.008 18.3464 14.869 18.6714 14.6074C18.9964 14.3458 19.219 13.9782 19.3003 13.5689L20.7225 6.11113H5.61144M9.16699 19.4445C9.16699 19.9354 8.76902 20.3334 8.2781 20.3334C7.78718 20.3334 7.38921 19.9354 7.38921 19.4445C7.38921 18.9535 7.78718 18.5556 8.2781 18.5556C8.76902 18.5556 9.16699 18.9535 9.16699 19.4445ZM18.9448 19.4445C18.9448 19.9354 18.5468 20.3334 18.0559 20.3334C17.565 20.3334 17.167 19.9354 17.167 19.4445C17.167 18.9535 17.565 18.5556 18.0559 18.5556C18.5468 18.5556 18.9448 18.9535 18.9448 19.4445Z"
                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    {{ __('translate.Add to cart') }}
                                </a>
                            @endif
                            <a class="tg-btn tg-btn-2 mb-10" href="javascript:void(0)"
                                data-url="{{ route('user.wishlist.store') }}"
                                onclick="addToWishlist({{ $product->id }}, this)">
                                <span>
                                    <svg width="20" height="18" viewBox="0 0 20 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.7579 2.41452C17.3097 1.96607 16.7775 1.61034 16.1917 1.36763C15.606 1.12492 14.9781 1 14.3441 1C13.71 1 13.0822 1.12492 12.4965 1.36763C11.9107 1.61034 11.3785 1.96607 10.9303 2.41452L10 3.34476L9.06979 2.41452C8.16439 1.50912 6.93641 1.00047 5.65598 1.00047C4.37555 1.00047 3.14757 1.50912 2.24217 2.41452C1.33677 3.31992 0.828125 4.5479 0.828125 5.82833C0.828125 7.10875 1.33677 8.33674 2.24217 9.24214L10 17L17.7579 9.24214C18.2063 8.79391 18.5621 8.26171 18.8048 7.67596C19.0475 7.0902 19.1724 6.46237 19.1724 5.82833C19.1724 5.19428 19.0475 4.56645 18.8048 3.9807C18.5621 3.39494 18.2063 2.86275 17.7579 2.41452Z"
                                            stroke="#560CE3" stroke-width="1.4" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>

                                @if ($product->my_wishlist_exists == true)
                                    {{ __('translate.Remove from Wishlist') }}
                                @else
                                    {{ __('translate.Add to Wishlist') }}
                                @endif
                            </a>
                        </div>
                        <div class="tg-product-details-share">
                            <span>{{ __('translate.Social Share') }}:</span>

                            @php
                                $productUrl = route('product.view', $product->slug); // adjust route name if needed
                                $productTitle = $product->translate->name;
                            @endphp

                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($productUrl) }}"
                                target="_blank">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>

                            <!-- Twitter -->
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode($productUrl) }}&text={{ urlencode($productTitle) }}"
                                target="_blank">
                                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.33161 6.77486L15.1688 0H13.7856L8.71722 5.8826L4.66907 0H0L6.12155 8.89546L0 16H1.38336L6.73581 9.78785L11.0109 16H15.68L9.33148 6.77486H9.33187H9.33161ZM7.43696 8.97374L6.81669 8.088L1.88171 1.03969H4.00634L7.98902 6.72789L8.60929 7.61362L13.7863 15.0074H11.6616L7.43709 8.974V8.97361L7.43696 8.97374Z"
                                        fill="currentColor" />
                                </svg>
                            </a>

                            <!-- LinkedIn -->
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($productUrl) }}"
                                target="_blank">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>

                            <!-- YouTube (Note: YouTube is not for sharing URLs — maybe use WhatsApp or Copy Link instead) -->
                            <a href="https://wa.me/?text={{ urlencode($productTitle . ' ' . $productUrl) }}"
                                target="_blank">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-shop-details-area-end -->

    <!-- product details tab area start -->
    <section class="tg-product-details-tab-area pb-100">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="tg-product-details-tab-nav mb-25">
                        <div class="tg-product-details-tab-nav-inner nav cm-tab-menu d-flex c-relative flex-wrap"
                            id="nav-tab-info" role="tablist">
                            <button class="nav-link active" id="nav-desc-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-desc" type="button" role="tab" aria-controls="nav-desc"
                                aria-selected="true">{{ __('translate.Product Description') }}</button>

                            @if ($product?->translate?->specification)
                                <button class="nav-link" id="nav-additional-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-additional" type="button" role="tab"
                                    aria-controls="nav-additional" aria-selected="false">
                                    {{ __('translate.Specification') }}
                                </button>
                            @endif
                            <button class="nav-link" id="nav-review-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review"
                                aria-selected="false">
                                ({{ __($product->reviews_count) }})
                                {{ __($product->reviews_count > 1 ? __('translate.Reviews') : __('translate.Review')) }}</button>

                        </div>
                    </div>
                    <div class="product__details-tab-content">
                        <div class="tab-content" id="nav-tabContent-info">
                            <div class="tab-pane fade show active" id="nav-desc" role="tabpanel"
                                aria-labelledby="nav-desc-tab">
                                <div class="tg-product-details-description-content editor_table">
                                    {!! $product->translate?->description !!}
                                </div>
                            </div>
                            @if ($product?->translate?->specification)
                                <div class="tab-pane fade" id="nav-additional" role="tabpanel"
                                    aria-labelledby="nav-additional-tab">
                                    <div class="tg-product-details-additionals">
                                        <div class="row">
                                            <div class="col-xl-9">
                                                <div class="p-4 border editor_table">
                                                    {!! $product?->translate?->specification !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                                <div class="tg-product-details-review">
                                    <div class="row">
                                        <div class="col-xl-9">
                                            <div class="tg-product-details-inner">
                                                <div class="tg-tour-about-review-wrap mb-45">
                                                    <h4 class="tg-tour-about-title mb-15">
                                                        {{ __('translate.Customer Reviews') }}</h4>
                                                    <p class="text-capitalize lh-28 mb-20">
                                                        {{ __('translate.Review_short_description') }} </p>
                                                </div>
                                                <div class="tg-tour-about-border mb-35"></div>
                                                <div class="tg-tour-about-cus-review-wrap mb-25">
                                                    <h4 class="tg-tour-about-title mb-40">
                                                        {{ __($product->reviews_count) }}
                                                        {{ __($product->reviews_count > 1 ? __('translate.Reviews') : __('translate.Review')) }}
                                                    </h4>
                                                    <ul>
                                                        @foreach ($productReviews as $review)
                                                            <li>
                                                                <div class="tg-tour-about-cus-review d-flex mb-40">
                                                                    <div class="tg-tour-about-cus-review-thumb">
                                                                        @if ($review->user && $review->user->image)
                                                                            <img src="{{ asset($review->user->image) }}"
                                                                                alt="thumb">
                                                                        @else
                                                                            <img src="{{ asset($general_setting->default_avatar) }}"
                                                                                alt="thumb">
                                                                        @endif
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="tg-tour-about-cus-name mb-5 d-flex align-items-center justify-content-between flex-wrap">
                                                                            <h6 class="mr-10 mb-10 d-inline-block">
                                                                                {{ html_decode($review->user->name) }}
                                                                                <span>-{{ \Carbon\Carbon::parse($review->created_at)->format('d M, Y . h:i A') }}</span>
                                                                            </h6>
                                                                            <span
                                                                                class="tg-tour-about-cus-review-star mb-10 d-inline-block">
                                                                                @foreach (range(1, 5) as $star)
                                                                                    <i
                                                                                        class="fa-sharp fa-solid fa-star @if ($review->rating >= $star) active @endif"></i>
                                                                                @endforeach
                                                                            </span>
                                                                        </div>
                                                                        <p class="text-capitalize lh-28 mb-10">
                                                                            {!! clean($review->reviews) !!}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="tg-tour-about-border mb-40"></div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>

                                                @if (auth()->user())
                                                    <div class="tg-tour-about-review-form-wrap">
                                                        <h4 class="tg-tour-about-title mb-15">
                                                            {{ __('translate.Leave a Reply') }}</h4>
                                                        <div class="tg-tour-about-rating-category mb-10">
                                                            <ul class="ms-0">
                                                                <li class="mb-0">
                                                                    <label>{{ __('translate.Ratings') }} :</label>
                                                                    <div class="rating-icon" id="rating-stars">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <i class="fa-sharp fa-solid fa-star"
                                                                                data-rating="{{ $i }}"></i>
                                                                        @endfor
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <!-- Selected rating output (optional) -->
                                                        <p class="mt-2">{{ __('translate.Selected Rating') }}: <span
                                                                id="selected-rating">0</span>/5</p>
                                                        <div class="tg-tour-about-review-form">
                                                            <form class="write_review_box_form" method="POST"
                                                                action="{{ route('user-order.reviewSubmit') }}"
                                                                id="review-form">
                                                                @csrf
                                                                <input type="hidden" name="rating" id="product_rating"
                                                                    value="0">
                                                                <input type="hidden" name="product_id"
                                                                    value="{{ $product->id }}">

                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <textarea name="reviews" id="reviews" class="textarea mb-20" placeholder="Write Message"></textarea>
                                                                        <button id="submit-review" type="submit"
                                                                            class="tg-btn tg-btn-switch-animation">
                                                                            {{ __('translate.Submit Review') }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- product details tab area end -->

    <section class="related-product-area pb-100">
        <div class="container">
            <h2 class="tg-product-details-title mb-20 pb-20">Related Products</h2>
            <div class="row">
                @foreach ($relatedProducts as $relatedProduct)
                    <div class="col-xl-3">
                        @include('ecommerce::frontend.partials.product_item', [
                            'product' => $relatedProduct,
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    @push('style_section')
        <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
        <style>
            ul.trips-tabs-menu li a {
                border: 2px solid transparent;
            }

            ul.trips-tabs-menu li.active a {
                border-color: var(--tg-theme-primary);
            }
        </style>
    @endpush

    @push('js_section')
        <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
        <script src="{{ asset('frontend/assets/js/tabs-slider.js') }}"></script>
        <script>
            "use strict";

            AOS.init();

            function listingReview() {
                document.querySelectorAll('#rating-stars i').forEach(star => {
                    star.addEventListener('click', function() {
                        const rating = this.getAttribute('data-rating');
                        document.getElementById('product_rating').value = rating;
                        document.getElementById('selected-rating').textContent = rating;

                        // Highlight stars up to the selected one
                        document.querySelectorAll('#rating-stars i').forEach(s => {
                            s.classList.remove('active');
                            if (parseInt(s.getAttribute('data-rating')) <= rating) {
                                s.classList.add('active');
                            }
                        });
                    });
                });
            }
            listingReview();

            @if (auth()->user())
                document.getElementById('submit-review').addEventListener('click', function(e) {
                    e.preventDefault();

                    const reviewForm = document.getElementById('review-form');
                    const reviews = document.getElementById('reviews').value;
                    const rating = document.getElementById('product_rating').value;

                    if (!reviews.trim()) {
                        toastr.error('{{ __('Please write your review before submitting.') }}');
                        return;
                    }

                    if (rating === '0') {
                        toastr.error('{{ __('Please select a rating before submitting.') }}');
                        return;
                    }

                    const formData = new FormData(reviewForm);

                    fetch(reviewForm.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success('{{ __('Your review has been submitted successfully.') }}');

                                // Reset the form
                                reviewForm.reset();

                                // Reset rating value and UI
                                document.getElementById('product_rating').value = '0';
                                document.getElementById('selected-rating').textContent = '0';

                                // Remove active classes from stars
                                const stars = document.querySelectorAll('#rating-stars i');
                                stars.forEach(star => star.classList.remove('active'));
                            } else {
                                toastr.error(data.message || '{{ __('An error occurred. Please try again.') }}');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('{{ __('An error occurred. Please try again later.') }}');
                        });
                });

                document.getElementById('write-review-link').addEventListener('click', function() {
                    const tabButton = document.getElementById('nav-review-tab');
                    const reviewTabContent = document.getElementById('nav-review');

                    if (tabButton && reviewTabContent) {
                        // Activate tab
                        tabButton.click();

                        // Scroll to tab content smoothly
                        setTimeout(() => {
                            reviewTabContent.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 300); // Small delay to allow tab switch
                    }
                });
            @endif
        </script>
    @endpush

@endsection