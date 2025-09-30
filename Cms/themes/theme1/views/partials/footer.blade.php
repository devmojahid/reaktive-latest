{{-- <footer class="site-footer">
    <div class="footer-widgets py-5">
        <div class="container">
            <div class="row">
                @php
                    $widgets = app('Theme\Theme1\Services\Theme1Service')->getFooterWidgets();
                @endphp

                <!-- About Widget -->
                <div class="col-md-4">
                    <div class="footer-widget">
                        <h4>{{ $widgets['about']['title'] }}</h4>
                        <p>{{ $widgets['about']['content'] }}</p>

                        <!-- Social Links -->
                        <div class="social-links mt-3">
                            @foreach(app('Theme\Theme1\Services\Theme1Service')->getSocialLinks() as $social)
                                <a href="{{ $social['url'] }}" class="social-link" target="_blank">
                                    <i class="{{ $social['icon'] }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quick Links Widget -->
                <div class="col-md-4">
                    <div class="footer-widget">
                        <h4>{{ $widgets['quick_links']['title'] }}</h4>
                        <ul class="footer-links">
                            @foreach($widgets['quick_links']['items'] as $link)
                                <li>
                                    <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Contact Widget -->
                <div class="col-md-4">
                    <div class="footer-widget">
                        <h4>{{ $widgets['contact']['title'] }}</h4>
                        <div class="contact-info">
                            <p>
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ $widgets['contact']['address'] }}
                            </p>
                            <p>
                                <i class="fas fa-phone me-2"></i>
                                {{ $widgets['contact']['phone'] }}
                            </p>
                            <p>
                                <i class="fas fa-envelope me-2"></i>
                                {{ $widgets['contact']['email'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Bar -->
    <div class="copyright-bar py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} {{ $seo_setting->site_name ?? 'Trips' }}.
                        All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-bottom-links">
                        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                        <span class="mx-2">|</span>
                        <a href="{{ route('terms-conditions') }}">Terms & Conditions</a>
                    </div>
                </div>
            </div>
        </div>
</div>
</footer> --}}

@push('styles')
<style>
    .site-footer {
        background-color: var(--dark-color);
        color: #fff;
    }

    .footer-widget h4 {
        color: #fff;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.5rem;
    }

    .footer-links a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: var(--primary-color);
    }

    .social-links {
        display: flex;
        gap: 1rem;
    }

    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: rgba(255,255,255,0.1);
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background-color: var(--primary-color);
        color: #fff;
        transform: translateY(-2px);
    }

    .contact-info p {
        margin-bottom: 0.5rem;
    }

    .copyright-bar {
        background-color: rgba(0,0,0,0.2);
        font-size: 0.875rem;
    }

    .footer-bottom-links a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-bottom-links a:hover {
        color: var(--primary-color);
    }
</style>
@endpush