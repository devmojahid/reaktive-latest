<div class="tg-shop-product-item mb-25">
    <div class="tg-shop-product-thumb mb-15 fix p-relative">
        <a href="{{ route('product.view', $product->slug) }}">
            <img class="w-100" src="{{ asset($product->thumbnail_image) }}" alt="{{ $product->translate->name }}">
        </a>
        <div class="tg-shop-product-btn">
            <a title="{{ __('translate.Add to wishlist') }}" class="wishlist wishlist_icon {{ $product->my_wishlist_exists ? 'active' : '' }}" href="javascript:void(0)"
                data-url="{{ route('user.wishlist.store') }}" onclick="addToWishlist({{ $product->id }}, this)">
                <svg width="18" height="16" viewBox="0 0 18 16" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.96 2.2377C15.5678 1.84531 15.1022 1.53404 14.5896 1.32168C14.0771 1.10931 13.5277 1 12.973 1C12.4182 1 11.8688 1.10931 11.3563 1.32168C10.8437 1.53404 10.3781 1.84531 9.98587 2.2377L9.17191 3.05166L8.35794 2.2377C7.56572 1.44548 6.49123 1.00041 5.37086 1.00041C4.25049 1.00041 3.176 1.44548 2.38378 2.2377C1.59155 3.02993 1.14648 4.10441 1.14648 5.22479C1.14648 6.34516 1.59155 7.41965 2.38378 8.21187L9.17191 15L15.96 8.21187C16.3524 7.81967 16.6637 7.354 16.8761 6.84146C17.0884 6.32893 17.1977 5.77958 17.1977 5.22479C17.1977 4.67 17.0884 4.12064 16.8761 3.60811C16.6637 3.09558 16.3524 2.6299 15.96 2.2377Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>

            <div class="tg-shop-product-hidden-btn">
                <a title="{{ __('translate.Add to cart') }}" href="javascript:void(0)" data-url="{{ route('cart.add') }}"
                    onclick="addToCart({{ $product->id }}, this)" class="cart-add-btn">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0.799805 4.2L3.1998 1H12.7998L15.1998 4.2M0.799805 4.2V15.4C0.799805 15.8243 0.968376 16.2313 1.26843 16.5314C1.56849 16.8314 1.97546 17 2.3998 17H13.5998C14.0242 17 14.4311 16.8314 14.7312 16.5314C15.0312 16.2313 15.1998 15.8243 15.1998 15.4V4.2M0.799805 4.2H15.1998M11.1998 7.4C11.1998 8.24869 10.8627 9.06263 10.2625 9.66274C9.66243 10.2629 8.8485 10.6 7.9998 10.6C7.15111 10.6 6.33718 10.2629 5.73706 9.66274C5.13695 9.06263 4.7998 8.24869 4.7998 7.4"
                            stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="tg-shop-product-content">
        <h3 class="tg-shop-product-title">
            <a href="{{ route('product.view', $product->slug) }}">
                {{ __($product->translate->name) }}
            </a>
        </h3>
        <div class="tg-shop-product-ratings">
            @include('ecommerce::frontend.partials.avg_ratting', [
                'rating' => $product->reviews_avg_rating,
                'class' => 'd-flex list-unstyled',
            ])
            <span>( {{ __($product->reviews_count) }}
                {{ __($product->reviews_count > 1 ? __('translate.Reviews') : __('translate.Review')) }} )</span>
        </div>
        <span class="price">
            {!! $product->price_display !!}
        </span>
    </div>
</div>
