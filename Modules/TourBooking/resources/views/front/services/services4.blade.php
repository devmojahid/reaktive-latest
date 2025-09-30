@extends('layout_inner_page')

@section('title')
    <title>Services</title>
    <meta name="title" content="Services">
    <meta name="description" content="Services">
@endsection

@section('front-content')
    <!-- tg-breadcrumb-area-start -->
    <div class="tg-breadcrumb-spacing-3 include-bg p-relative fix"
        data-background="{{ asset($general_setting->breadcrumb_image) }}">
        <div class="tg-hero-top-shadow"></div>
    </div>
    <!-- tg-breadcrumb-area-end -->


    <!-- tg-listing-map-area-start -->
    <div x-data="data" class="tg-listing-map-area fix">
        <div class="container-fluid p-0">
            <div class="row gx-0">
                <div class="col-lg-6">
                    <div class="tg-listing-map-list-wrap mb-15">
                        <div class="tg-listing-map-booking pl-40 pr-35">
                            <div class="col-12">
                                <div class="tg-booking-form-item pt-35 pb-25">
                                    <div class="tg-booking-form-input-group d-flex align-items-end justify-content-between">
                                        <div class="tg-booking-form-parent-inner tg-hero-quantity p-relative mr-15 mb-15">
                                            <span
                                                class="tg-booking-form-title mb-5">{{ __('translate.Destinations:') }}</span>
                                            <div class="tg-booking-add-input-field tg-booking-quantity-toggle">
                                                <span x-clock x-show="bookingForm.destination"
                                                    x-text="bookingForm.destination" class="tg-booking-title-value">
                                                    {{ __('translate.Where are you going . . .') }}
                                                </span>
                                                <span x-clock x-show="!bookingForm.destination"
                                                    class="tg-booking-title-value">
                                                    {{ __('translate.Where are you going . . .') }}
                                                </span>
                                                <span class="location">
                                                    <svg width="13" height="16" viewBox="0 0 13 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12.3329 6.7071C12.3329 11.2324 6.55512 15.1111 6.55512 15.1111C6.55512 15.1111 0.777344 11.2324 0.777344 6.7071C0.777344 5.16402 1.38607 3.68414 2.46962 2.59302C3.55316 1.5019 5.02276 0.888916 6.55512 0.888916C8.08748 0.888916 9.55708 1.5019 10.6406 2.59302C11.7242 3.68414 12.3329 5.16402 12.3329 6.7071Z"
                                                            stroke="currentColor" stroke-width="1.15556"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M6.55512 8.64649C7.61878 8.64649 8.48105 7.7782 8.48105 6.7071C8.48105 5.636 7.61878 4.7677 6.55512 4.7677C5.49146 4.7677 4.6292 5.636 4.6292 6.7071C4.6292 7.7782 5.49146 8.64649 6.55512 8.64649Z"
                                                            stroke="currentColor" stroke-width="1.15556"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="tg-booking-form-location-list tg-booking-quantity-active">
                                                <ul class="scrool-bar scrool-height pr-5">
                                                    @foreach ($destinations as $key => $destination)
                                                        <li
                                                            @click="selectDestination(`{{ $destination->id }}`, `{{ $destination->name }}`)">
                                                            <i class="fa-regular fa-location-dot"></i>
                                                            <span>{{ $destination->name }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tg-booking-form-parent-inner mr-15 mb-15">
                                            <span class="tg-booking-form-title mb-5">{{ __('translate.Check in:') }}</span>
                                            <div class="tg-booking-add-input-date p-relative">
                                                <input x-model="bookingForm.checkIn" class="input timepicker"
                                                    name="check_in" type="text"
                                                    placeholder="{{ __('translate.Check in') }}">
                                                <span>
                                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.76501 0.777771V3.26668M4.23413 0.777771V3.26668M0.777344 5.75548H13.2218M2.16006 2.02211H11.8391C12.6027 2.02211 13.2218 2.57927 13.2218 3.26656V11.9778C13.2218 12.6651 12.6027 13.2222 11.8391 13.2222H2.16006C1.39641 13.2222 0.777344 12.6651 0.777344 11.9778V3.26656C0.777344 2.57927 1.39641 2.02211 2.16006 2.02211Z"
                                                            stroke="#737374" stroke-width="0.977778" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="tg-booking-form-parent-inner tg-hero-quantity p-relative mr-15 mb-15">
                                            <span class="tg-booking-form-title mb-5">{{ __('translate.Guest:') }}</span>
                                            <div class="tg-booking-add-input-field tg-booking-quantity-toggle">
                                                <div>
                                                    <!-- Show this when no values are selected -->
                                                    <span
                                                        x-show="!bookingForm.rooms && !bookingForm.adults && !bookingForm.children"
                                                        class="tg-booking-title-value">
                                                        {{ __('translate.+ Add Guests') }}
                                                    </span>

                                                    <!-- Show this when any value exists -->
                                                    <span
                                                        x-show="bookingForm.rooms || bookingForm.adults || bookingForm.children"
                                                        class="tg-booking-title-value">
                                                        <template x-if="bookingForm.rooms">
                                                            <span x-text="bookingForm.rooms + ' Room'"></span>
                                                        </template>
                                                        <template x-if="bookingForm.adults">
                                                            <span x-text="', ' + bookingForm.adults + ' Adult'"></span>
                                                        </template>
                                                        <template x-if="bookingForm.children">
                                                            <span x-text="', ' + bookingForm.children + ' Child'"></span>
                                                        </template>
                                                    </span>
                                                </div>
                                                <span class="location">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_53_98)">
                                                            <path
                                                                d="M1.51285 15.2889C1.33507 15.2889 1.15729 15.2 1.0684 15.1111C0.979514 14.9334 0.890625 14.8445 0.890625 14.6667C0.890625 13.4222 1.24618 12.1778 1.8684 11.0222C2.49063 9.95558 3.4684 8.9778 4.53507 8.35558C4.09062 7.82225 3.73507 7.11114 3.55729 6.40003C3.4684 5.68892 3.4684 4.88892 3.64618 4.26669C3.82396 3.55558 4.2684 2.84447 4.71285 2.31114C5.24618 1.7778 5.8684 1.33336 6.49063 1.15558C7.02396 0.977805 7.55729 0.888916 8.09063 0.888916C8.2684 0.888916 8.53507 0.888916 8.71285 0.888916C9.42396 0.977805 10.1351 1.24447 10.7573 1.68892C11.3795 2.13336 11.824 2.66669 12.1795 3.28892C12.5351 3.91114 12.7128 4.62225 12.7128 5.42225C12.7128 6.48892 12.3573 7.55558 11.6462 8.35558C12.1795 8.71114 12.7128 9.06669 13.2462 9.51114C13.9573 10.2222 14.4017 10.9334 14.8462 11.8222C15.2017 12.7111 15.3795 13.6 15.3795 14.5778C15.3795 14.7556 15.2906 14.9334 15.2017 15.0222C15.1128 15.1111 14.9351 15.2 14.7573 15.2C14.6684 15.2 14.5795 15.2 14.4906 15.1111C14.4017 15.1111 14.3128 15.0222 14.3128 14.9334C14.224 14.8445 14.224 14.8445 14.1351 14.7556C14.1351 14.6667 14.0462 14.5778 14.0462 14.4889C14.0462 13.6889 13.8684 12.9778 13.6017 12.2667C13.3351 11.5556 12.8906 10.9334 12.2684 10.4C11.7351 9.95558 11.2017 9.51114 10.5795 9.24447C9.8684 9.68892 9.0684 9.95558 8.09063 9.95558C7.20174 9.95558 6.31285 9.68892 5.60174 9.24447C4.62396 9.68892 3.73507 10.4 3.11285 11.3778C2.49063 12.3556 2.13507 13.4222 2.13507 14.5778C2.13507 14.7556 2.04618 14.9334 1.95729 15.0222C1.8684 15.2 1.69062 15.2889 1.51285 15.2889ZM8.09063 2.22225C7.4684 2.22225 6.84618 2.40003 6.31285 2.75558C5.69062 3.11114 5.33507 3.64447 5.0684 4.1778C4.80174 4.80003 4.71285 5.42225 4.89063 6.13336C4.97951 6.75558 5.33507 7.37781 5.77951 7.82225C6.22396 8.26669 6.84618 8.62225 7.4684 8.71114C7.64618 8.71114 7.91285 8.80003 8.09063 8.80003C8.53507 8.80003 8.97951 8.71114 9.33507 8.53336C9.95729 8.26669 10.4017 7.91114 10.8462 7.28892C11.2017 6.75558 11.3795 6.13336 11.3795 5.51114C11.3795 4.62225 11.024 3.82225 10.4017 3.20003C9.77951 2.48892 8.97951 2.22225 8.09063 2.22225Z"
                                                                fill="currentColor" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_53_98">
                                                                <rect width="16" height="16" fill="currentColor" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div
                                                class="tg-booking-form-location-list tg-quantity tg-booking-quantity-active">
                                                <ul>
                                                    <li>
                                                        <span class="mr-20">{{ __('translate.Rooms') }}</span>
                                                        <div class="tg-booking-quantity-item">
                                                            <span @click="incrementRooms" class="increment">
                                                                <svg width="15" height="14" viewBox="0 0 15 14"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1.21924 7H13.3836" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                    <path d="M7.30176 13V1" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </span>
                                                            <input x-bind:value="bookingForm.rooms"
                                                                class="tg-quantity-input" type="text">
                                                            <span @click="decrementRooms" class="decrement">
                                                                <svg width="14" height="2" viewBox="0 0 14 2"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1 1H13" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <span class="mr-20">{{ __('translate.Adults') }}</span>
                                                        <div class="tg-booking-quantity-item">
                                                            <span @click="incrementAdults" class="increment">
                                                                <svg width="15" height="14" viewBox="0 0 15 14"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1.21924 7H13.3836" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                    <path d="M7.30176 13V1" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </span>
                                                            <input x-bind:value="bookingForm.adults"
                                                                class="tg-quantity-input" type="text">
                                                            <span @click="decrementAdults" class="decrement">
                                                                <svg width="14" height="2" viewBox="0 0 14 2"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1 1H13" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <span class="mr-20">{{ __('translate.Children') }}</span>
                                                        <div class="tg-booking-quantity-item">
                                                            <span @click="incrementChildren" class="increment">
                                                                <svg width="15" height="14" viewBox="0 0 15 14"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1.21924 7H13.3836" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                    <path d="M7.30176 13V1" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </span>
                                                            <input x-bind:value="bookingForm.children"
                                                                class="tg-quantity-input" type="text">
                                                            <span @click="decrementChildren" class="decrement">
                                                                <svg width="14" height="2" viewBox="0 0 14 2"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1 1H13" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tg-booking-form-search-btn mb-15">
                                            <button @click="searchServices" class="bk-search-button"
                                                type="button">{{ __('translate.Search') }}
                                                <span class="ml-5">
                                                    <svg width="14" height="14" viewBox="0 0 14 14"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_53_103)">
                                                            <path
                                                                d="M13.2218 13.2222L10.5188 10.5192M12.1959 6.48705C12.1959 9.6402 9.63977 12.1963 6.48662 12.1963C3.33348 12.1963 0.777344 9.6402 0.777344 6.48705C0.777344 3.3339 3.33348 0.777771 6.48662 0.777771C9.63977 0.777771 12.1959 3.3339 12.1959 6.48705Z"
                                                                stroke="currentColor" stroke-width="1.575"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_53_103">
                                                                <rect width="14" height="14"
                                                                    fill="currentColor" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tg-listing-map-filter-wrap pl-40 pr-35">
                            <div class="tg-listing-map-filter pt-25 pb-15">
                                <div class="item-select mb-10">
                                    <select id="typeSelect" class="select item-first">
                                        <option>{{ __('translate.Type') }}</option>
                                        @foreach ($serviceTypes as $key => $serviceType)
                                            <option value="{{ $serviceType?->id }}">
                                                {{ $serviceType?->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="item-select mb-10">
                                    <select id="amenitySelect" class="select">
                                        <option>{{ __('translate.Amenities') }}</option>
                                        @foreach ($amenities as $key => $amenity)
                                            <option value="{{ $amenity?->translation?->id }}">
                                                {{ $amenity?->translation?->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="item-select mb-10">
                                    <select id="ratingSelect" class="select">
                                        <option value="default">{{ __('translate.Ratings') }}</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Star</option>
                                        <option value="3">3 Star</option>
                                        <option value="4">4 Star</option>
                                        <option value="5">5 Star</option>
                                    </select>
                                </div>
                                <div class="mt-5 ml-10">
                                    <a class="tg-filter-reset" x-show="isFilterChanged || isBookingFilterChanged"
                                        @click="resetFilters()"
                                        href="javascript:void(0);">{{ __('translate.Reset All') }}</a>
                                </div>
                            </div>
                            <div class="tg-listing-map-filter-bottom mb-5">
                                <div class="row align-items-center">
                                    <div class="col-xl-5 mb-15">
                                        <div class="tg-listing-box-number-found">
                                            <span class="custom_pagination_count"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-7 mb-15">
                                        <div
                                            class="tg-listing-box-view-type d-flex justify-content-end align-items-center">
                                            <div class="tg-listing-sort">
                                                <span>{{ __('translate.Sort by') }}:</span>
                                                <a href="javascript:void(0);">
                                                    <svg width="14" height="16" viewBox="0 0 14 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M8.46918 3.27934C8.6098 3.41979 8.80043 3.49868 8.99918 3.49868C9.19793 3.49868 9.38855 3.41979 9.52918 3.27934L10.2492 2.55934V12.7493C10.2492 12.9483 10.3282 13.139 10.4688 13.2797C10.6095 13.4203 10.8003 13.4993 10.9992 13.4993C11.1981 13.4993 11.3889 13.4203 11.5295 13.2797C11.6702 13.139 11.7492 12.9483 11.7492 12.7493V2.55934L12.4692 3.27934C12.5378 3.35303 12.6206 3.41213 12.7126 3.45312C12.8046 3.49411 12.904 3.51615 13.0047 3.51793C13.1054 3.51971 13.2054 3.50118 13.2988 3.46346C13.3922 3.42574 13.477 3.3696 13.5482 3.29838C13.6194 3.22716 13.6756 3.14233 13.7133 3.04894C13.751 2.95555 13.7695 2.85552 13.7678 2.75482C13.766 2.65411 13.7439 2.5548 13.703 2.4628C13.662 2.3708 13.6029 2.288 13.5292 2.21934L11.5292 0.21934C11.3886 0.0788894 11.1979 0 10.9992 0C10.8004 0 10.6098 0.0788894 10.4692 0.21934L8.46918 2.21934C8.32873 2.35997 8.24984 2.55059 8.24984 2.74934C8.24984 2.94809 8.32873 3.13871 8.46918 3.27934ZM3.74918 12.9393L4.46918 12.2193C4.53784 12.1457 4.62064 12.0866 4.71264 12.0456C4.80464 12.0046 4.90395 11.9825 5.00465 11.9807C5.10536 11.979 5.20539 11.9975 5.29877 12.0352C5.39216 12.0729 5.477 12.1291 5.54821 12.2003C5.61943 12.2715 5.67558 12.3564 5.7133 12.4497C5.75102 12.5431 5.76955 12.6432 5.76777 12.7439C5.76599 12.8446 5.74395 12.9439 5.70296 13.0359C5.66197 13.1279 5.60286 13.2107 5.52918 13.2793L3.52918 15.2793C3.38855 15.4198 3.19793 15.4987 2.99918 15.4987C2.80043 15.4987 2.6098 15.4198 2.46918 15.2793L0.469177 13.2793C0.39549 13.2107 0.336388 13.1279 0.295396 13.0359C0.254404 12.9439 0.232362 12.8446 0.230585 12.7439C0.228809 12.6432 0.247333 12.5431 0.285054 12.4497C0.322775 12.3564 0.37892 12.2715 0.450138 12.2003C0.521357 12.1291 0.606191 12.0729 0.699579 12.0352C0.792967 11.9975 0.892997 11.979 0.993699 11.9807C1.0944 11.9825 1.19372 12.0046 1.28571 12.0456C1.37771 12.0866 1.46052 12.1457 1.52918 12.2193L2.24918 12.9393V2.74934C2.24918 2.55043 2.32819 2.35966 2.46885 2.21901C2.6095 2.07836 2.80026 1.99934 2.99918 1.99934C3.19809 1.99934 3.38885 2.07836 3.52951 2.21901C3.67016 2.35966 3.74918 2.55043 3.74918 2.74934V12.9393Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </a>
                                            </div>
                                            <div class="tg-listing-select-price ml-10">
                                                <select id="sortSelect" class="select" name="sort_by">
                                                    <option value="default">Default</option>
                                                    <option value="latest">Latest</option>
                                                    <option value="oldest">Oldest</option>
                                                    <option value="price_low">Price Low</option>
                                                    <option value="price_high">Price High</option>
                                                    <option value="trending">Trending</option>
                                                    <option value="popular">Popular</option>
                                                    <option value="location_asc">Location A-Z</option>
                                                    <option value="location_desc">Location Z-A</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="filter_data"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="tg-map-full h-100">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31078.361591144112!2d-74.0256365664179!3d40.705584751235754!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sbd!4v1724572184688!5m2!1sen!2sbd"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tg-listing-map-area-end -->
@endsection


@push('js_section')
    <script>
        (function($) {
            "use strict"
            $(document).ready(function() {

                // Initialize timepicker
                $(".timepicker").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });
            });
        })(jQuery);
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('data', () => ({
                page: 1,
                style: 'style4',
                isListView: false,
                // Booking form data
                defaultBookingForm: {
                    destination_id: '',
                    destination: 'Where are you going . . .',
                    checkIn: '',
                    checkOut: '',
                    rooms: 1,
                    adults: 1,
                    children: 0
                },
                bookingForm: {
                    destination_id: `{{ request('destination_id', '') }}`,
                    destination: `{{ request('destination', '') }}`,
                    checkIn: `{{ request('checkIn', '') }}`,
                    checkOut: `{{ request('checkOut', '') }}`,
                    rooms: `{{ request('rooms', '') }}`,
                    adults: `{{ request('adults', '') }}`,
                    children: `{{ request('children', '') }}`,
                },
                incrementRooms() {
                    this.bookingForm.rooms++;
                },
                decrementRooms() {
                    if (this.bookingForm.rooms > 0) {
                        this.bookingForm.rooms--;
                    }
                },
                incrementAdults() {
                    this.bookingForm.adults++;
                },
                decrementAdults() {
                    if (this.bookingForm.adults > 0) {
                        this.bookingForm.adults--;
                    }
                },
                incrementChildren() {
                    this.bookingForm.children++;
                },
                decrementChildren() {
                    if (this.bookingForm.children > 0) {
                        this.bookingForm.children--;
                    }
                },
                selectDestination(destinationId, destinationName) {
                    this.bookingForm.destination_id = destinationId;
                    this.bookingForm.destination = destinationName;
                    this.closeDestinationDropdown();
                },

                closeDestinationDropdown() {
                    $('.tg-booking-quantity-toggle').removeClass('active');
                    $('.tg-booking-quantity-active').removeClass('tg-list-open');
                },

                filters: {
                    search: `{{ request('search', '') }}`,
                    service_type_id: `{{ request('service_type_id', '') }}`,
                    amenity_id: `{{ request('amenity_id', '') }}`,
                    max_price: `{{ request('max_price', '') }}`,
                    min_price: `{{ request('min_price', '') }}`,
                    languages: {!! json_encode(request('languages', [])) !!},
                    sort_by: `{{ request('sort_by', '') }}`,
                    ratings: {!! json_encode(request('ratings', [])) !!},
                    ratting: `{{ request('ratting', '') }}`
                },
                defaultFilters: {
                    search: '',
                    service_type_id: '',
                    max_price: '',
                    min_price: '',
                    amenity_id: [],
                    languages: [],
                    sort_by: '',
                    ratings: [],
                    ratting: ''
                },
                get isFilterChanged() {
                    return JSON.stringify(this.filters) !== JSON.stringify(this.defaultFilters);
                },
                get isBookingFilterChanged() {
                    return JSON.stringify(this.bookingForm) !== JSON.stringify(this
                        .defaultBookingForm);
                },
                updateURL(value) {
                    var currentURL = window.location.protocol + "//" + window.location.host + window
                        .location.pathname;
                    var queryParams = [];

                    // Loop through the object and only add non-empty values to the query params
                    for (const [key, val] of Object.entries(value)) {
                        // Check if the value is an array
                        if (Array.isArray(val)) {
                            if (val.length > 0) {
                                // Append [] to key for array values
                                val.forEach(item => {
                                    queryParams.push(
                                        `${encodeURIComponent(key)}%5B%5D=${encodeURIComponent(item)}`
                                    );
                                });
                            }
                        }
                        // Check if the value is not empty, and not equal to the default value
                        else if (val !== null && val !== undefined && val !== '') {
                            queryParams.push(`${encodeURIComponent(key)}=${encodeURIComponent(val)}`);
                        }
                    }

                    // Join all query parameters with '&'
                    var queryString = queryParams.length > 0 ? '?' + queryParams.join('&') : '';

                    var newURL = currentURL + queryString;

                    // Update the browser's URL
                    window.history.pushState({
                        path: newURL
                    }, '', newURL);
                },
                resetFilters() {
                    this.filters = JSON.parse(JSON.stringify(this.defaultFilters));
                    this.bookingForm = JSON.parse(JSON.stringify({
                        destination_id: '',
                        destination: '',
                        checkIn: '',
                        checkOut: '',
                        rooms: '',
                        adults: '',
                        children: ''
                    }));

                    this.$nextTick(() => {
                        $('#typeSelect').val('Type').niceSelect('update');
                        $('#sortSelect').val('default').niceSelect('update');
                        $('#amenitySelect').val('Amenities').niceSelect('update');
                        $('#ratingSelect').val('default').niceSelect('update');
                    });
                },
                init() {
                    this.$watch('filters', (value, oldValue) => {
                        this.page = 1;
                        this.fetchServices();
                        this.updateURL({
                            ...this.bookingForm,
                            ...this.filters
                        });
                    });
                    this.initializeAll();
                },
                fetchServices() {
                    that = this;

                    this.loadingOverlay("show");

                    $.ajax({
                        url: `{{ route('front.tourbooking.services.load.ajax') }}`,
                        method: 'GET',
                        data: {
                            ...this.filters,
                            ...this.bookingForm,
                            page: this.page,
                            isListView: this.isListView,
                            style: this.style
                        },
                        success: function(response) {
                            $('#filter_data').html(response.view);
                            $('.custom_pagination_count').html(response
                                .customPaginationCount);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        },
                        complete: function() {
                            that.loadingOverlay("hide");
                        }
                    });
                },
                searchServices() {
                    this.page = 1;
                    this.fetchServices();
                    this.updateURL({
                        ...this.bookingForm,
                        ...this.filters
                    });
                },
                initializeAll() {
                    $(document).on('click', '.pagination a', (event) => {
                        event.preventDefault();
                        const page = $(event.target).attr('href').split('page=')[1];
                        this.page = page;
                        this.fetchServices();
                        $("html, body").animate({
                            scrollTop: 0
                        }, 500);
                    });

                    this.$nextTick(() => {

                        $('#sortSelect').niceSelect();

                        $('#typeSelect').on('change', (e) => {
                            this.filters.service_type_id = e.target.value
                        });
                        $('#typeSelect').val(this.filters.service_type_id || 'Type').niceSelect(
                            'update');

                        $('#sortSelect').on('change', (e) => {
                            this.filters.sort_by = e.target.value
                        });
                        $('#sortSelect').val(this.filters.sort_by || 'default').niceSelect(
                            'update');

                        $('#amenitySelect').on('change', (e) => {
                            this.filters.amenity_id = e.target.value
                        });
                        $('#amenitySelect').val(this.filters.amenity_id || 'Amenities')
                            .niceSelect(
                                'update');

                        $('#ratingSelect').on('change', (e) => {
                            this.filters.ratting = e.target.value
                        });
                        $('#ratingSelect').val(this.filters.ratting || 'default')
                            .niceSelect(
                                'update');
                    });

                    this.loadingOverlay("show");

                    this.fetchServices();
                },

                loadingOverlay(action = 'show', target = false) {
                    const options = {
                        size: 50,
                        maxSize: 50,
                        minSize: 50
                    };

                    if (target && typeof target === 'string') {
                        $(target).LoadingOverlay(action, options);
                    } else {
                        $.LoadingOverlay(action, options);
                    }
                }
            }));
        });
    </script>
@endpush

@push('style_section')
    <style>
        .item_loading {
            top: 20px;
            position: relative;
        }

        .tg-listing-map-list-item .tg-listing-card-thumb {
            width: 280px;
            min-width: 280px;
        }

        .tg-listing-item-wishlist.active svg {
            color: var(--tg-theme-primary);
        }

        .tg-listing-card-currency-amount del {
            font-weight: 500;
            font-size: 14px;
            line-height: 1;
            text-transform: capitalize;
            color: #dbe6f7;
            display: block;
        }

        span.tg-listing-item-price-discount.shape-3 {
            top: 55px;
        }

        span.tg-listing-item-price-discount.shape {
            top: 0;
        }

        span.tg-listing-item-price-discount.shape-2 {
            top: 28px;
        }

        .tg-listing-card-thumb {
            height: 212px;
        }

        .tg-listing-card-thumb img {
            height: 100%;
            object-fit: cover;
        }
    </style>
@endpush
