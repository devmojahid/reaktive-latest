@extends('layout_inner_page')

@section('title')
    <title>Services</title>
    <meta name="title" content="Services">
    <meta name="description" content="Services">
@endsection

@section('front-content')
    <!-- main-area -->
    <main>

        <!-- tg-breadcrumb-area-start -->
        <div class="tg-breadcrumb-spacing-3 include-bg p-relative fix"
             data-background="{{ asset($general_setting->secondary_breadcrumb_image ?? $general_setting->breadcrumb_image) }}">
            <div class="tg-hero-top-shadow"></div>
        </div>
        <div class="tg-breadcrumb-list-2-wrap">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="tg-breadcrumb-list-2">
                            <ul>
                                <li><a href="{{ url('home') }}">{{ __('translate.Home') }}</a></li>
                                <li><i class="fa-sharp fa-solid fa-angle-right"></i></li>
                                <li><a href="{{ route('front.tourbooking.services') }}">{{ __('translate.Services') }}</a></li>
                                <li><i class="fa-sharp fa-solid fa-angle-right"></i></li>
                                <li><span>{{ $service?->translation?->title }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- tg-breadcrumb-area-end -->


        <!-- tg-tour-details-area-start -->
        <div class="tg-tour-details-area pt-35 pb-25">
            <div class="container">
                <div class="row align-items-end mb-35">
                    <div class="col-xl-9 col-lg-8">
                        <div class="tg-tour-details-video-title-wrap">
                            <h2 class="tg-tour-details-video-title mb-15">
                                {{ $service?->translation?->title }}
                            </h2>
                            <div class="tg-tour-details-video-location d-flex flex-wrap">

                                @if ($service?->country || $service?->location)
    <span class="mr-25">
        <i class="fa-regular fa-location-dot"></i>
        @php
            $parts = [];
            if (!empty($service?->country)) { $parts[] = $service->country; }
            if (!empty($service?->location))    { $parts[] = $service->location; }
        @endphp
        {{ implode(', ', $parts) }}
    </span>
@endif


                                <div class="tg-tour-details-video-ratings">
                                    @foreach (range(1, 5) as $star)
                                        <i class="fa-sharp fa-solid fa-star @if ($avgRating >= $star) active @endif"></i>
                                    @endforeach
                                    <span class="review">
                                        ({{ __($reviews->count()) }}
                                        {{ __($reviews->count() > 1 ? __('translate.Reviews') : __('translate.Review')) }})
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                    @php
                        // --- FIX 1: decodăm JSON-ul age_categories ca să putem afișa categoriile ---
                        $ageCatsRaw = is_array($service->age_categories)
                            ? $service->age_categories
                            : (json_decode($service->age_categories ?? '[]', true) ?: []);
                        $enabledAgeCats = collect($ageCatsRaw)->filter(fn($c) => !empty($c['enabled']));
                        $hasAgePricing = $enabledAgeCats->count() > 0;
                        $minAgePrice = $hasAgePricing
                            ? collect($enabledAgeCats)
                                ->pluck('price')
                                ->filter(fn($p) => $p !== null && $p !== '' && is_numeric($p))
                                ->min()
                            : null;
                    @endphp

                    <div class="col-xl-3 col-lg-4">
                        <div class="tg-tour-details-video-share text-end">
                            <a class="d-none" href="#">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.87746 9.03227L10.7343 11.8625M10.7272 4.05449L5.87746 6.88471M14.7023 2.98071C14.7023 4.15892 13.7472 5.11405 12.569 5.11405C11.3908 5.11405 10.4357 4.15892 10.4357 2.98071C10.4357 1.80251 11.3908 0.847382 12.569 0.847382C13.7472 0.847382 14.7023 1.80251 14.7023 2.98071ZM6.16901 7.95849C6.16901 9.1367 5.21388 10.0918 4.03568 10.0918C2.85747 10.0918 1.90234 9.1367 1.90234 7.95849C1.90234 6.78029 2.85747 5.82516 4.03568 5.82516C5.21388 5.82516 6.16901 6.78029 6.16901 7.95849ZM14.7023 12.9363C14.7023 14.1145 13.7472 15.0696 12.569 15.0696C11.3908 15.0696 10.4357 14.1145 10.4357 12.9363C10.4357 11.7581 11.3908 10.8029 12.569 10.8029C13.7472 10.8029 14.7023 11.7581 14.7023 12.9363Z"
                                          stroke="currentColor" stroke-width="0.977778" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Share
                            </a>
                            <a
                                @class(['tg-listing-item-wishlist ml-25', 'active' => $service?->my_wishlist_exists == 1])
                                data-url="{{ route('user.wishlist.store') }}"
                                onclick="addToWishlist({{ $service->id }}, this, 'service')"
                                href="javascript:void(0);"
                            >
                                <svg width="16" height="14" viewBox="0 0 16 14" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.2606 10.7831L10.2878 10.8183L10.2606 10.7831L10.2482 10.7928C10.0554 10.9422 9.86349 11.0909 9.67488 11.2404C9.32643 11.5165 9.01846 11.7565 8.72239 11.9304C8.42614 12.1044 8.19324 12.1804 7.99978 12.1804C7.80633 12.1804 7.57342 12.1044 7.27718 11.9304C6.9811 11.7565 6.67312 11.5165 6.32472 11.2404C6.13618 11.091 5.94436 10.9423 5.75159 10.7929L5.73897 10.7831C4.90868 10.1397 4.06133 9.48294 3.36178 8.6911C2.51401 7.73157 1.92536 6.61544 1.92536 5.16811C1.92536 3.75448 2.71997 2.57143 3.80086 2.07481C4.84765 1.59384 6.26028 1.71692 7.61021 3.12673L7.64151 3.09675L7.61021 3.12673C7.7121 3.23312 7.85274 3.2933 7.99978 3.2933C8.14682 3.2933 8.28746 3.23312 8.38936 3.12673L8.35868 3.09736L8.38936 3.12673C9.73926 1.71692 11.1519 1.59384 12.1987 2.07481C13.2796 2.57143 14.0742 3.75448 14.0742 5.16811C14.0742 6.61544 13.4856 7.73157 12.6378 8.69109L12.668 8.71776L12.6378 8.6911C11.9382 9.48294 11.0909 10.1397 10.2606 10.7831ZM5.10884 11.6673L5.13604 11.6321L5.10884 11.6673L5.10901 11.6674C5.29802 11.8137 5.48112 11.9554 5.65523 12.0933C5.99368 12.3616 6.35981 12.6498 6.73154 12.8682L6.75405 12.8298L6.73154 12.8682C7.10315 13.0864 7.53174 13.2667 7.99978 13.2667C8.46782 13.2667 8.89641 13.0864 9.26802 12.8682L9.24552 12.8298L9.26803 12.8682C9.63979 12.6498 10.0059 12.3615 10.3443 12.0933C10.5185 11.9553 10.7016 11.8136 10.8907 11.6673L10.8907 11.6673L10.8926 11.6659C11.7255 11.0212 12.6722 10.2884 13.4463 9.41228L13.413 9.38285L13.4463 9.41227C14.4145 8.31636 15.1553 6.95427 15.1553 5.16811C15.1553 3.34832 14.1308 1.76808 12.6483 1.08693C11.2517 0.445248 9.53362 0.635775 7.99979 1.99784C6.46598 0.635775 4.74782 0.445248 3.35124 1.08693C1.86877 1.76808 0.844227 3.34832 0.844227 5.16811C0.844227 6.95427 1.58502 8.31636 2.55325 9.41227C3.32727 10.2883 4.27395 11.0211 5.10682 11.6657L5.10884 11.6673Z"
                                          fill="currentColor" stroke="currentColor" stroke-width="0.0888889"/>
                                </svg>
                                <span class="wishlist_change_text">
                                    @if ($service?->my_wishlist_exists == 1)
                                        Remove
                                    @else
                                        Add
                                    @endif
                                    to Wishlist
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                @php
                    $thumbnails = $service->media->where('is_thumbnail', 1)->sortBy('display_order')->values();
                    $nonThumbnails = $service->media->where('is_thumbnail', 0)->sortBy('display_order')->values();
                @endphp

                <div class="row gx-15 mb-25">
                    {{-- Left side: Big image (first thumbnail) --}}
                    <div class="col-lg-7">
                        <div class="tg-tour-details-video-thumb mb-15">
                            @if (isset($thumbnails[0]))
                                <img class="w-100" src="{{ asset($thumbnails[0]->file_path) }}" alt="{{ $thumbnails[0]->caption }}">
                            @else
                                <img class="w-100" src="{{ asset('frontend/assets/img/shape/placeholder.png') }}" alt="default">
                            @endif
                        </div>
                    </div>

                    {{-- Right side: Small images --}}
                    <div class="col-lg-5">
                        <div class="row gx-15">
                            {{-- Top-right: play button image --}}
                            <div class="col-12">
                                <div class="tg-tour-details-video-thumb p-relative mb-15">
                                    @if (isset($nonThumbnails[0]))
                                        <img class="w-100" src="{{ asset($nonThumbnails[0]->file_path) }}" alt="{{ $nonThumbnails[0]->caption }}">
                                        <div class="tg-tour-details-video-inner text-center">
                                            <a class="tg-video-play popup-video tg-pulse-border" href="{{ $service->video_url }}">
                                                <span class="p-relative z-index-11">
                                                    <svg width="19" height="21" viewBox="0 0 19 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17.3616 8.34455C19.0412 9.31425 19.0412 11.7385 17.3616 12.7082L4.13504 20.3445C2.45548 21.3142 0.356021 20.1021 0.356021 18.1627L0.356022 2.89C0.356022 0.950609 2.45548 -0.261512 4.13504 0.708185L17.3616 8.34455Z"
                                                              fill="currentColor"/>
                                                    </svg>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom-right: two smaller images --}}
                            @for ($i = 1; $i <= 2; $i++)
                                @if (isset($nonThumbnails[$i]))
                                    <div class="col-lg-6 col-md-6">
                                        <div class="tg-tour-details-video-thumb mb-15">
                                            <img class="w-100" src="{{ asset($nonThumbnails[$i]->file_path) }}" alt="{{ $nonThumbnails[$i]->caption }}">
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="tg-tour-details-feature-list-wrap">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="tg-tour-details-video-feature-list">
                                <ul>
                                    @if ($service?->duration)
                                        <li>
                                            <span class="icon">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.00001 4.19992V8.99992L12.2 10.5999M17 9C17 13.4183 13.4183 17 9 17C4.58172 17 1 13.4183 1 9C1 4.58172 4.58172 1 9 1C13.4183 1 17 4.58172 17 9Z"
                                                          stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <span class="title">{{ __('translate.Duration') }}</span>
                                                <span class="duration">{{ $service?->duration }}</span>
                                            </div>
                                        </li>
                                    @endif

                                    @if ($service?->serviceType?->name)
                                        <li>
                                            <span class="icon">
                                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.5 6.52684L4.5 2.64944M1.21001 4.70401L8.00001 8.47683L14.79 4.70401M8 16V8.46931M15 11.4578V5.48102C14.9997 5.21899 14.9277 4.96165 14.7912 4.7348C14.6547 4.50794 14.4585 4.31956 14.2222 4.18855L8.77778 1.20018C8.5413 1.06904 8.27306 1 8 1C7.72694 1 7.4587 1.06904 7.22222 1.20018L1.77778 4.18855C1.54154 4.31956 1.34532 4.50794 1.2088 4.7348C1.07229 4.96165 1.00028 5.21899 1 5.48102V11.4578C1.00028 11.7198 1.07229 11.9771 1.2088 12.204C1.34532 12.4308 1.54154 12.6192 1.77778 12.7502L7.22222 15.7386C7.4587 15.8697 7.72694 15.9388 8 15.9388C8.27306 15.9388 8.5413 15.8697 8.77778 15.7386L14.2222 12.7502C14.4585 12.6192 14.6547 12.4308 14.7912 12.204C14.9277 11.9771 14.9997 11.7198 15 11.4578Z"
                                                          stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <span class="title">{{ __('translate.Type') }}</span>
                                                <span class="duration">{{ $service?->serviceType?->name }}</span>
                                            </div>
                                        </li>
                                    @endif

                                    @if ($service?->group_size)
                                        <li>
                                            <span class="icon">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.7 17.2C1.5 17.2 1.3 17.1 1.2 17C1.1 16.8 1 16.7 1 16.5C1 15.1 1.4 13.7 2.1 12.4C2.8 11.2 3.9 10.1 5.1 9.4C4.6 8.8 4.2 8 4 7.2C3.9 6.4 3.9 5.5 4.1 4.8C4.3 4 4.8 3.2 5.3 2.6C5.9 2 6.6 1.5 7.3 1.3C7.9 1.1 8.5 1 9.1 1C9.3 1 9.6 1 9.8 1C10.6 1.1 11.4 1.4 12.1 1.9C12.8 2.4 13.3 3 13.7 3.7C14.1 4.4 14.3 5.2 14.3 6.1C14.3 7.3 13.9 8.5 13.1 9.4C13.7 9.8 14.3 10.2 14.9 10.7C15.7 11.5 16.2 12.3 16.7 13.3C17.1 14.3 17.3 15.3 17.3 16.4C17.3 16.6 17.2 16.8 17.1 16.9C17 17 16.8 17.1 16.6 17.1C16.5 17.1 16.4 17.1 16.3 17C16.2 17 16.1 16.9 16.1 16.8C16 16.7 16 16.7 15.9 16.6C15.9 16.5 15.8 16.4 15.8 16.3C15.8 15.4 15.6 14.6 15.3 13.8C15 13 14.5 12.3 13.8 11.7C13.2 11.2 12.6 10.7 11.9 10.4C11.1 10.9 10.2 11.2 9.1 11.2C8.1 11.2 7.1 10.9 6.3 10.4C5.2 10.9 4.2 11.7 3.5 12.8C2.8 13.9 2.4 15.1 2.4 16.4C2.4 16.6 2.3 16.8 2.2 16.9C2.1 17.1 1.9 17.2 1.7 17.2ZM9.1 2.5C8.4 2.5 7.7 2.7 7.1 3.1C6.4 3.5 6 4.1 5.7 4.7C5.4 5.4 5.3 6.1 5.5 6.9C5.6 7.6 6 8.3 6.5 8.8C7 9.3 7.7 9.7 8.4 9.8C8.6 9.8 8.9 9.9 9.1 9.9C9.6 9.9 10.1 9.8 10.5 9.6C11.2 9.3 11.7 8.9 12.2 8.2C12.6 7.6 12.8 6.9 12.8 6.2C12.8 5.2 12.4 4.3 11.7 3.6C11 2.8 10.1 2.5 9.1 2.5Z"
                                                          fill="currentColor"/>
                                            </span>
                                            <div>
                                                <span class="title">{{ __('translate.Group Size') }}</span>
                                                <span class="duration">{{ $service?->group_size }}</span>
                                            </div>
                                        </li>
                                    @endif

                                    @if ($service?->languages && is_array($service?->languages) && count($service?->languages) > 0)
                                        <li>
                                            <span class="icon">
                                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16 8.5C16 12.6421 12.6421 16 8.5 16M16 8.5C16 4.35786 12.6421 1 8.5 1M16 8.5H1M8.5 16C4.35786 16 1 12.6421 1 8.5M8.5 16C10.376 13.9462 11.4421 11.281 11.5 8.5C11.4421 5.71903 10.376 3.05376 8.5 1M8.5 16C6.62404 13.9462 5.55794 11.281 5.5 8.5C5.55794 5.71903 6.62404 3.05376 8.5 1M1 8.5C1 4.35786 4.35786 1 8.5 1"
                                                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <div>
                                                <span class="title">{{ __('translate.Languages') }}</span>
                                                <span class="duration">
                                                    @foreach ($service?->languages as $language)
                                                        {{ $language }}@if (!$loop->last), @endif
                                                    @endforeach
                                                </span>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="tg-tour-details-video-feature-price mb-15 text-right">
                                @if ($hasAgePricing)
                                    <p>
                                        {{ __('translate.From') }}
                                        <span>{{ currency($minAgePrice ?? 0) }}</span>
                                        / {{ __('translate.Person') }}
                                    </p>
                                @elseif ($service?->is_per_person)
                                    <p>
                                        {{ __('translate.From') }}
                                        <span>{{ currency($service->price_per_person ?? 0) }}</span>
                                        / {{ __('translate.Person') }}
                                    </p>
                                @else
                                    <div class="service-price_display">
                                        {!! $service->price_display !!}
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- tg-tour-details-area-end -->

        <!-- tg-tour-about-start -->
        <div class="tg-tour-about-area tg-tour-about-border pt-40 pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-xl-9 col-lg-8">
                        <div class="tg-tour-about-wrap mr-55">
                            <div class="tg-tour-about-content">
                                <div class="tg-tour-about-inner mb-25">
                                    <h4 class="tg-tour-about-title mb-15">{{ __('translate.About This Tour') }}</h4>
                                    <div class="text-capitalize lh-28">
                                        {!! $service?->translation?->short_description !!}
                                    </div>
                                </div>

                                @if ($service?->translation?->description)
                                    <div class="tg-tour-about-inner mb-40">
                                        {!! $service?->translation?->description !!}
                                    </div>
                                    <div class="tg-tour-about-border mb-40"></div>
                                @endif

                                @if ($service?->included || $service?->excluded)
                                    <div class="tg-tour-about-inner mb-40">
                                        <h4 class="tg-tour-about-title mb-20">Included/Exclude</h4>
                                        <div class="row">
                                            @if ($service?->included)
                                                <div class="col-lg-5">
                                                    <div class="tg-tour-about-list tg-tour-about-list-2">
                                                        <ul>
                                                            @foreach (json_decode($service?->included) as $key => $item)
                                                                <li>
                                                                    <span class="icon mr-10">
                                                                        <i class="fa-sharp fa-solid fa-check fa-fw"></i>
                                                                    </span>
                                                                    <span class="text">{{ $item }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($service?->excluded)
                                                <div class="col-lg-7">
                                                    <div class="tg-tour-about-list tg-tour-about-list-2 disable">
                                                        <ul>
                                                            @foreach (json_decode($service?->excluded) as $key => $item)
                                                                <li>
                                                                    <span class="icon mr-10">
                                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                                    </span>
                                                                    <span class="text">{{ $item }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tg-tour-about-border mb-40"></div>
                                @endif

                                <div class="tg-tour-faq-wrap mb-70">
                                    <h4 class="tg-tour-about-title mb-15">{{ __('translate.Tour Plan') }}</h4>

                                    @if ($service?->tour_plan_sub_title)
                                        <p class="text-capitalize lh-28 mb-20">{{ $service?->tour_plan_sub_title }}</p>
                                    @endif

                                    <div class="tg-tour-about-faq-inner">
                                        <div class="tg-tour-about-faq" id="accordionExample">
                                            @foreach ($service?->itineraries as $itinerary)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button
                                                            @class(['accordion-button', 'collapsed' => !$loop->first])
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse_{{ $itinerary->id }}"
                                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                            aria-controls="collapse_{{ $itinerary->id }}"
                                                        >
                                                            <span>Day-{{ $itinerary?->day_number }}</span>
                                                            {{ $itinerary?->title }}
                                                        </button>
                                                    </h2>
                                                    <div
                                                        id="collapse_{{ $itinerary->id }}"
                                                        @class(['accordion-collapse collapse', 'show' => $loop->first])
                                                        data-bs-parent="#accordionExample"
                                                    >
                                                        <div class="accordion-body">
                                                            <div class="row pb-5">
                                                                @if ($itinerary?->image)
                                                                    <div class="col-md-4 mb-5">
                                                                        <img src="{{ asset($itinerary->image) }}"
                                                                             alt="{{ $itinerary->title }}"
                                                                             class="itinerary-image">
                                                                    </div>
                                                                @endif
                                                                <div @class(['col-12 mb-5' => !$itinerary?->image, 'col-md-8 mb-5' => $itinerary?->image])>
                                                                    @if ($itinerary?->description)
                                                                        <div>{!! $itinerary?->description !!}</div>
                                                                    @endif
                                                                    @if ($itinerary?->location)
                                                                        <div class="mt-3">
                                                                            <strong><i class="fa fa-map-marker"></i> Location:</strong>
                                                                            {{ $itinerary?->location }}
                                                                        </div>
                                                                    @endif
                                                                    @if ($itinerary?->duration)
                                                                        <div class="mt-3">
                                                                            <strong><i class="fa-solid fa-business-time"></i> Duration:</strong>
                                                                            {{ $itinerary?->duration }}
                                                                        </div>
                                                                    @endif
                                                                    @if ($itinerary?->meal_included)
                                                                        <div class="mt-2">
                                                                            <strong><i class="fa fa-utensils"></i> Meal Included:</strong>
                                                                            <span class="badge bg-success">{{ $itinerary?->meal_included }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="tg-tour-about-border mb-45"></div>

                                <div class="tg-tour-about-map mb-40">
                                    <h4 class="tg-tour-about-title mb-15">{{ __('translate.Location') }}</h4>

                                    @if ($service?->google_map_sub_title)
                                        <p class="text-capitalize lh-28">{{ $service?->google_map_sub_title }}</p>
                                    @endif

                                    @if ($service?->google_map_url)
                                        <div class="tg-tour-about-map h-100">{!! $service?->google_map_url !!}</div>
                                    @endif
                                </div>

                                <div class="tg-tour-about-border mb-45"></div>

                                <div class="tg-tour-about-review-wrap mb-45">
                                    <h4 class="tg-tour-about-title mb-15">{{ __('translate.Customer Reviews') }}</h4>

                                    @if ($reviews->count() > 0)
                                        <div class="tg-tour-about-review">
                                            <div class="head-reviews">
                                                <div class="review-left">
                                                    <div class="review-info-inner">
                                                        <h2>{{ number_format($avgRating, 1) }}</h2>
                                                        <p>Based On {{ __($reviews->count()) }}
                                                            {{ __($reviews->count() > 1 ? __('translate.Reviews') : __('translate.Review')) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="review-right">
                                                    <div class="review-progress">
                                                        @foreach ($averageRatings as $item)
                                                            <div class="item-review-progress">
                                                                <div class="text-rv-progress"><p>{{ $item['category'] }}</p></div>
                                                                <div class="bar-rv-progress">
                                                                    <div class="progress">
                                                                        <div class="progress-bar" style="width: {{ $item['percent'] }}%"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-avarage"><p>{{ $item['average'] }}/5</p></div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>

                                <div class="tg-tour-about-border mb-35"></div>

                                <div class="tg-tour-about-cus-review-wrap mb-25">
                                    <h4 class="tg-tour-about-title mb-40">
                                        {{ __($reviews->count()) }}
                                        {{ __($reviews->count() > 1 ? __('translate.Reviews') : __('translate.Review')) }}
                                    </h4>
                                    <ul>
                                        @forelse ($paginatedReviews as $review)
                                            <li>
                                                <div class="tg-tour-about-cus-review d-flex mb-40">
                                                    <div class="tg-tour-about-cus-review-thumb">
                                                        <img
                                                            src="{{ asset($review->user->image ?? 'frontend/assets/img/shape/placeholder.png') }}"
                                                            alt="{{ $review->user->name }}">
                                                    </div>
                                                    <div>
                                                        <div class="tg-tour-about-cus-name mb-5 d-flex align-items-center justify-content-between flex-wrap">
                                                            <h6 class="mr-10 mb-10 d-inline-block">
                                                                {{ $review->user->name }}
                                                                <span>- {{ \Carbon\Carbon::parse($review->created_at)->format('d M, Y . h:i A') }}</span>
                                                            </h6>
                                                            <span class="tg-tour-about-cus-review-star mb-10 d-inline-block">
                                                                @foreach (range(1, 5) as $star)
                                                                    <i class="fa-sharp fa-solid fa-star @if ($review->rating >= $star) active @endif"></i>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                        <p class="text-capitalize lh-28 mb-10">{{ $review->review }}</p>
                                                    </div>
                                                </div>
                                                <div class="tg-tour-about-border mb-40"></div>
                                            </li>
                                        @empty
                                            <h5 class="text-center">{{ __('translate.No Review Found') }}</h5>
                                        @endforelse
                                    </ul>
                                    @include('components.front.custom-pagination', ['items' => $paginatedReviews])
                                </div>

                                <div id="reviewForm" x-data="reviewForm()" class="tg-tour-about-review-form-wrap mb-45">
                                    <h4 class="tg-tour-about-title mb-5">{{ __('translate.Leave a Reply') }}</h4>
                                    <div class="tg-tour-about-rating-category mb-20">
                                        <ul>
                                            <template x-for="(category, index) in categories" :key="category.name">
                                                <li>
                                                    <label x-text="category.name + ' :'" class="mr-2"></label>
                                                    <div class="rating-icon flex space-x-1">
                                                        <template x-for="star in 5" :key="star">
                                                            <i
                                                                class="fa-sharp fa-solid fa-star cursor-pointer"
                                                                :class="star <= category.rating ? 'active' : ''"
                                                                @click="setRating(index, star)"
                                                                @mouseover="hoverRating = star; hoverIndex = index"
                                                                @mouseleave="hoverRating = 0; hoverIndex = null"
                                                            ></i>
                                                        </template>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <div class="tg-tour-about-review-form">
                                        <form @submit.prevent="submitForm" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <textarea x-model="message" class="textarea mb-5" placeholder="Write Message"></textarea>
                                                    <button type="submit" class="tg-btn tg-btn-switch-animation">
                                                        {{ __('translate.Submit Review') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ========================= BOOK NOW SIDEBAR ========================= --}}
                    <div class="col-xl-3 col-lg-4">
                        <div x-data="bookingForm()" class="tg-tour-about-sidebar top-sticky mb-50">
                            <form action="{{ route('front.tourbooking.book.checkout.view') }}">
                                <h4 class="tg-tour-about-title title-2 mb-15">Book Now</h4>

                                <input type="hidden" name="service_id" value="{{ $service->id }}">

                                <div class="tg-booking-form-parent-inner mb-10">
                                    <div class="tg-tour-about-date p-relative">
                                        <input
                                            id="check_in_date"
                                            required
                                            class="input"
                                            name="check_in_date"
                                            type="text"
                                            placeholder="When (Date)"
                                            value="{{ now()->format('Y-m-d') }}"
                                        >
                                        <span class="calender"></span>
                                        <span class="angle"><i class="fa-sharp fa-solid fa-angle-down"></i></span>
                                        <input type="hidden" name="availability_id" id="selected-availability-id">
                                    </div>
                                    <div id="availability-info" class="mt-2" style="display: none;"></div>
                                </div>

                                {{-- Tickets / Age Categories --}}
                                @if ($hasAgePricing)
                                    <div class="tg-tour-about-border-doted mb-15"></div>
                                    <div class="tg-tour-about-tickets-wrap mb-15">
                                        <span class="tg-tour-about-sidebar-title">Tickets:</span>

                                        {{-- Render enabled age categories dynamically --}}
                                        <template x-for="(cfg, key) in ageConfig" :key="key">
                                            <div class="tg-tour-about-tickets mb-10">
                                                <div class="tg-tour-about-tickets-adult">
                                                    <span x-text="cfg.label"></span>
                                                    <p class="mb-0">
                                                        <span x-text="ageRangeText(cfg)"></span>
                                                        <span x-text="calculatePrice(prices[key])"></span>
                                                    </p>
                                                </div>
                                                <div class="tg-tour-about-tickets-quantity">
                                                    <select
                                                        class="item-first custom-select"
                                                        :name="'age_quantities[' + key + ']'"
                                                        x-model.number="tickets[key]"
                                                    >
                                                        <template x-for="i in 11" :key="i">
                                                            <option :value="i - 1" x-text="i - 1"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="tg-tour-about-border-doted mb-15"></div>
                                @elseif ($service->is_per_person)
                                    <div class="tg-tour-about-time d-flex align-items-center mb-10 d-none">
                                        <span class="time">Time:</span>
                                        <div class="form-check mr-15">
                                            <input type="hidden" name="check_in_time_hidden" value="{{ $service->check_in_time }}">
                                            <input class="form-check-input" name="check_in_time" type="radio" id="time1">
                                            <label class="form-check-label" for="time1">{{ $service->check_in_time }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="hidden" name="check_out_time_hidden" value="{{ $service->check_out_time }}">
                                            <input class="form-check-input" name="check_out_time" type="radio" id="time2">
                                            <label class="form-check-label" for="time2">{{ $service->check_out_time }}</label>
                                        </div>
                                    </div>

                                    <div class="tg-tour-about-border-doted mb-15"></div>

                                    <div class="tg-tour-about-tickets-wrap mb-15">
                                        <span class="tg-tour-about-sidebar-title">Tickets:</span>

                                        <div class="tg-tour-about-tickets mb-10">
                                            <div class="tg-tour-about-tickets-adult">
                                                <span>Person</span>
                                                <p class="mb-0">(18+ years)
                                                    <span x-text="calculatePrice(pricesLegacy.person)"></span>
                                                </p>
                                            </div>
                                            <div class="tg-tour-about-tickets-quantity">
                                                <select name="person" class="item-first custom-select" x-model.number="ticketsLegacy.person">
                                                    <template x-for="i in 8" :key="i">
                                                        <option :value="i" x-text="i"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="tg-tour-about-tickets mb-10">
                                            <div class="tg-tour-about-tickets-adult">
                                                <span>Children</span>
                                                <p class="mb-0">(13-17 years)
                                                    <span x-text="calculatePrice(pricesLegacy.children)"></span>
                                                </p>
                                            </div>
                                            <div class="tg-tour-about-tickets-quantity">
                                                <select name="children" class="item-first custom-select" x-model.number="ticketsLegacy.children">
                                                    <template x-for="i in 8" :key="i">
                                                        <option :value="i - 1" x-text="i - 1"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tg-tour-about-border-doted mb-15"></div>
                                @endif

                                {{-- Extras --}}
                                @if ($service->extraCharges->count() > 0)
                                    <div class="tg-tour-about-extra mb-10">
                                        <span class="tg-tour-about-sidebar-title mb-10 d-inline-block">Add Extra:</span>
                                        <div class="tg-filter-list">
                                            <ul>
                                                @foreach ($service->extraCharges as $key => $extra)
                                                    <li>
                                                        <div class="checkbox d-flex">
                                                            <input
                                                                name="extras[]"
                                                                value="{{ $extra->id }}"
                                                                class="tg-checkbox"
                                                                type="checkbox"
                                                                x-model="extras.charge_{{ $key }}"
                                                                id="charge_{{ $key }}"
                                                            >
                                                            <label for="charge_{{ $key }}" class="tg-label">{{ $extra->name }}</label>
                                                        </div>
                                                        <span class="quantity">{{ currency($extra->price) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="tg-tour-about-border-doted mb-15"></div>
                                @endif

                                {{-- Total --}}
                                @if ($hasAgePricing)
                                    <div class="tg-tour-about-coast d-flex align-items-center flex-wrap justify-content-between mb-20">
                                        <span class="tg-tour-about-sidebar-title d-inline-block">Total Cost:</span>
                                        <h5 class="total-price" x-text="calculatePrice(totalCostAge)"></h5>
                                    </div>
                                @elseif ($service->is_per_person)
                                    <div class="tg-tour-about-coast d-flex align-items-center flex-wrap justify-content-between mb-20">
                                        <span class="tg-tour-about-sidebar-title d-inline-block">Total Cost:</span>
                                        <h5 class="total-price" x-text="calculatePrice(totalCostLegacy)"></h5>
                                    </div>
                                @else
                                    <div class="mt-4 tg-tour-about-coast d-flex align-items-center flex-wrap justify-content-between mb-20">
                                        <span class="tg-tour-about-sidebar-title d-inline-block">Total Cost:</span>
                                        <h5 class="total-price">{{ currency($service->discount_price ?? $service->full_price) }}</h5>
                                    </div>
                                @endif

                                <button type="submit" class="tg-btn tg-btn-switch-animation w-100">Book now</button>
                            </form>
                        </div>
                    </div>
                    {{-- ======================= /BOOK NOW SIDEBAR ======================= --}}

                </div>
            </div>
        </div>
        <!-- tg-tour-about-end -->

        @include('tourbooking::front.services.popular-services')

    </main>
    <!-- main-area-end -->
@endsection


@push('js_section')
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>


<script>
(function ($) {
  'use strict';
  $(function () {

    function extractMoney(text) {
      const t = String(text || '');
      const m = t.match(/(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{2})|\d+(?:[.,]\d{2})?)\s*(?:€|$|£|lei|ron|usd|eur)?\s*$/i);
      if (!m) return 0;
      let n = m[1].replace(/\s+/g, '');
      n = n.replace(/\.(?=\d{3}\b)/g, '').replace(',', '.');
      const v = parseFloat(n);
      return isNaN(v) ? 0 : v;
    }

    function ticketRows($root) {
      const $rows = $root.find('[data-price]')
        .add($root.find('div,li,.d-flex,.tg-tour-about-tickets').filter(function () {
          return $(this).find('input[type="number"], input.tg-quantity-input').length > 0;
        }));
      return $rows;
    }

    function qtyFromRow($row) {
      const $inp = $row.find('input[type="number"], input.tg-quantity-input').first();
      const v = parseInt($inp.val(), 10);
      return isNaN(v) ? 0 : Math.max(0, v);
    }

    function priceFromRow($row) {
      const dp = $row.data('price');
      if (dp !== undefined) return parseFloat(String(dp).replace(',', '.')) || 0;
      const $p = $row.find('.price,.amount,.tg-price,.ticket-price').last();
      if ($p.length) return extractMoney($p.text());
      return extractMoney($row.text());
    }

    function extrasTotal($root) {
      let sum = 0;
      $root.find('input[type="checkbox"]').each(function () {
        const $cb = $(this);
        if (!$cb.is(':checked')) return;
        if ($cb.data('price') !== undefined) {
          const p = parseFloat(String($cb.data('price')).replace(',', '.')) || 0;
          sum += p;
          return;
        }
        const labelText = $cb.closest('label,li,div').text();
        sum += extractMoney(labelText);
      });
      return sum;
    }

    function currencySymbol($root) {
      const txt = $root.text();
      const m = txt.match(/(€|\$|£|lei|RON)/i);
      if (!m) return '$';
      const sym = m[1];
      return /ron|lei/i.test(sym) ? 'Lei ' : sym;
    }

    function fmt(n) {
      try {
        return new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
      } catch (e) {
        return Number(n || 0).toFixed(2);
      }
    }

    function recalc($root) {
      let total = 0;

      ticketRows($root).each(function () {
        const $row = $(this);
        const q = qtyFromRow($row);
        if (!q) return;
        const p = priceFromRow($row);
        if (p > 0) total += q * p;
      });

      total += extrasTotal($root);

      const sym = currencySymbol($root);
      $root.find('.total-price').text(sym + fmt(total));
      $root.find('input[name="total"]').val(total);
    }

    const $bookNow = $('.tg-blog-sidebar-box, .booking-sidebar, [data-book-now]').first();
    if (!$bookNow.length) return;

    recalc($bookNow);

    $bookNow.on('input change', 'input', function () {
      setTimeout(() => recalc($bookNow), 0);
    });

    let debounce;
    const mo = new MutationObserver(() => {
      clearTimeout(debounce);
      debounce = setTimeout(() => recalc($bookNow), 50);
    });
    mo.observe($bookNow[0], { childList: true, subtree: true, characterData: true });
  });
})(jQuery);
</script>




    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {
                // ========= Availability calendar (dates allowed + info bubble) =========
                $(".timepicker").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });

                const availabilities = @json($service?->availabilities ?? []);
                const availableDates = availabilities.map(item => item.date);
                const availabilityMap = {};
                availabilities.forEach(item => {
                    availabilityMap[item.date] = {
                        id: item.id,
                        spots: item.available_spots,
                        special_price: item.special_price,
                        per_children_price: item.per_children_price,
                        notes: item.notes,
                        start_time: item.start_time,
                        end_time: item.end_time,
                        is_available: item.is_available
                    };
                });

                const datePicker = flatpickr("input[name='check_in_date']", {
                    dateFormat: "Y-m-d",
                    disableMobile: "true",
                    minDate: "today",
                    enable: availableDates,
                    onChange: function (selectedDates, dateStr) {
                        updateAvailabilityInfo(dateStr);
                        document.dispatchEvent(new CustomEvent('booking-date-changed', { detail: { date: dateStr } }));
                    }
                });

                function updateAvailabilityInfo(dateStr) {
                    const availInfo = $('#availability-info');
                    const bookBtn = $('button[type="submit"]');
                    const availabilityInput = $('#selected-availability-id');

                    if (dateStr && availabilityMap[dateStr]) {
                        const info = availabilityMap[dateStr];

                        availabilityInput.val(info.id || '');

                        let html = '<div class="alert alert-info mt-2 mb-0">';

                        if (info.spots !== null && info.spots !== undefined) {
                            html += `<p class="mb-1"><strong>Available spots:</strong> ${info.spots}</p>`;
                            if (+info.spots <= 0) {
                                html += '<p class="text-danger mb-0">No spots available for this date!</p>';
                                bookBtn.prop('disabled', true);
                            } else {
                                bookBtn.prop('disabled', false);
                            }
                        } else {
                            html += '<p class="mb-1">Spots available for booking</p>';
                            bookBtn.prop('disabled', false);
                        }

                        if (info.start_time && info.end_time) {
                            html += `<p class="mb-1"><strong>Time:</strong> ${info.start_time.substring(0,5)} - ${info.end_time.substring(0,5)}</p>`;
                        }

                        if (info.special_price) {
                            html += `<p class="mb-1"><strong>Special price (adult):</strong> {{ default_currency()['currency_icon'] }}${(+info.special_price * {{ default_currency()['currency_rate'] }}).toFixed(2)}</p>`;
                        }
                        if (info.per_children_price) {
                            html += `<p class="mb-1"><strong>Child price:</strong> {{ default_currency()['currency_icon'] }}${(+info.per_children_price * {{ default_currency()['currency_rate'] }}).toFixed(2)}</p>`;
                        }

                        if (info.notes) {
                            html += `<p class="mb-0"><strong>Notes:</strong> ${info.notes}</p>`;
                        }

                        html += '</div>';
                        availInfo.html(html).show();
                    } else {
                        availInfo.hide().html('');
                        availabilityInput.val('');
                        bookBtn.prop('disabled', false);
                    }
                }

                const initialDate = $('input[name="check_in_date"]').val();
                if (initialDate) {
                    updateAvailabilityInfo(initialDate);
                }
            });
        })(jQuery);
    </script>

    {{-- AlpineJS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- === AGE_CONFIG construit în PHP și injectat în JS === --}}
    @php
        $ageCatsRawJs = is_array($service->age_categories)
            ? $service->age_categories
            : (json_decode($service->age_categories ?? '[]', true) ?: []);
        $enabledAgeCatsJs = collect($ageCatsRawJs)->filter(fn($c) => !empty($c['enabled']));
        $ageConfigForJs = $enabledAgeCatsJs
            ->mapWithKeys(function ($cfg, $key) {
                return [
                    $key => [
                        'label'         => ucfirst($key),
                        'price'         => (float)($cfg['price'] ?? 0),
                        'min_age'       => $cfg['min_age'] ?? null,
                        'max_age'       => $cfg['max_age'] ?? null,
                        // FIX: force default_count = 0 pentru a porni cu total 0 când nu s-a selectat nimic
                        'default_count' => 0,
                    ],
                ];
            })
            ->toArray();
    @endphp
    <script>
        const AGE_CONFIG = @json($ageConfigForJs);
    </script>

    <script>
        function reviewForm() {
            return {
                categories: [
                    { name: 'Location',  rating: 0 },
                    { name: 'Price',     rating: 0 },
                    { name: 'Amenities', rating: 0 },
                    { name: 'Rooms',     rating: 0 },
                    { name: 'Services',  rating: 0 }
                ],
                hoverRating: 0,
                hoverIndex: null,
                message: '',

                setRating(index, rating) {
                    this.categories[index].rating = rating;
                },

                submitForm() {
                    const data = {
                        service_id: `{{ $service->id }}`,
                        message: this.message,
                        ratings: this.categories.map(c => ({ category: c.name, rating: c.rating }))
                    };

                    if (!data.message.trim()) {
                        toastr.error('{{ __('Please write your review before submitting.') }}');
                        return;
                    }
                    if (data.ratings.some(c => c.rating === 0)) {
                        toastr.error('{{ __('Please select a rating before submitting.') }}');
                        return;
                    }

                    fetch(`{{ route('front.tourbooking.reviews.store') }}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(r => r.json())
                        .then(d => {
                            if (d.success) {
                                toastr.success(d.message);
                                this.message = '';
                                this.categories.forEach(c => c.rating = 0);
                            } else {
                                toastr.error(d.message);
                            }
                        })
                        .catch(() => toastr.error('{{ __('An error occurred. Please try again later.') }}'));
                }
            };
        }

        function bookingForm() {
            return {
                // === Currency ===
                currencyIcon: "{{ default_currency()['currency_icon'] }}",
                currencyRate: parseFloat("{{ default_currency()['currency_rate'] }}"),

                // === Extras toggles & prices ===
                extras: {
                    @foreach ($service->extraCharges as $key => $extra)
                        charge_{{ $key }}: false,
                    @endforeach
                },
                extrasPrice: {
                    @foreach ($service->extraCharges as $key => $extra)
                        charge_{{ $key }}: {{ $extra->price ?? 0 }},
                    @endforeach
                },

                // === AGE-BASED PRICING (new) ===
                ageConfig: AGE_CONFIG,
                tickets: Object.fromEntries(Object.keys(AGE_CONFIG || {}).map(k => [k, AGE_CONFIG[k].default_count || 0])),
                prices:  Object.fromEntries(Object.keys(AGE_CONFIG || {}).map(k => [k, parseFloat(AGE_CONFIG[k].price || 0)])),

                // === Legacy per-person pricing fallback ===
                ticketsLegacy: {
                    person: 1,
                    children: 0
                },
                pricesLegacy: {
                    person: {{ $service?->availabilitieByDate?->special_price ?? ($service->price_per_person ?? 0) }},
                    children: {{ $service?->availabilitieByDate?->per_children_price ?? ($service->child_price ?? 0) }}
                },

                // === Date selection state ===
                selectedDate: "{{ now()->format('Y-m-d') }}",
                loading: false,

                init() {
                    const input = document.querySelector("input[name='check_in_date']");
                    if (input && input.value) this.selectedDate = input.value;

                    document.addEventListener('booking-date-changed', (e) => {
                        const date = e.detail?.date || '';
                        if (!date) return;
                        this.selectedDate = date;
                        this.fetchAvailabilityPricing(date);
                    });

                    this.fetchAvailabilityPricing(this.selectedDate);
                },

                fetchAvailabilityPricing(dateStr) {
                    const that = this;
                    this.loadingOverlay('show');

                    $.ajax({
                        url: "{{ route('front.tourbooking.get.availity.by.date') }}",
                        method: 'GET',
                        data: {
                            service_id: `{{ $service->id }}`,
                            check_in_date: dateStr
                        },
                        success(response) {
                            if (response && response.age_categories) {
                                Object.keys(response.age_categories).forEach(function (key) {
                                    if (that.prices.hasOwnProperty(key)) {
                                        const val = parseFloat(response.age_categories[key] ?? 0);
                                        if (!isNaN(val)) that.prices[key] = val;
                                    }
                                });
                            } else {
                                if (response && (response.special_price !== undefined)) {
                                    const v = parseFloat(response.special_price);
                                    if (!isNaN(v)) that.pricesLegacy.person = v;
                                }
                                if (response && (response.per_children_price !== undefined)) {
                                    const v = parseFloat(response.per_children_price);
                                    if (!isNaN(v)) that.pricesLegacy.children = v;
                                }
                            }
                        },
                        error(err) {
                            console.error('Error fetching availability:', err);
                        },
                        complete() {
                            that.loadingOverlay('hide');
                        }
                    });
                },

                // ===== Totals =====
                get totalCostAge() {
                    let total = 0;
                    for (const key in this.tickets) {
                        const qty = Number(this.tickets[key] || 0);
                        const price = Number(this.prices[key] || 0);
                        total += qty * price;
                    }
                    for (let key in this.extras) {
                        if (this.extras[key]) total += Number(this.extrasPrice[key] || 0);
                    }
                    return Number(total.toFixed(2));
                },

                get totalCostLegacy() {
                    let total = 0;
                    total += (this.ticketsLegacy.person   || 0) * (this.pricesLegacy.person   || 0);
                    total += (this.ticketsLegacy.children || 0) * (this.pricesLegacy.children || 0);
                    for (let key in this.extras) {
                        if (this.extras[key]) total += Number(this.extrasPrice[key] || 0);
                    }
                    return Number(total.toFixed(2));
                },

                // ===== Helpers =====
                ageRangeText(cfg) {
                    const min = cfg.min_age, max = cfg.max_age;
                    if (min != null && max != null) {
                        if (Number(max) >= 120) return `(${min}+ years) `;
                        return `(${min}-${max} years) `;
                    }
                    if (min != null && (max == null || Number(max) === 0)) return `(${min}+ years) `;
                    if (max != null) return `(0-${max} years) `;
                    return '';
                },

                calculatePrice(amount) {
                    return this.currencyIcon + (this.currencyRate * Number(amount || 0)).toFixed(2);
                },

                loadingOverlay(action = 'show', target = false) {
                    const options = { size: 50, maxSize: 50, minSize: 50 };
                    if (target && typeof target === 'string') $(target).LoadingOverlay(action, options);
                    else $.LoadingOverlay(action, options);
                }
            };
        }
    </script>
@endpush

@push('style_section')
    <style>
        a.tg-listing-item-wishlist.active { color: var(--tg-theme-primary); }
        .tg-tour-about-cus-review-thumb img { height: 128px; }

        .tg-tour-details-video-ratings i { color: #a6a6a6; }
        .tg-tour-details-video-ratings i.active { color: var(--tg-common-yellow); }

        .custom-select {
            min-width: 60px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #d6d6d6;
            border-radius: 24px;
            padding: 1px 14px;
            font-weight: 400;
            font-size: 16px;
            color: var(--tg-grey-1);
        }
        .custom-select:focus { outline: none; border-color: #560CE3; }

        .calender-active.open .flatpickr-innerContainer .flatpickr-days .flatpickr-day.today,
        .flatpickr-calendar.open .flatpickr-innerContainer .flatpickr-days .flatpickr-day.selected {
            color: var(--tg-common-white) !important;
            background-color: var(--tg-theme-primary) !important;
        }
    </style>
@endpush
