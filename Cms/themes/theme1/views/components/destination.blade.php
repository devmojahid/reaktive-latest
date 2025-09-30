@php
    $theme1_destination = getContent('theme1_destination.content', true);
    $destination_items = popularDestinations();
@endphp

<!-- tg-location-area-start -->
<div class="tg-location-area p-relative pb-40 tg-grey-bg pt-140">
    <img class="tg-location-shape d-none d-lg-block" src="{{ asset('frontend/assets/img/shape/tower.png') }}"
        alt="shape">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="tg-location-section-title text-center mb-30">
                    <h5 class="tg-section-subtitle mb-15 wow fadeInUp" data-wow-delay=".4s" data-wow-duration=".9s">
                        {{ getTranslatedValue($theme1_destination, 'sub_title') }}
                    </h5>
                    <h2 class="mb-15 text-capitalize wow fadeInUp" data-wow-delay=".5s" data-wow-duration=".9s">
                        {!! strip_tags(clean(getTranslatedValue($theme1_destination, 'title')), '<br>') !!}
                    </h2>
                    <p class="text-capitalize wow fadeInUp" data-wow-delay=".6s" data-wow-duration=".9s">
                        {!! strip_tags(clean(getTranslatedValue($theme1_destination, 'description')), '<br>') !!}
                    </p>
                </div>
            </div>
            @if ($destination_items->count() > 0)
                @foreach ($destination_items as $destination_item)
                    <div class="col-lg-3 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay=".3s" data-wow-duration=".9s">
                        <div class="bg-white tg-round-25 p-relative z-index-1">
                            <div class="tg-location-wrap p-relative mb-30">
                                <div class="tg-location-thumb">
                                    <img class="w-100"
                                        src="{{ asset($destination_item->image) }}"
                                        alt="{{ $destination_item->country }}">
                                </div>
                                <div class="tg-location-content text-center">
                                    <span class="tg-location-time">
                                        {{ $destination_item->services_count }}
                                        {{ $destination_item->services_count > 1 ? __('translate.Tours') : __('translate.Tour') }}</span>
                                    <h3 class="tg-location-title mb-0">
                                        <a
                                            href="{{ route('front.tourbooking.services', ['destination_id' => $destination_item->id, 'destination' => $destination_item->name]) }}">
                                            {{ $destination_item->country }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="tg-location-border one"></div>
                                <div class="tg-location-border two"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<!-- tg-location-area-end -->

@push('style_section')
    <style>
        .tg-location-thumb img {
            min-height: 245px;
            max-height: 245px;
        }
    </style>
@endpush
