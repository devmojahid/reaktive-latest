@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Service Details') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Service Details') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Service Details') }}</p>
@endsection

@push('style_section')
    <style>
        /* Service Details Styling */
        .service-detail-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .service-detail-header {
            background: #eeeeee;
            color: white;
            padding: 20px 30px;
            position: relative;
        }

        .service-detail-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .service-detail-body {
            padding: 30px;
        }

        .detail-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .detail-value {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .detail-value.large {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }

        .feature-badge {
            display: inline-block;
            padding: 3px 8px;
            margin: 2px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .feature-featured { background-color: #fff3cd; color: #856404; }
        .feature-popular  { background-color: #d1ecf1; color: #0c5460; }
        .feature-homepage { background-color: #d4edda; color: #155724; }
        .feature-new      { background-color: #f8d7da; color: #721c24; }

        .price-display { font-size: 24px; font-weight: 700; color: #28a745; }
        .price-display del { font-size: 18px; color: #dc3545; margin-right: 10px; }

        .currency-icon { font-size: 18px; color: #666; margin-right: 5px; }

        .list-items { padding-left: 0; list-style: none; }
        .list-items li { padding: 5px 0; border-bottom: 1px solid #f0f0f0; }
        .list-items li:last-child { border-bottom: none; }

        .amenity-tag {
            display: inline-block;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 15px;
            padding: 4px 12px;
            margin: 2px;
            font-size: 12px;
            color: #495057;
        }

        .language-tag {
            display: inline-block;
            background: #e9ecef;
            border-radius: 12px;
            padding: 3px 10px;
            margin: 2px;
            font-size: 11px;
            color: #495057;
        }

        .action-buttons {
            text-align: right;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        .btn-group .btn { margin-left: 10px; }

        .media-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .media-item { border-radius: 6px; overflow: hidden; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .media-item img { width: 100%; height: 80px; object-fit: cover; }

        .empty-value { color: #999; font-style: italic; }

        /* Responsive */
        @media (max-width: 768px) {
            .service-detail-body { padding: 20px; }
            .action-buttons { padding: 15px 20px; text-align: center; }
            .btn-group .btn { margin: 5px; display: block; width: 100%; }
        }
    </style>
@endpush

@section('body-content')
    <!-- Service Details Header -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <div class="service-detail-card">
                                        <div class="service-detail-header">
                                            <h4>{{ $service->title }}</h4>
                                            <div class="mt-2">
                                                @if ($service->status)
                                                    <span class="status-badge status-active">Active</span>
                                                @else
                                                    <span class="status-badge status-inactive">Inactive</span>
                                                @endif

                                                @if ($service->is_featured)
                                                    <span class="feature-badge feature-featured">Featured</span>
                                                @endif

                                                @if ($service->is_popular)
                                                    <span class="feature-badge feature-popular">Popular</span>
                                                @endif

                                                @if ($service->show_on_homepage)
                                                    <span class="feature-badge feature-homepage">Homepage</span>
                                                @endif

                                                @if ($service->is_new)
                                                    <span class="feature-badge feature-new">New</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="action-buttons">
                                            <div class="btn-group gap-3">
                                                <a href="{{ route('admin.tourbooking.services.index') }}" class="crancy-btn crancy-btn--secondary">
                                                    <i class="fa fa-list"></i> {{ __('translate.Service List') }}
                                                </a>
                                                <a href="{{ route('admin.tourbooking.services.edit', $service->id) }}" class="crancy-btn">
                                                    <i class="fa fa-edit"></i> {{ __('translate.Edit Service') }}
                                                </a>
                                                <a href="{{ route('admin.tourbooking.services.media', $service->id) }}" class="crancy-btn crancy-btn--info">
                                                    <i class="fa fa-image"></i> {{ __('translate.Media Gallery') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- row -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Details Content -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">

                                <!-- Basic Information -->
                                <div class="col-lg-8 col-12 mg-top-30">
                                    <div class="service-detail-card">
                                        <div class="service-detail-header">
                                            <h4>{{ __('translate.Basic Information') }}</h4>
                                        </div>
                                        <div class="service-detail-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Title') }}</div>
                                                        <div class="detail-value large">{{ $service->title }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Slug') }}</div>
                                                        <div class="detail-value">{{ $service->slug ?: 'Not set' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Service Type') }}</div>
                                                        <div class="detail-value">
                                                            {{ $service->serviceType->name ?? 'Not set' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">Destination</div>
                                                        <div class="detail-value">
                                                            {{ $service->destination->name ?? 'Not set' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Location') }}</div>
                                                        <div class="detail-value">{{ $service->location ?: 'Not set' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Duration') }}</div>
                                                        <div class="detail-value">{{ $service->duration ?: 'Not set' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Group Size') }}</div>
                                                        <div class="detail-value">{{ $service->group_size ?: 'Not set' }}</div>
                                                    </div>
                                                </div>

                                                {{-- Age Categories (read-only list) --}}
                                                @if(is_array($service->age_categories) && count($service->age_categories))
                                                    @php
                                                        $labels = [
                                                            'infant' => __('translate.Infant'),
                                                            'baby'   => __('translate.Baby'),
                                                            'child'  => __('translate.Child'),
                                                            'adult'  => __('translate.Adult'),
                                                        ];
                                                    @endphp
                                                    <div class="col-12">
                                                        <div class="detail-item">
                                                            <div class="detail-label">{{ __('translate.Age Categories') }}</div>
                                                            <div class="detail-value">
                                                                <div style="display:grid;gap:8px">
                                                                    @foreach(['infant','baby','child','adult'] as $k)
                                                                        @php $c = $service->age_categories[$k] ?? null; @endphp
                                                                        @if($c && !empty($c['enabled']))
                                                                            <div style="display:flex;justify-content:space-between;align-items:center;border:1px solid #f0f0f0;border-radius:8px;padding:10px 12px">
                                                                                <div>
                                                                                    <div class="fw-semibold">{{ $labels[$k] }}</div>
                                                                                    <div class="text-muted small">
                                                                                        {{ __('translate.Min Age (years)') }}: {{ (int)($c['min_age'] ?? 0) }}
                                                                                        &nbsp;|&nbsp;
                                                                                        {{ __('translate.Max Age (years)') }}: {{ (int)($c['max_age'] ?? 0) }}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="text-end">
                                                                                    @if(isset($c['price']))
                                                                                        <div class="large">{{ currency((float)$c['price']) }}</div>
                                                                                        <div class="text-muted small">{{ __('translate.Price (per person)') }}</div>
                                                                                    @endif
                                                                                    @if(isset($c['count']))
                                                                                        <div class="text-muted small">{{ __('translate.Count') }}: {{ (int)$c['count'] }}</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.Short Description') }}</div>
                                                <div class="detail-value">
                                                    {!! $service->short_description ?: '<span class="empty-value">No short description</span>' !!}
                                                </div>
                                            </div>

                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.Description') }}</div>
                                                <div class="detail-value">
                                                    {!! $service->description ?: '<span class="empty-value">No description</span>' !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing Details -->
                                <div class="col-lg-4 col-12 mg-top-30">
                                    <div class="service-detail-card">
                                        <div class="service-detail-header">
                                            <h4>{{ __('translate.Pricing Details') }}</h4>
                                        </div>
                                        <div class="service-detail-body">
                                            @if ($service->is_per_person)
                                                @php
                                                    $labels = [
                                                        'infant' => __('translate.Infant'),
                                                        'baby'   => __('translate.Baby'),
                                                        'child'  => __('translate.Child'),
                                                        'adult'  => __('translate.Adult'),
                                                    ];
                                                    $cats = is_array($service->age_categories) ? $service->age_categories : [];
                                                @endphp

                                                @php $hasAny = false; @endphp
                                                @foreach(['infant','baby','child','adult'] as $k)
                                                    @php $c = $cats[$k] ?? null; @endphp
                                                    @if($c && !empty($c['enabled']))
                                                        @php $hasAny = true; @endphp
                                                        <div class="d-flex justify-content-between align-items-start mb-2" style="border-bottom:1px solid #f5f5f5;padding-bottom:8px">
                                                            <div>
                                                                <div class="fw-semibold">{{ $labels[$k] }}</div>
                                                                <div class="text-muted small">
                                                                    {{ __('translate.Min Age (years)') }}: {{ (int)($c['min_age'] ?? 0) }} /
                                                                    {{ __('translate.Max Age (years)') }}: {{ (int)($c['max_age'] ?? 0) }}
                                                                </div>
                                                            </div>
                                                            <div class="text-end">
                                                                @if(isset($c['price']))
                                                                    <div class="price-display" style="font-size:18px">{{ currency((float)$c['price']) }}</div>
                                                                    <div class="text-muted small">{{ __('translate.Per Person') }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach

                                                @if(!$hasAny)
                                                    <div class="text-muted">{{ __('translate.N/A') }}</div>
                                                @endif
                                            @else
                                                @if ($service->price_display)
                                                    <div class="price-display">{!! $service->price_display !!}</div>
                                                    <div class="text-muted small">{{ __('translate.Full Price') }}</div>
                                                @else
                                                    <div class="text-muted">{{ __('translate.N/A') }}</div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Service Media Preview -->
                                    @if ($service->media->count() > 0)
                                        <div class="service-detail-card">
                                            <div class="service-detail-header">
                                                <h4>Media Preview</h4>
                                            </div>
                                            <div class="service-detail-body">
                                                <div class="media-preview">
                                                    @foreach ($service->media->take(6) as $media)
                                                        <div class="media-item">
                                                            <img src="{{ asset($media->file_path) }}" alt="Service Image">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if ($service->media->count() > 6)
                                                    <p class="text-center mt-2">
                                                        <small>+{{ $service->media->count() - 6 }} more images</small>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Additional Information -->
                                <div class="col-12 mg-top-30">
                                    <div class="service-detail-card">
                                        <div class="service-detail-header">
                                            <h4>{{ __('translate.Additional Information') }}</h4>
                                        </div>
                                        <div class="service-detail-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Check-in Time') }}</div>
                                                        <div class="detail-value">{{ $service->check_in_time ?: 'Not set' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Check-out Time') }}</div>
                                                        <div class="detail-value">{{ $service->check_out_time ?: 'Not set' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Ticket') }}</div>
                                                        <div class="detail-value">{{ $service->ticket ?: 'Not set' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item">
                                                        <div class="detail-label">{{ __('translate.Video URL') }}</div>
                                                        <div class="detail-value">
                                                            @if ($service->video_url)
                                                                <a href="{{ $service->video_url }}" target="_blank">{{ $service->video_url }}</a>
                                                            @else
                                                                Not set
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($service->languages && is_array($service->languages))
                                                <div class="detail-item">
                                                    <div class="detail-label">{{ __('translate.Languages') }}</div>
                                                    <div class="detail-value">
                                                        @foreach ($service->languages as $language)
                                                            <span class="language-tag">{{ $language }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($service->translation && $service->translation->amenities)
                                                <div class="detail-item">
                                                    <div class="detail-label">{{ __('translate.Amenities') }}</div>
                                                    <div class="detail-value">
                                                        @foreach ($service->translation->amenities as $amenityId)
                                                            @php
                                                                $amenity = \Modules\TourBooking\App\Models\Amenity::find($amenityId);
                                                            @endphp
                                                            @if ($amenity)
                                                                <span class="amenity-tag">{{ $amenity->translation->name ?? $amenity->name }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($service->included)
                                                <div class="detail-item">
                                                    <div class="detail-label">{{ __('translate.What is included') }}</div>
                                                    <div class="detail-value">
                                                        @if (is_string($service->included))
                                                            {!! nl2br(e($service->included)) !!}
                                                        @else
                                                            <ul class="list-items">
                                                                @foreach ($service->included as $item)
                                                                    <li>{{ $item }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($service->excluded)
                                                <div class="detail-item">
                                                    <div class="detail-label">{{ __('translate.What is excluded') }}</div>
                                                    <div class="detail-value">
                                                        @if (is_string($service->excluded))
                                                            {!! nl2br(e($service->excluded)) !!}
                                                        @else
                                                            <ul class="list-items">
                                                                @foreach ($service->excluded as $item)
                                                                    <li>{{ $item }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($service->tour_plan_sub_title)
                                                <div class="detail-item">
                                                    <div class="detail-label">{{ __('translate.Tour Plan Sub Title') }}</div>
                                                    <div class="detail-value">{{ $service->tour_plan_sub_title }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-lg-6 col-12 mg-top-30">
                                    <div class="service-detail-card">
                                        <div class="service-detail-header">
                                            <h4>{{ __('translate.Contact Information') }}</h4>
                                        </div>
                                        <div class="service-detail-body">
                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.Address') }}</div>
                                                <div class="detail-value">
                                                    {!! $service->address ? nl2br(e($service->address)) : '<span class="empty-value">Not provided</span>' !!}
                                                </div>
                                            </div>

                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.Email') }}</div>
                                                <div class="detail-value">
                                                    @if ($service->email)
                                                        <a href="mailto:{{ $service->email }}">{{ $service->email }}</a>
                                                    @else
                                                        <span class="empty-value">Not provided</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.Phone') }}</div>
                                                <div class="detail-value">
                                                    @if ($service->phone)
                                                        <a href="tel:{{ $service->phone }}">{{ $service->phone }}</a>
                                                    @else
                                                        <span class="empty-value">Not provided</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.Website') }}</div>
                                                <div class="detail-value">
                                                    @if ($service->website)
                                                        <a href="{{ $service->website }}" target="_blank">{{ $service->website }}</a>
                                                    @else
                                                        <span class="empty-value">Not provided</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($service->google_map_sub_title)
                                                <div class="detail-item">
                                                    <div class="detail-label">Google Map Sub Title</div>
                                                    <div class="detail-value">{{ $service->google_map_sub_title }}</div>
                                                </div>
                                            @endif

                                            @if ($service->google_map_url)
                                                <div class="detail-item">
                                                    <div class="detail-label">Google Map URL</div>
                                                    <div class="detail-value">
                                                        <a href="{{ $service->google_map_url }}" target="_blank">View on Google Maps</a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO Information -->
                                <div class="col-lg-6 col-12 mg-top-30">
                                    <div class="service-detail-card">
                                        <div class="service-detail-header">
                                            <h4>{{ __('translate.SEO Information') }}</h4>
                                        </div>
                                        <div class="service-detail-body">
                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.SEO Title') }}</div>
                                                <div class="detail-value">
                                                    {{ $service->seo_title ?: $service->title }}
                                                </div>
                                            </div>

                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.SEO Description') }}</div>
                                                <div class="detail-value">
                                                    {!! $service->seo_description ?: '<span class="empty-value">Not set</span>' !!}
                                                </div>
                                            </div>

                                            <div class="detail-item">
                                                <div class="detail-label">{{ __('translate.SEO Keywords') }}</div>
                                                <div class="detail-value">
                                                    {{ $service->seo_keywords ?: 'Not set' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- row -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
