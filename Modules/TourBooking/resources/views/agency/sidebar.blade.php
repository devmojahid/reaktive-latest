<h4 class="admin-menu__title pt-2">{{ __('translate.Booking Services') }}</h4>
<li
    class="{{ Route::is('agency.tourbooking.services.index') || Route::is('agency.tourbooking.services.create') || Route::is('agency.tourbooking.services.edit') ? 'active' : '' }}">
    <a href="#!" class="collapsed" data-bs-toggle="collapse" data-bs-target="#menu-item__booking_services_list"><span
            class="menu-bar__text">
            <span class="crancy-menu-icon crancy-svg-icon__v1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2 21C2.5 20.0909 4.4 18.2727 8 18.2727C11.6 18.2727 13.5 16.0909 14 15M8 8V5C8 3.89543 8.89543 3 10 3H20C21.1046 3 22 3.89543 22 5V13C22 14.1046 21.1046 15 20 15H16.7397M12 7H18M10 13C10 14.1046 9.10457 15 8 15C6.89543 15 6 14.1046 6 13C6 11.8954 6.89543 11 8 11C9.10457 11 10 11.8954 10 13Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M15 11H18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </span>

            <span class="menu-bar__name">{{ __('translate.Booking Services') }}</span></span> <span
            class="crancy__toggle"></span></a></span>
    <!-- Dropdown Menu -->
    <div class="collapse crancy__dropdown {{ Route::is('agency.tourbooking.services.index') || Route::is('agency.tourbooking.services.create') || Route::is('agency.tourbooking.services.edit') ? 'show' : '' }}"
        id="menu-item__booking_services_list" data-bs-parent="#CrancyMenu">
        <ul class="menu-bar__one-dropdown">
            <li><a href="{{ route('agency.tourbooking.services.index') }}"><span class="menu-bar__text"><span
                            class="menu-bar__name">{{ __('translate.Booking Services') }}</span></span></a></li>

            <li><a href="{{ route('agency.tourbooking.services.create') }}"><span class="menu-bar__text"><span
                            class="menu-bar__name">{{ __('translate.Add New Service') }}</span></span></a></li>
        </ul>
    </div>
</li>

<li
    class="{{ Route::is('agency.tourbooking.destinations.index') || Route::is('agency.tourbooking.destinations.create') || Route::is('agency.tourbooking.destinations.edit') ? 'active' : '' }}">
    <a href="#!" class="collapsed" data-bs-toggle="collapse" data-bs-target="#menu-item__destination_list"><span
            class="menu-bar__text">
            <span class="crancy-menu-icon crancy-svg-icon__v1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2 21C2.5 20.0909 4.4 18.2727 8 18.2727C11.6 18.2727 13.5 16.0909 14 15M8 8V5C8 3.89543 8.89543 3 10 3H20C21.1046 3 22 3.89543 22 5V13C22 14.1046 21.1046 15 20 15H16.7397M12 7H18M10 13C10 14.1046 9.10457 15 8 15C6.89543 15 6 14.1046 6 13C6 11.8954 6.89543 11 8 11C9.10457 11 10 11.8954 10 13Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M15 11H18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </span>

            <span class="menu-bar__name">{{ __('translate.Destinations') }}</span></span> <span
            class="crancy__toggle"></span></a></span>
    <!-- Dropdown Menu -->
    <div class="collapse crancy__dropdown {{ Route::is('agency.tourbooking.destinations.index') || Route::is('agency.tourbooking.destinations.create') || Route::is('agency.tourbooking.destinations.edit') ? 'show' : '' }}"
        id="menu-item__destination_list" data-bs-parent="#CrancyMenu">
        <ul class="menu-bar__one-dropdown">

            <li><a href="{{ route('agency.tourbooking.destinations.index') }}"><span class="menu-bar__text"><span
                            class="menu-bar__name">{{ __('translate.Destinations') }}</span></span></a></li>

            <li><a href="{{ route('agency.tourbooking.destinations.create') }}"><span class="menu-bar__text"><span
                            class="menu-bar__name">{{ __('translate.Create Destination') }}</span></span></a></li>
        </ul>
    </div>
</li>

<li class="{{ Route::is('agency.tourbooking.bookings.index') ? 'active' : '' }}">
    <a class="collapsed" href="{{ route('agency.tourbooking.bookings.index') }}">
        <span class="menu-bar__text">
            <span class="crancy-menu-icon crancy-svg-icon__v1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2 21C2.5 20.0909 4.4 18.2727 8 18.2727C11.6 18.2727 13.5 16.0909 14 15M8 8V5C8 3.89543 8.89543 3 10 3H20C21.1046 3 22 3.89543 22 5V13C22 14.1046 21.1046 15 20 15H16.7397M12 7H18M10 13C10 14.1046 9.10457 15 8 15C6.89543 15 6 14.1046 6 13C6 11.8954 6.89543 11 8 11C9.10457 11 10 11.8954 10 13Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M15 11H18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </span>
            <span class="menu-bar__name">{{ __('translate.Bookings') }}</span>
        </span>
    </a>
</li>
