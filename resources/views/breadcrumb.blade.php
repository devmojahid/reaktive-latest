<div class="tg-breadcrumb-area tg-breadcrumb-spacing-5 fix p-relative z-index-1 include-bg"
    data-background="{{ asset($general_setting->breadcrumb_image) }}">
    <div class="tg-hero-top-shadow"></div>
    <div class="tg-breadcrumb-shadow"></div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="tg-breadcrumb-content text-center">
                    <h2 class="tg-breadcrumb-title mb-10 fs-40">{{ $breadcrumb_title }}</h2>
                    <div class="tg-breadcrumb-list-4">
                        <ul>
                            <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                            <li><i class="fa-sharp fa-solid fa-angle-right"></i></li>
                            <li>{{ $breadcrumb_title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tg-hero-bottom-shape d-none d-md-block">
        <span>
          @include('svg.breadcrumb_shape1')
        </span>
    </div>
    <div class="tg-hero-bottom-shape-2 d-none d-md-block">
        <span>
          @include('svg.breadcrumb_shape2')
        </span>
    </div>
</div>
