@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Trip Type Details') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Trip Type Details') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Trip Type Details') }}</p>
@endsection

@section('body-content')
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">
                                                {{ __('translate.Trip Type Details') }}</h4>
                                            <div>
                                                <a href="{{ route('admin.tourbooking.trip-type.edit', $tripType->id) }}"
                                                    class="crancy-btn crancy-btn__primary me-2">
                                                    <i class="fa fa-edit"></i> {{ __('translate.Edit') }}
                                                </a>
                                                <a href="{{ route('admin.tourbooking.trip-type.index') }}"
                                                    class="crancy-btn">
                                                    <i class="fa fa-list"></i> {{ __('translate.Back to List') }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row mg-top-25">
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-body text-center">

                                                        <h5 class="card-title">
                                                            {{ $tripType->name }}
                                                        </h5>
                                                        <p class="text-muted"><small>{{ $tripType->slug }}</small></p>

                                                        <div class="mt-3">
                                                            @if ($tripType->status)
                                                                <span
                                                                    class="crancy-badge crancy-badge-success">{{ __('translate.Active') }}</span>
                                                            @else
                                                                <span
                                                                    class="crancy-badge crancy-badge-danger">{{ __('translate.Inactive') }}</span>
                                                            @endif

                                                            @if ($tripType->is_featured)
                                                                <span
                                                                    class="crancy-badge crancy-badge-primary">{{ __('translate.Featured') }}</span>
                                                            @endif

                                                            @if ($tripType->show_on_homepage)
                                                                <span
                                                                    class="crancy-badge crancy-badge-info">{{ __('translate.Homepage') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($tripType->description)
                                                    <div class="card mt-4">
                                                        <div class="card-header">
                                                            <h5 class="mb-0">{{ __('translate.Description') }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <p>{{ $tripType->description }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif
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
    </section>
@endsection
