<div class="tg-shop-sidebar top-sticky mb-50">
    <form action="{{ route('product.shop') }}" method="GET">
        <div class="tg-blog-sidebar-search tg-blog-sidebar-box mb-40">
            <h5 class="tg-blog-sidebar-title mb-15">{{ __('translate.Search') }}</h5>
            <div class="tg-blog-sidebar-form">
                <input type="text" name="search" value="{{ request()->get('search') }}"
                    placeholder="{{ __('translate.Type here . . .') }}">
                <button type="submit" class="product_search_btn">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_497_1336)">
                            <path
                                d="M17 17L13.5247 13.5247M15.681 8.3405C15.681 12.3945 12.3945 15.681 8.3405 15.681C4.28645 15.681 1 12.3945 1 8.3405C1 4.28645 4.28645 1 8.3405 1C12.3945 1 15.681 4.28645 15.681 8.3405Z"
                                stroke="#560CE3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_497_1336">
                                <rect width="18" height="18" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </button>
            </div>
        </div>
        <div class="tg-blog-categories tg-blog-sidebar-box mb-40">
            <h5 class="tg-blog-sidebar-title mb-5">{{ __('translate.Categories') }}</h5>
            <div class="tg-blog-categories-list mt-15">
                @foreach ($categories as $category)
                    <div class="shop_sidebar_item_box mb-10 border-bottom pb-2">
                        <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}"
                            id="category_{{ $category->id }}"
                            {{ in_array($category->id, (array) request('categories', [])) ? 'checked' : '' }}>

                        <label class="form-check-label ms-2" for="category_{{ $category->id }}">
                            {{ $category->translate->name }} ({{ $category->products_count }})
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="tg-blog-categories tg-blog-sidebar-box mb-40">
            <h5 class="tg-blog-sidebar-title mb-5">{{ __('translate.Brands') }}</h5>
            <div class="tg-blog-categories-list mt-15">
                @foreach ($brands as $brand)
                    <div class="shop_sidebar_item_box mb-10 border-bottom pb-2">
                        <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand->id }}"
                            id="brand_{{ $brand->id }}"
                            {{ in_array($brand->id, (array) request('brands', [])) ? 'checked' : '' }}>

                        <label class="form-check-label ms-2" for="brand_{{ $brand->id }}">
                            {{ $brand->translate->name }} ({{ $brand->products_count }})
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="tg-blog-post tg-blog-sidebar-box tg-shop-product-list-widget mb-40">
            <h5 class="tg-blog-sidebar-title mb-25">{{ __('translate.Best Selling Products') }}</h5>

            @foreach ($bestSellingProducts as $bp)
                <div class="tg-blog-post-item d-flex align-items-center mb-20">
                    <div class="tg-blog-post-thumb mr-15">
                        <a href="shop-details.html"><img src="{{ asset($bp->thumbnail_image) }}" alt="product"></a>
                    </div>
                    <div class="tg-blog-post-content w-100">
                        <h4 class="tg-blog-post-title mb-5">
                            <a href="{{ route('product.view', $bp->slug) }}">
                                {{ __(Str::limit($bp->translate->name, 15)) }}
                            </a>
                        </h4>
                        @include('ecommerce::frontend.partials.avg_ratting', [
                            'rating' => $bp->reviews_avg_rating,
                        ])
                        <span class="price">
                            {{ currency($bp->price) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="tg-btn d-block w-100" data-text="Apply Now">
            <span class="btn-wraper">{{ __('translate.Apply Now') }}</span>
        </button>
    </form>
</div>
