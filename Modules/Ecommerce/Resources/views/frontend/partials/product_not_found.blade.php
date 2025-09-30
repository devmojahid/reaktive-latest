<div class="12">
    <!-- End section -->
    <div class="row justify-content-center">
        <div class="col-xxl-6  col-xl-8 col-lg-10">
            <div class="shop_not_found text-center">
                <div class="shop_not_found_thumb">
                    <img src="{{ asset($general_setting->not_found) }}" alt="thumb">
                </div>
                <div class="shop_not_found_text">
                    <h2>{{ __('translate.Search Not Found') }}</h2>
                    <p>
                        @if (request('query'))
                            {{ __('No results found for') }} "{{ request('query') }}
                            ". {{ __('translate.Please try a different search term.') }}
                        @else
                            {{ __('translate.Not Found: It seems the saved search is no longer active.') }}
                        @endif
                        <span>{{ __('translate.Thank you') }}</span>
                    </p>
                </div>
                <a class="tg-btn" href="{{ route('product.shop') }}"
                    data-text="{{ __('translate.Back to Shop') }}">
                    <span class="btn-wraper">{{ __('translate.Back to Shop') }}</span>
                </a>
            </div>
        </div>
    </div>
    <!-- End section -->
</div>
