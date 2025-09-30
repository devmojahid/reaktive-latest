@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- tg-team-details-area-start -->
    <div class="tg-team-details-area pt-130 p-relative z-index-1 pb-90">
        <img class="tg-team-shape-2 d-none d-md-block" src="assets/img/banner/banner-2/shape.png" alt="">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="tg-team-details-thumb mb-30">
                        <img class="w-100" src="{{ asset($team->image_details ?? $team->image) }}" alt="{{ $team->translate->name }}">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="tg-team-details-contant-wrap ml-40 mr-80 mb-30">
                        <div class="tg-team-content tg-team-details-head mb-30">
                            <h5>{{ $team->translate->name }}</h5>
                            <span>{{ $team->translate->designation }}</span>
                            <div class="tg-team-social">
                                <a target="_blank" href="{{ $team->facebook }}"><i class="fa-brands fa-facebook-f"></i></a>
                                <a target="_blank" href="{{ $team->twitter }}"><i class="fa-brands fa-twitter"></i></a>
                                <a target="_blank" href="{{ $team->instagram }}"><i class="fa-brands fa-instagram"></i></a>
                                <a target="_blank" href="{{ $team->pinterest }}"><i
                                        class="fa-brands fa-pinterest-p"></i></a>
                            </div>
                        </div>
                        <div class="tg-team-details-contant">
                            <h6 class="mb-15">{{ __('translate.About Me') }}:</h6>
                            <p>
                                {!! strip_tags(clean($team->translate->description)) !!}
                            </p>

                            <h6 class="mb-15">{{ __('translate.Professional Skills') }} : </h6>
                            <p class="mb-30">
                                {!! strip_tags(clean($team->translate->skill_short_description)) !!}
                            </p>

                            @if ($team->translate->skill_list && count($team->translate->skill_list) > 0)
                                <div class="tg-team-progress-wrap fix mb-15">
                                    @foreach ($team->translate->skill_list as $key => $skill)
                                        <div class="tg-team-single-progress mb-20">
                                            <h5 class="tg-team-progress-title">{{ $skill['title'] }}</h5>
                                            <div class="tg-team-progress">
                                                <div class="progress-bar wow slideInLeft" data-wow-duration="2s"
                                                    data-wow-delay=".1s" role="progressbar"
                                                    data-width="{{ $skill['percentage'] ?? 0 }}%"
                                                    aria-valuenow="{{ $skill['percentage'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    <span>{{ $skill['percentage'] }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <h6 class="mb-15">{{ __('translate.Information') }}:</h6>
                            <p class="mb-25">
                                {!! strip_tags(clean($team->translate->information)) !!}
                            </p>
                            <div class="tg-team-details-contact-info">
                                <div class="row row-cols-sm-2 row-cols-1">
                                    <div class="col">
                                        <div class="tg-team-details-contact">
                                            <span>{{ __('translate.Phone') }} :</span>
                                            <a href="tel:{{ $team->phone_number }}">{{ $team->phone_number }}</a>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="tg-team-details-contact">
                                            <span>{{ __('translate.Website') }} : </span>
                                            <a href="{{ getLink($team->website) }}">{{ $team->website }}</a>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="tg-team-details-contact">
                                            <span>{{ __('translate.E-mail') }} : </span>
                                            <a href="mailto:{{ $team->mail }}">{{ $team->mail }}</a>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="tg-team-details-contact">
                                            <span>{{ __('translate.Address') }} :</span>
                                            <a href="#"> {{ $team->address }} </a>
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
    <!-- tg-team-details-area-end -->
@endsection

@push('style_section')
    <style>
        .tg-team-details-thumb.mb-30 {
            background: #efefef;
            border-radius: 15px;
        }
    </style>
@endpush
