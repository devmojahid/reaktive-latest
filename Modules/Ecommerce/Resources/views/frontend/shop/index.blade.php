@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- tg-shop-area-start -->
    <div class="tg-shop-area pt-130 pb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('ecommerce::frontend.partials.product_sidebar')
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="tg-shop-product-wrap mb-50">
                        @include('ecommerce::frontend.partials.product_topbar')
                        <div class="row">
                            @forelse ($products as $product)
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                                    @include('ecommerce::frontend.partials.product_item', [
                                        'product' => $product,
                                    ])
                                </div>
                            @empty
                                @include('ecommerce::frontend.partials.product_not_found')
                            @endforelse

                            <div class="col-12">
                                @include('ecommerce::frontend.partials.product_pagination')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-shop-area-end -->
@endsection
