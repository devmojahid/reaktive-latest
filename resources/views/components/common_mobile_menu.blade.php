<div class="tgmobile__menu">
    <nav class="tgmobile__menu-box">
        <div class="close-btn"><i class="fa-solid fa-xmark"></i></div>
        <div class="nav-logo">
            <a href="{{ route('home') }}"><img src="{{ asset($general_setting->secondary_logo) }}"
                    alt="logo"></a>
        </div>
        <div class="tgmobile__menu-outer">
            <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
        </div>
        <div class="social-links">
            <ul class="list-wrap">
                @if ($footer->facebook)
                    <li><a href="{{ $footer->facebook }}"><i class="fab fa-facebook-f"></i></a></li>
                @endif
                @if ($footer->twitter)
                    <li><a href="{{ $footer->twitter }}"><i class="fab fa-twitter"></i></a></li>
                @endif
                @if ($footer->instagram)
                    <li><a href="{{ $footer->instagram }}"><i class="fab fa-instagram"></i></a></li>
                @endif
                @if ($footer->linkedin)
                    <li><a href="{{ $footer->linkedin }}"><i class="fab fa-linkedin-in"></i></a></li>
                @endif
                @if ($footer->youtube)
                    <li><a href="{{ $footer->youtube }}"><i class="fab fa-youtube"></i></a></li>
                @endif
            </ul>
        </div>
    </nav>
</div>
<div class="tgmobile__menu-backdrop"></div>