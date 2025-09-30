<footer>
    <div class="tg-footer-area tg-footer-space include-bg" data-background="{{ asset('frontend/assets/img/shape/home-1-footer-bg.jpeg') }}">
        <div class="container">
            <div class="tg-footer-top mb-40">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="tg-footer-widget mb-40">
                            <div class="tg-footer-logo mb-20">
                                <a href="{{ route('home') }}"><img src="{{ asset($general_setting->footer_logo) }}" alt=""></a>
                            </div>
                            <p class="mb-20">{{ $footer->about_us }}</p>
                            <div class="tg-footer-form mb-30">
                                <form action="{{ route('store-newsletter') }}" method="POST">
                                    @csrf
                                    <input type="email" placeholder="Enter your mail" name="email">
                                    <button class="tg-footer-form-btn" type="submit">
                                        <svg width="22" height="17" viewBox="0 0 22 17" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M1.52514 8.47486H20.4749M20.4749 8.47486L13.5 1.5M20.4749 8.47486L13.5 15.4497"
                                                stroke="white" stroke-width="1.77778" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            <div class="tg-footer-social">
                                @if ($footer->facebook)
                                    <a href="{{ $footer->facebook }}"><i class="fa-brands fa-facebook-f"></i></a>
                                @endif
                                @if ($footer->twitter)
                                    <a href="{{ $footer->twitter }}"><i class="fa-brands fa-twitter"></i></a>
                                @endif
                                @if ($footer->instagram)
                                    <a href="{{ $footer->instagram }}"><i class="fa-brands fa-instagram"></i></a>
                                @endif
                                @if ($footer->linkedin)
                                    <a href="{{ $footer->linkedin }}"><i class="fa-brands fa-linkedin-in"></i></a>
                                @endif
                                @if ($footer->youtube)
                                    <a href="{{ $footer->youtube }}"><i class="fa-brands fa-youtube"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="tg-footer-widget tg-footer-link ml-80 mb-40">
                            <h3 class="tg-footer-widget-title mb-25">{{ __('translate.Quick Links') }}</h3>
                            {!! wp_nav_menu([
                                'theme_location' => 'footer_menu_1',
                                'menu_class' => '',
                                'container' => false,
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                'menu_id' => 'main-nav',
                                'before' => '',
                                'after' => '',
                                'link_before' => '',
                                'link_after' => '',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="tg-footer-widget tg-footer-info mb-40">
                            <h3 class="tg-footer-widget-title mb-25">{{ __('translate.Information') }}</h3>
                            <ul>
                                @if ($footer->address || $footer->address_url)
                                <li>
                                    <a class="d-flex"
                                        href="{{ $footer->address_url }}">
                                        <span class="mr-15">
                                            <svg width="20" height="24" viewBox="0 0 20 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19.0013 10.0608C19.0013 16.8486 10.3346 22.6668 10.3346 22.6668C10.3346 22.6668 1.66797 16.8486 1.66797 10.0608C1.66797 7.74615 2.58106 5.52634 4.20638 3.88965C5.83169 2.25297 8.03609 1.3335 10.3346 1.3335C12.6332 1.3335 14.8376 2.25297 16.4629 3.88965C18.0882 5.52634 19.0013 7.74615 19.0013 10.0608Z"
                                                    stroke="white" stroke-width="1.73333" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M10.3346 12.9699C11.9301 12.9699 13.2235 11.6674 13.2235 10.0608C13.2235 8.45412 11.9301 7.15168 10.3346 7.15168C8.73915 7.15168 7.44575 8.45412 7.44575 10.0608C7.44575 11.6674 8.73915 12.9699 10.3346 12.9699Z"
                                                    stroke="white" stroke-width="1.73333" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        {{ $footer->address }}
                                    </a>
                                </li>
                                @endif
                                @if ($footer->phone)
                                <li>
                                    <a class="d-flex" href="tel:{{ $footer->phone }}">
                                        <span class="mr-15">
                                            <i class="fa-sharp text-white fa-solid fa-phone"></i>
                                        </span>
                                        {{ $footer->phone }}
                                    </a>
                                </li>
                                @endif
                                @if ($footer->working_days)
                                <li class="d-flex">
                                    <span class="mr-15">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M11.9987 5.60006V12.0001L16.2654 14.1334M22.6654 12.0002C22.6654 17.8912 17.8897 22.6668 11.9987 22.6668C6.10766 22.6668 1.33203 17.8912 1.33203 12.0002C1.33203 6.10912 6.10766 1.3335 11.9987 1.3335C17.8897 1.3335 22.6654 6.10912 22.6654 12.0002Z"
                                                stroke="white" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <p class="mb-0">
                                        {{ $footer->working_days }}
                                        {{-- Mon – Sat: 8 am – 5 pm,<br>
                                        Sunday: <span class="text-white d-inline-block">CLOSED</span> --}}
                                    </p>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="tg-footer-widget tg-footer-link mb-40">
                            <h3 class="tg-footer-widget-title mb-25">{{ __('translate.Utility Pages') }}</h3>
                            {!! wp_nav_menu([
                                'theme_location' => 'footer_menu_2',
                                'menu_class' => '',
                                'container' => false,
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                'menu_id' => 'main-nav',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tg-footer-copyright text-center">
            <span>
                {{ $footer->copyright }}
            </span>
        </div>
    </div>
</footer>
<!-- End Footer Section -->
