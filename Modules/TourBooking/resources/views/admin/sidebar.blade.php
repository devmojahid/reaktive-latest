@php
    use Illuminate\Support\Facades\Route;

    // Tipuri de servicii active (pt. link-urile dinamice)
    $servicesTypes = \Modules\TourBooking\App\Models\ServiceType::where('status', true)
        ->orderBy('display_order', 'asc')
        ->pluck('name', 'slug');

    // Flag-uri pentru active state / collapses
    $isTBServices  = Route::is('admin.tourbooking.services.*'); // include și media/filters
    $isTBTypes     = Route::is('admin.tourbooking.service-types.*')
                    || Route::is('admin.tourbooking.trip-type.*')
                    || Route::is('admin.tourbooking.amenities.*');
    $isTBGroup     = $isTBServices || $isTBTypes;

    $isTBDest      = Route::is('admin.tourbooking.destinations.*');
    $isTBBookings  = Route::is('admin.tourbooking.bookings.*');
    $isTBReviews   = Route::is('admin.tourbooking.reviews.*');
@endphp

<div class="pt-section">{{ __('Experiences List') }}</div>
<ul class="pt-nav">
    {{-- EXPERIENCES (dropdown) --}}
    <li class="pt-item {{ $isTBGroup ? 'is-active' : '' }}">
        <a class="pt-link" data-bs-toggle="collapse" href="#pt-tb-exp" aria-expanded="{{ $isTBGroup ? 'true' : 'false' }}">
            <span class="pt-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="8" height="6" rx="2"></rect>
                    <path d="M3 14h18M7 18h10"></path>
                </svg>
            </span>
            <span class="pt-text">{{ __('Experiences List') }}</span>
            <span class="pt-caret">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </span>
        </a>

        <div class="collapse pt-collapse {{ $isTBGroup ? 'show' : '' }}" id="pt-tb-exp" data-bs-parent="#adminSidebar">
            <ul class="pt-nav">
                {{-- Lista completă servicii --}}
                <li class="pt-item">
                    <a class="pt-link" href="{{ route('admin.tourbooking.services.index') }}">{{ __('Experiences List') }}</a>
                </li>

                {{-- Clasificări / configurări --}}
                <li class="pt-item">
                    <a class="pt-link" href="{{ route('admin.tourbooking.service-types.index') }}">{{ __('Trip Type') }}</a>
                </li>
                <li class="pt-item">
                    <a class="pt-link" href="{{ route('admin.tourbooking.trip-type.index') }}">{{ __('Experience Type') }}</a>
                </li>
                <li class="pt-item">
                    <a class="pt-link" href="{{ route('admin.tourbooking.amenities.index') }}">{{ __('translate.Amenities') }}</a>
                </li>

                {{-- Filtre rapide existente în rute --}}
                <li class="pt-item">
                    <a class="pt-link" href="{{ route('admin.tourbooking.services.tours') }}">{{ __('translate.Tours') }}</a>
                </li>
                
               
                {{-- Tipuri de servicii (dinamic) --}}
                @foreach ($servicesTypes as $slug => $name)
                    <li class="pt-item">
                        <a class="pt-link" href="{{ route('admin.tourbooking.services.by-type', $slug) }}">{{ $name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </li>

    {{-- DESTINATIONS (dropdown) --}}
    <li class="pt-item {{ $isTBDest ? 'is-active' : '' }}">
        <a class="pt-link" data-bs-toggle="collapse" href="#pt-tb-dest" aria-expanded="{{ $isTBDest ? 'true' : 'false' }}">
            <span class="pt-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 5-9 12-9 12S3 15 3 10a9 9 0 1 1 18 0Z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </span>
            <span class="pt-text">{{ __('translate.Destinations') }}</span>
            <span class="pt-caret">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </span>
        </a>

        <div class="collapse pt-collapse {{ $isTBDest ? 'show' : '' }}" id="pt-tb-dest" data-bs-parent="#adminSidebar">
            <ul class="pt-nav">
                <li class="pt-item"><a class="pt-link" href="{{ route('admin.tourbooking.destinations.index') }}">{{ __('translate.Destinations') }}</a></li>
                <li class="pt-item"><a class="pt-link" href="{{ route('admin.tourbooking.destinations.create') }}">{{ __('translate.Create Destination') }}</a></li>
            </ul>
        </div>
    </li>

    {{-- BOOKINGS --}}
    <li class="pt-item {{ $isTBBookings ? 'is-active' : '' }}">
        <a class="pt-link" href="{{ route('admin.tourbooking.bookings.index') }}">
            <span class="pt-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <path d="M16 2v4M8 2v4M3 10h18"></path>
                </svg>
            </span>
            <span class="pt-text">{{ __('translate.Bookings') }}</span>
        </a>
    </li>

    {{-- REVIEWS --}}
    <li class="pt-item {{ $isTBReviews ? 'is-active' : '' }}">
        <a class="pt-link" href="{{ route('admin.tourbooking.reviews.index') }}">
            <span class="pt-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 17.25 6.18 3.25-1.64-6.77L21 9.75l-6.9-.6L12 2 9.9 9.15 3 9.75l4.46 4.98L5.82 20.5Z"/>
                </svg>
            </span>
            <span class="pt-text">{{ __('translate.Review list') }}</span>
        </a>
    </li>
</ul>
