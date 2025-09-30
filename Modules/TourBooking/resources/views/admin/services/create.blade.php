@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Create Service') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Create Service') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Create Service') }}</p>
@endsection

@push('style_section')
    <link rel="stylesheet" href="{{ asset('global/select2/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* Integrare cu dev.css — folosim input styling global; doar utilitare locale aici */
        .flatpickr-months .flatpickr-month { height: 108px; }

        /* Currency adornment (fără a strica stilul input-ului din dev.css) */
        .crancy__item-form--currency{ position: relative; display: flex; align-items: center; }
        .crancy__item-form--currency .crancy__item-input{ padding-right: 44px; }
        .crancy__currency-icon{
            position:absolute; right:12px; top:50%; transform:translateY(-50%);
            pointer-events:none; font-weight:700; color:#111827; opacity:.8;
        }

        /* Card secțiuni + subtitluri (stil identic cu Edit) */
        .section-card + .section-card{ margin-top:24px; }
        .section-card .crancy-card__head{ padding:18px 20px; border-bottom:1px solid var(--border); }
        .section-card .crancy-card__title{ margin:0; font-weight:700; font-size:var(--fs-xl); color:var(--g-900); }
        .section-card .crancy-card__body{ padding:20px; }

        /* Header „Switch language” dacă vei avea nevoie aici în viitor */
        .translation_main_box .translation_box ul{
            display:flex; flex-wrap:wrap; gap:8px; padding:0; margin:0; list-style:none;
        }
        .translation_main_box .translation_box ul li a{
            display:inline-flex; align-items:center; gap:6px;
            padding:8px 12px; border:1px solid var(--border);
            border-radius:12px; background:#fff; color:#2b2f3a; font-weight:600;
        }
        .translation_main_box .translation_box ul li a:hover{
            border-color:var(--brand-200); color:#111827; background:#fff; box-shadow:var(--shadow-1);
        }

        /* Layout helpers */
        .create_new_btn_inline_box{ display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .create_new_btn_inline_box .crancy-btn{ white-space:nowrap; }

        /* Mobil: mai compact */
        @media (max-width: 992px){
            .section-card .crancy-card__body{ padding:16px; }
        }

        /* === Age Categories: spacing & header alignment (identic cu Edit) === */
        .agecat-card{
            border:1px solid var(--border);
            border-radius:16px;
            padding:16px;
            background:#fff;
            box-shadow:var(--shadow-1);
            margin:16px 0;
        }
        .agecat-card .d-flex.align-items-center.justify-content-between{ gap:12px; }
        .agecat-card .form-check{
            margin-left:auto; display:inline-flex; align-items:center; gap:8px; white-space:nowrap;
        }
        .agecat-card .form-check-input{ margin-top:0; }
        .agecat-card.disabled{ opacity:.55; }

        .visually-hidden{ display:none !important; }
    </style>
@endpush

@section('body-content')
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <br>
            <div class="row">
                <br>
                <div class="col-12">
                    <form action="{{ route('admin.tourbooking.services.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <!-- Basic Information -->
                        <div class="crancy-card section-card">
                            <div class="crancy-card__head">
                                <div class="create_new_btn_inline_box">
                                    <h4 class="crancy-card__title">{{ __('translate.Basic Information') }}</h4>
                                    <a href="{{ route('admin.tourbooking.services.index') }}" class="crancy-btn">
                                        <i class="fa fa-list"></i> {{ __('translate.Service List') }}
                                    </a>
                                </div>
                            </div>
                            <div class="crancy-card__body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label" for="title">{{ __('translate.Title') }} *</label>
                                            <input class="crancy__item-input" type="text" name="title" id="title" value="{{ old('title') }}" required>
                                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label" for="slug">{{ __('translate.Slug') }}</label>
                                            <input class="crancy__item-input" type="text" name="slug" id="slug" value="{{ old('slug') }}">
                                            @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Service Type') }} *</label>
                                            <select class="crancy__item-input" name="service_type_id" required>
                                                <option value="">{{ __('translate.Select Type') }}</option>
                                                @foreach ($serviceTypes as $type)
                                                    <option value="{{ $type->id }}" @selected(old('service_type_id') == $type->id)>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('service_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Select Destination') }}</label>
                                            <select class="crancy__item-input" name="destination_id" required>
                                                <option value="">{{ __('translate.Select Destination') }}</option>
                                                @foreach ($destinations as $destination)
                                                    <option value="{{ $destination->id }}" @selected(old('destination_id') == $destination->id)>{{ $destination->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('destination_id') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Location') }}</label>
                                            <input class="crancy__item-input" type="text" name="location" value="{{ old('location') }}">
                                            @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Duration') }}</label>
                                            <input class="crancy__item-input" type="text" name="duration" value="{{ old('duration') }}" placeholder="e.g. 3 hours, 2 days">
                                            @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Group Size') }}</label>
                                            <input class="crancy__item-input" type="text" name="group_size" value="{{ old('group_size') }}" placeholder="e.g. Up to 10 people">
                                            @error('group_size') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('Tour Count') }}</label>
                                            <input class="crancy__item-input" type="number" name="room_count" value="{{ old('room_count', 1) }}" placeholder="1">
                                            @error('room_count') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    {{-- ===================== Age Categories ===================== --}}
                                    @php
                                        $ageCategories = [
                                            'infant' => __('Infant'),
                                            'baby'   => __('Baby'),
                                            'child'  => __('Child'),
                                            'adult'  => __('Adult'),
                                        ];
                                    @endphp

                                    <div class="col-12">
                                        <h5 class="mg-top-form-20 mb-2">{{ __('Age Categories & Pricing') }}</h5>
                                        <br>
                                        <p class="text-muted" style="margin-top:-6px">
                                            {{ __('Define age ranges and per-category pricing. Enable only the categories that apply for this trip.') }}
                                        </p>
                                        <br>
                                    </div>

                                    @foreach($ageCategories as $key => $label)
                                        @php $enabled = old("age_categories.$key.enabled", $key === 'adult' ? 1 : 0); @endphp
                                        <div class="col-12">
                                            <div class="agecat-card" id="card_{{ $key }}">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <label class="crancy__item-label mb-0">{{ $label }}</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input toggle-agecat"
                                                               type="checkbox"
                                                               id="agecat_{{ $key }}_enabled"
                                                               name="age_categories[{{ $key }}][enabled]"
                                                               value="1"
                                                               {{ $enabled ? 'checked' : '' }}
                                                               data-target="#fields_{{ $key }}">
                                                        <label class="form-check-label" for="agecat_{{ $key }}_enabled">
                                                            {{ __('Enable Category') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div id="fields_{{ $key }}" class="row mt-3">
                                                    <div class="col-lg-3 col-md-6 col-12">
                                                        <label class="crancy__item-label">{{ __('Count') }}</label>
                                                        <input class="crancy__item-input" type="number" min="0"
                                                               name="age_categories[{{ $key }}][count]"
                                                               value="{{ old("age_categories.$key.count", $key==='adult' ? 1 : 0) }}"
                                                               placeholder="{{ $key==='adult' ? '1' : '0' }}">
                                                        @error("age_categories.$key.count") <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12">
                                                        <label class="crancy__item-label">{{ __('Price (per person)') }}</label>
                                                        <div class="crancy__item-form--currency">
                                                            <input class="crancy__item-input" type="number" step="0.01" min="0"
                                                                   name="age_categories[{{ $key }}][price]"
                                                                   value="{{ old("age_categories.$key.price") }}"
                                                                   placeholder="0.00">
                                                            <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                                                        </div>
                                                        @error("age_categories.$key.price") <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12">
                                                        <label class="crancy__item-label">{{ __('Min Age (years)') }}</label>
                                                        <input class="crancy__item-input" type="number" min="0" step="1"
                                                               name="age_categories[{{ $key }}][min_age]"
                                                               value="{{ old("age_categories.$key.min_age", match($key){ 'infant'=>0, 'baby'=>0, 'child'=>2, 'adult'=>18, default=>0 }) }}"
                                                               placeholder="0">
                                                        @error("age_categories.$key.min_age") <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12">
                                                        <label class="crancy__item-label">{{ __('Max Age (years)') }}</label>
                                                        <input class="crancy__item-input" type="number" min="0" step="1"
                                                               name="age_categories[{{ $key }}][max_age]"
                                                               value="{{ old("age_categories.$key.max_age", match($key){ 'infant'=>1, 'baby'=>2, 'child'=>12, 'adult'=>120, default=>0 }) }}"
                                                               placeholder="{{ $key==='child' ? '12 / 15' : '' }}">
                                                        @error("age_categories.$key.max_age") <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    {{-- ===================== /Age Categories ===================== --}}

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Select Trip Types') }}</label>
                                            <select class="select2" name="trip_types[]" multiple="multiple">
                                                @foreach ($tripTypes as $type)
                                                    <option value="{{ $type->id }}" @selected(collect(old('trip_types', []))->contains($type->id))>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('trip_types') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Short Description') }}</label>
                                            <textarea class="crancy__item-input summernote" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                                            @error('short_description') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Description') }}</label>
                                            <textarea class="crancy__item-input summernote" name="description" rows="6">{{ old('description') }}</textarea>
                                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label d-block">{{ __('translate.Status') }}</label>
                                            <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                <label class="crancy__item-switch">
                                                    <input name="status" type="checkbox" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                                    <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div> <!-- /row -->
                            </div>
                        </div>
                        <!-- /Basic Information -->

                        {{-- LEGACY Pricing (ascuns când există age categories active) --}}
                        <div class="crancy-card section-card legacy-pricing-card">
                            <div class="crancy-card__head">
                                <h4 class="crancy-card__title">{{ __('translate.Pricing Details') }}</h4>
                            </div>
                            <div class="crancy-card__body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Show Per Person Price') }}</label>
                                            <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                <label class="crancy__item-switch">
                                                    <input name="is_per_person" id="show_per_person_price" type="checkbox" {{ old('is_per_person') ? 'checked' : '' }}>
                                                    <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 d-none per_person_price_div">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Price Per Person') }}</label>
                                            <div class="crancy__item-form--currency">
                                                <input class="crancy__item-input" type="number" step="0.01" name="price_per_person" value="{{ old('price_per_person') }}">
                                                <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                                            </div>
                                            @error('price_per_person') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 full_price_div">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Full Price') }}</label>
                                            <div class="crancy__item-form--currency">
                                                <input class="crancy__item-input" type="number" step="0.01" name="full_price" value="{{ old('full_price') }}">
                                                <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                                            </div>
                                            @error('full_price') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-12 full_price_div">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Discount Price') }}</label>
                                            <div class="crancy__item-form--currency">
                                                <input class="crancy__item-input" type="number" step="0.01" name="discount_price" value="{{ old('discount_price') }}">
                                                <div class="crancy__currency-icon"><span>{{ config('settings.currency_icon', '$') }}</span></div>
                                            </div>
                                            @error('discount_price') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- /LEGACY Pricing --}}

                        <!-- Additional Information -->
                        <div class="crancy-card section-card">
                            <div class="crancy-card__head">
                                <h4 class="crancy-card__title">{{ __('translate.Additional Information') }}</h4>
                            </div>
                            <div class="crancy-card__body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Check in Date') }}</label>
                                            <input class="crancy__item-input timepicker-date" type="text" name="check_in_date" value="{{ old('check_in_date') }}" placeholder="Select date" autocomplete="off">
                                            @error('check_in_date') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Check Out Date') }}</label>
                                            <input class="crancy__item-input timepicker-date" type="text" name="check_out_date" value="{{ old('check_out_date') }}" placeholder="Select date" autocomplete="off">
                                            @error('check_out_date') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Check-in Time') }}</label>
                                            <input class="crancy__item-input timepicker" type="text" name="check_in_time" value="{{ old('check_in_time') }}" placeholder="e.g. 14:00" autocomplete="off">
                                            @error('check_in_time') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Check-out Time') }}</label>
                                            <input class="crancy__item-input timepicker" type="text" name="check_out_time" value="{{ old('check_out_time') }}" placeholder="e.g. 10:00" autocomplete="off">
                                            @error('check_out_time') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Ticket') }}</label>
                                            <input class="crancy__item-input" type="text" name="ticket" value="{{ old('ticket') }}" placeholder="e.g. Mobile Voucher or Printed Ticket" autocomplete="off">
                                            @error('ticket') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Video URL') }}</label>
                                            <input class="crancy__item-input" type="url" name="video_url" value="{{ old('video_url') }}" placeholder="YouTube or Vimeo URL">
                                            @error('video_url') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Languages') }}</label>
                                            <select class="crancy__item-input select2" name="languages[]" multiple>
                                                @foreach ($enum_languages as $language)
                                                    <option value="{{ $language->name }}" @selected(collect(old('languages', []))->contains($language->name))>
                                                        {{ $language->value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Amenities') }}</label>
                                            <select class="crancy__item-input select2" name="amenities[]" multiple>
                                                @foreach ($amenities as $amenity)
                                                    <option value="{{ $amenity->translation->id }}" @selected(collect(old('amenities', []))->contains($amenity->translation->id))>
                                                        {{ $amenity->translation->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.What is included') }}</label>
                                            <textarea name="included" rows="30" class="crancy__item-input" placeholder="One item per line">{{ old('included') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.What is excluded') }}</label>
                                            <textarea name="excluded" rows="30" class="crancy__item-input" placeholder="One item per line">{{ old('excluded') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.Tour Plan Sub Title') }}</label>
                                            <input class="crancy__item-input" type="text" name="tour_plan_sub_title" value="{{ old('tour_plan_sub_title') }}" placeholder="Tour Plan Sub Title">
                                            @error('tour_plan_sub_title') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- /Additional Information -->

                        <!-- SEO Information -->
                        <div class="crancy-card section-card">
                            <div class="crancy-card__head">
                                <h4 class="crancy-card__title">{{ __('translate.SEO Information') }}</h4>
                            </div>
                            <div class="crancy-card__body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.SEO Title') }}</label>
                                            <input class="crancy__item-input" type="text" name="seo_title" value="{{ old('seo_title') }}">
                                            @error('seo_title') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.SEO Description') }}</label>
                                            <textarea class="crancy__item-input summernote" name="seo_description" rows="3">{{ old('seo_description') }}</textarea>
                                            @error('seo_description') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label">{{ __('translate.SEO Keywords') }}</label>
                                            <input class="crancy__item-input" type="text" name="seo_keywords" value="{{ old('seo_keywords') }}" placeholder="Comma separated keywords">
                                            @error('seo_keywords') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /SEO Information -->

                        <!-- Display Options + Actions -->
                        <div class="crancy-card section-card">
                            <div class="crancy-card__head">
                                <h4 class="crancy-card__title">{{ __('translate.Display Options') }}</h4>
                            </div>
                            <div class="crancy-card__body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label d-block">{{ __('translate.Featured') }}</label>
                                            <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                <label class="crancy__item-switch">
                                                    <input name="is_featured" type="checkbox" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                                    <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label d-block">{{ __('translate.Popular') }}</label>
                                            <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                <label class="crancy__item-switch">
                                                    <input name="is_popular" type="checkbox" value="1" {{ old('is_popular') ? 'checked' : '' }}>
                                                    <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label d-block">{{ __('translate.Show on Homepage') }}</label>
                                            <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                <label class="crancy__item-switch">
                                                    <input name="show_on_homepage" type="checkbox" value="1" {{ old('show_on_homepage') ? 'checked' : '' }}>
                                                    <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="crancy__item-form--group mg-top-form-20">
                                            <label class="crancy__item-label d-block">{{ __('translate.Is New') }}</label>
                                            <div class="crancy-ptabs__notify-switch crancy-ptabs__notify-switch--two">
                                                <label class="crancy__item-switch">
                                                    <input name="is_new" type="checkbox" value="1" {{ old('is_new', 1) ? 'checked' : '' }}>
                                                    <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            {{ __('translate.After creating the service, you can upload images and videos from the Media Gallery section.') }}
                                        </div>
                                        <button class="crancy-btn" type="submit">{{ __('translate.Create Service') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Display Options + Actions -->
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js_section')
    <script src="{{ asset('global/select2/select2.min.js') }}"></script>
    <script src="{{ asset('global/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    (function ($) {
        "use strict";

        $(function () {
            /* ---------- Slug from Title ---------- */
            $("#title").on("keyup", function () {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^\w ]+/g, "")
                    .replace(/ +/g, "-");
                $("#slug").val(slug);
            });

            /* ---------- Select2 ---------- */
            $('.select2').select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });

            /* ---------- Flatpickr ---------- */
            $(".timepicker").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                allowInput: true,
                onReady: function (selectedDates, dateStr, instance) {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "flatpickr-clear-btn";
                    btn.textContent = "Clear";
                    btn.style.marginLeft = "10px";
                    btn.onclick = function () { instance.clear(); };
                    instance.calendarContainer.appendChild(btn);
                }
            });

            $(".timepicker-date").flatpickr({
                dateFormat: "Y-m-d",
                minDate: "today",
                allowInput: true,
                onReady: function (selectedDates, dateStr, instance) {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "flatpickr-clear-btn";
                    btn.textContent = "Clear";
                    btn.style.marginLeft = "10px";
                    btn.onclick = function () { instance.clear(); };
                    instance.calendarContainer.appendChild(btn);
                }
            });

            /* ---------- TinyMCE ---------- */
            tinymce.init({
                selector: '.summernote',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | numlist bullist outdent indent | removeformat',
                menubar: false,
                branding: false
            });

            /* ---------- Per-person vs Full price (legacy pricing) ---------- */
            function applyPerPersonToggle(isChecked) {
                if (isChecked) {
                    $('.per_person_price_div').removeClass('d-none');
                    $('.full_price_div').addClass('d-none');
                } else {
                    $('.per_person_price_div').addClass('d-none');
                    $('.full_price_div').removeClass('d-none');
                }
            }
            applyPerPersonToggle($('#show_per_person_price').is(':checked'));
            $('#show_per_person_price').on('change', function () {
                applyPerPersonToggle($(this).is(':checked'));
            });

            /* ---------- Age categories: enable/disable & hide legacy pricing when used ---------- */

            // 1) Ensure unchecked checkboxes submit "0"
            $('.toggle-agecat').each(function () {
                const $chk = $(this);
                const name = $chk.attr('name');
                const exists = $chk.prev('input[type=hidden][name="' + name.replace(/([\[\]])/g, '\\$1') + '"]').length;
                if (!exists) $('<input>', { type: 'hidden', name: name, value: 0 }).insertBefore($chk);
            });

            // 2) Enable/disable fields within card
            function setAgecatState($chk) {
                const enabled   = $chk.is(':checked');
                const targetSel = $chk.data('target');
                const $card     = $chk.closest('.agecat-card');
                const $fields   = $(targetSel);

                $card.toggleClass('disabled', !enabled);
                $fields.find('input').prop('disabled', !enabled);
            }

            // 3) Hide legacy pricing card when any age category is enabled
            function recalcLegacyPricingVisibility() {
                const anyEnabled = $('.toggle-agecat:checked').length > 0;
                const $legacy = $('.legacy-pricing-card');

                if (!$legacy.length) return;

                if (anyEnabled) {
                    $legacy.addClass('visually-hidden')
                           .find(':input').prop('disabled', true);
                } else {
                    $legacy.removeClass('visually-hidden')
                           .find(':input').prop('disabled', false);
                    applyPerPersonToggle($('#show_per_person_price').is(':checked'));
                }
            }

            // Initial states
            $('.toggle-agecat').each(function () { setAgecatState($(this)); });
            recalcLegacyPricingVisibility();

            // On toggle
            $(document).on('change', '.toggle-agecat', function () {
                setAgecatState($(this));
                recalcLegacyPricingVisibility();
            });
        });
    })(jQuery);
    </script>
@endpush
