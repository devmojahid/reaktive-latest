@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- tg-team-area-start -->
    <div class="tg-team-area pt-130 pb-100 p-relative z-index-1">
        <img class="tg-team-shape d-none d-md-block" src="{{ asset('frontend/assets/img/shape/hill.png') }}" alt="">
        <img class="tg-team-shape-2 d-none d-md-block" src="{{ asset('frontend/assets/img/shape/tree.png') }}" alt="">
        <div class="container">
            <div class="row">
                @foreach ($teams as $key => $team)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="tg-team-wrap text-center mb-30">
                            <div class="tg-team-thumb p-relative">
                                <img class="hover-img" src="{{ asset($team->image) }}" alt="team">
                                <div class="tg-listing-2-mask">
                                    <img class="w-100" src="assets/img/listing/listing-2/shape.png" alt="">
                                </div>
                            </div>
                            <div class="tg-team-content">
                                <h5><a href="{{ route('teamPerson', $team->slug) }}">{{ $team->translate->name }}</a></h5>
                                <span>{{ $team->translate->designation }}</span>
                                <div class="tg-team-social">
                                    <a target="_blank" href="{{ $team->facebook }}"><i
                                            class="fa-brands fa-facebook-f"></i></a>
                                    <a target="_blank" href="{{ $team->twitter }}"><i class="fa-brands fa-twitter"></i></a>
                                    <a target="_blank" href="{{ $team->instagram }}"><i
                                            class="fa-brands fa-instagram"></i></a>
                                    <a target="_blank" href="{{ $team->pinterest }}"><i
                                            class="fa-brands fa-pinterest-p"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- tg-team-area-end -->
@endsection
