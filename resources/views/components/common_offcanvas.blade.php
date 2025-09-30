    <!-- offCanvas-menu -->
    <div class="offCanvas__info">
        <div class="offCanvas__close-icon menu-close">
            <button><i class="fa-sharp fa-regular fa-xmark"></i></button>
        </div>
        <div class="offCanvas__logo mb-30">
            <a href="{{ route('home') }}"><img src="{{ asset($general_setting?->secondary_logo) }}"
                    alt="Logo"></a>
        </div>
        <div class="offCanvas__side-info mb-30">
            <div class="contact-list mb-30">
                <h4>{{ __('translate.Office Address') }}</h4>
                <p>{{ $footer->address }}</p>
            </div>
            <div class="contact-list mb-30">
                <h4>{{ __('translate.Phone Number') }}</h4>
                <p>{{ $footer->phone }}</p>
            </div>
            <div class="contact-list mb-30">
                <h4>{{ __('translate.Email Address') }}</h4>
                <p>{{ $footer->email }}</p>
            </div>
        </div>
        <div class="offCanvas__social-icon mt-30">
            @if ($footer->facebook)
                <a href="{{ $footer->facebook }}"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if ($footer->twitter)
                <a href="{{ $footer->twitter }}"><i class="fab fa-twitter"></i></a>
            @endif
            @if ($footer->instagram)
                <a href="{{ $footer->instagram }}"><i class="fab fa-instagram"></i></a>
            @endif
            @if ($footer->linkedin)
                <a href="{{ $footer->linkedin }}"><i class="fab fa-linkedin-in"></i></a>
            @endif
            @if ($footer->youtube)
                <a href="{{ $footer->youtube }}"><i class="fab fa-youtube"></i></a>
            @endif
        </div>
    </div>
    <div class="offCanvas__overly"></div>
    <!-- offCanvas-menu-end -->
