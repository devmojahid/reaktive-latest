<!-- Script -->
<script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/imagesloaded.pkgd.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/jquery.odometer.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/jquery.appear.js') }}"></script>
<script src="{{ asset('frontend/assets/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/flatpickr.js') }}"></script>
<script src="{{ asset('frontend/assets/js/nice-select.js') }}"></script>
<script src="{{ asset('frontend/assets/js/ajax-form.js') }}"></script>
<script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/cart.js') }}"></script>
<script src="{{ asset('frontend/assets/js/main.js') }}"></script>
<script src="{{ asset('global/toastr/toastr.min.js') }}"></script>

<script>
    (function($) {
        "use strict"
        $(document).ready(function() {

            const session_notify_message = @json(Session::get('message'));
            const demo_mode_message = @json(Session::get('demo_mode'));

            if (session_notify_message != null) {
                const session_notify_type = @json(Session::get('alert-type', 'info'));
                switch (session_notify_type) {
                    case 'info':
                        toastr.info(session_notify_message);
                        break;
                    case 'success':
                        toastr.success(session_notify_message);
                        break;
                    case 'warning':
                        toastr.warning(session_notify_message);
                        break;
                    case 'error':
                        toastr.error(session_notify_message);
                        break;
                }
            }

            if (demo_mode_message != null) {
                toastr.warning(
                    "{{ __('translate.All Language keywords are not implemented in the demo mode') }}"
                );
                toastr.info("{{ __('translate.Admin can translate every word from the admin panel') }}");
            }

            const validation_errors = @json($errors->all());

            if (validation_errors.length > 0) {
                validation_errors.forEach(error => toastr.error(error));
            }

            if (localStorage.getItem('trips-cookie') != '1') {
                $('.cookie_consent_modal').removeClass('d-none');
            }

            $('.cookie_consent_close_btn').on('click', function() {
                $('.cookie_consent_modal').addClass('d-none');
            });

            $('.cookie_consent_accept_btn').on('click', function() {
                localStorage.setItem('trips-cookie', '1');
                $('.cookie_consent_modal').addClass('d-none');
            });

            $('.before_auth_wishlist').on("click", function() {
                toastr.error("{{ __('translate.Please login first') }}")
            });

            $(".currency_code").on('change', function() {
                var currency_code = $(this).val();

                window.location.href = "{{ route('currency-switcher') }}" + "?currency_code=" +
                    currency_code;
            });

            $(".language_code").on('change', function() {
                var language_code = $(this).val();

                window.location.href = "{{ route('language-switcher') }}" + "?lang_code=" +
                    language_code;
            });

        });
    })(jQuery);
</script>


@stack('js_section')