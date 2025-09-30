<div class="payment_right">
    <div class="payment_select">
        <h2 class="tg-checkout-form-title tg-checkout-form-title-3 mb-15">{{ __('translate.Select Payment Method') }}
        </h2>
        <div class="tg-tour-about-border-doted mb-20"></div>
        <div class="payment_select_item_main">

            @if ($payment_setting->stripe_status == 1)
                <div class="payment_select_item_box">

                    <a href="javascript:;" class="payment_select_item">
                        <div class="payment_select_item_thumb">
                            <img src="{{ asset($payment_setting->stripe_image) }}" class="w-100" alt="">
                        </div>
                    </a>

                    <div class="payment_select_modal  tg-checkout-form-input">
                        <div class="payment_select_modal_head">
                            <h2>{{ __('translate.Stripe Payment') }}</h2>
                            <button type="button" class="close_modal_btn">
                                <span>
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 1L1.00081 16" stroke="#FE2C55" stroke-width="1.8"
                                            stroke-linecap="round" />
                                        <path d="M16 16L1.00081 1.00001" stroke="#FE2C55" stroke-width="1.8"
                                            stroke-linecap="round" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <form class="payment_select_modal_form stripe-modal-form require-validation " role="form"
                            action="{{ route('payment.stripe') }}" method="POST" data-cc-on-file="false"
                            data-stripe-publishable-key="{{ $payment_setting->stripe_key }}" id="payment-form">
                            @csrf

                            @include('tourbooking::front.bookings.customer-info')

                            <div class="payment_select_modal_form_item mt-0">
                                <div class="payment_select_modal_form_inner">
                                    <label for="card_number" class="form-label">
                                        {{ __('translate.Card Number') }}*</label>
                                    <input type="text" class="input card-number" id="card_number"
                                        placeholder="{{ __('translate.Card Number') }}" name="card_number"
                                        autocomplete="off">
                                </div>
                            </div>


                            <div class="payment_select_modal_form_item">
                                <div class="payment_select_modal_form_inner">
                                    <label for="expiry_month" class="form-label">{{ __('translate.Expired Month') }}
                                        *</label>
                                    <input type="text" class="input card-expiry-month" id="expiry_month"
                                        placeholder="{{ __('translate.MM') }}" name="month" autocomplete="off">
                                </div>
                            </div>


                            <div class="payment_select_modal_form_item">
                                <div class="payment_select_modal_form_inner">
                                    <label for="expired_year"
                                        class="form-label">{{ __('translate.Expired Year') }}*</label>
                                    <input type="text" class="input card-expiry-year" id="expired_year"
                                        placeholder="{{ __('translate.YYYY') }}" name="year" autocomplete="off">
                                </div>
                                <div class="payment_select_modal_form_inner">
                                    <label for="cvc" class="form-label">{{ __('translate.CVC') }}*</label>
                                    <input type="text" class="input card-cvc" id="cvc"
                                        placeholder="{{ __('translate.CVC') }}" name="cvc" autocomplete="off">
                                </div>
                            </div>

                            <div class="payment_select_modal_form_item stripe_error d-none">
                                <div class="stripe-modal-form-inner">
                                    <div class='alert-danger alert '>
                                        {{ __('translate.Please provide your valid card information') }}</div>
                                </div>
                            </div>

                            <button type="submit" class="tg-btn tg-btn-switch-animation mt-20 w-100">

                                <span>{{ __('translate.Pay Now') }}</span>
                                <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.1575 4.34302L3.84375 15.6567" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M15.157 11.4142C15.157 11.4142 16.0887 5.2748 15.157 4.34311C14.2253 3.41142 8.08594 4.34314 8.08594 4.34314"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endif



            @if ($payment_setting->paypal_status == 1)
                <div class="payment_select_item_box">
                    <form action="{{ route('payment.paypal') }}">
                        @csrf
                        @include('tourbooking::front.bookings.customer-info')
                        <button type="submit">
                            <div class="payment_select_item_thumb">
                                <img src="{{ asset($payment_setting->paypal_image) }}" class="w-100" alt="">
                            </div>
                        </button>
                    </form>
                </div>
            @endif

            @if ($payment_setting->razorpay_status == 1)
                <div class="payment_select_item_box" id="razorpay_btn">
                    <a href="javascript:;">
                        <div class="payment_select_item_thumb">
                            <img src="{{ asset($payment_setting->razorpay_image) }}" class="w-100" alt="">
                        </div>
                    </a>
                </div>

                <form action="{{ route('payment.razorpay') }}" method="POST" class="d-none">
                    @csrf
                    @php
                        $payable_amount = $data['total'] * $razorpay_currency->currency_rate;
                        $payable_amount = round($payable_amount, 2);
                    @endphp

                    @include('tourbooking::front.bookings.customer-info')

                    <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ $payment_setting->razorpay_key }}"
                        data-currency="{{ $razorpay_currency->currency_code }}" data-amount="{{ $payable_amount * 100 }}"
                        data-buttontext="{{ __('translate.Pay') }}" data-name="{{ $payment_setting->razorpay_name }}"
                        data-description="{{ $payment_setting->razorpay_description }}"
                        data-image="{{ asset($payment_setting->razorpay_image) }}" data-prefill.name="" data-prefill.email=""
                        data-theme.color="{{ $payment_setting->razorpay_theme_color }}"></script>
                </form>
            @endif





            @if ($payment_setting->flutterwave_status == 1)
                <div class="payment_select_item_box" id="payWithFlutterwave">
                    <a href="javascript:;">
                        <div class="payment_select_item_thumb">
                            <img src="{{ asset($payment_setting->flutterwave_logo) }}" class="w-100"
                                alt="">
                        </div>
                    </a>
                </div>
            @endif

            @if ($payment_setting->paystack_status == 1)
                <div class="payment_select_item_box" id="paystackPayment">
                    <a href="javascript:;">
                        <div class="payment_select_item_thumb">
                            <img src="{{ asset($payment_setting->paystack_image) }}" class="w-100" alt="">
                        </div>
                    </a>
                </div>
            @endif



            @if ($payment_setting->mollie_status == 1)
                <div class="payment_select_item_box">
                    <form action="{{ route('payment.mollie') }}">
                        @include('tourbooking::front.bookings.customer-info')
                        <button type="submit">
                            <div class="payment_select_item_thumb">
                                <img src="{{ asset($payment_setting->mollie_image) }}" class="w-100"
                                    alt="">
                            </div>
                        </button>
                    </form>
                </div>
            @endif


            @if ($payment_setting->instamojo_status == 1)
                <div class="payment_select_item_box">
                    <form action="{{ route('payment.instamojo') }}">
                        @include('tourbooking::front.bookings.customer-info')
                        <button type="submit">
                            <div class="payment_select_item_thumb">
                                <img src="{{ asset($payment_setting->instamojo_image) }}" class="w-100"
                                    alt="">
                            </div>
                        </button>
                    </form>
                </div>
            @endif

            @if ($payment_setting->bank_status == 1)
                <div class="payment_select_item_box">

                    <a href="javascript:;" class="payment_select_item">
                        <div class="payment_select_item_thumb">
                            <img src="{{ asset($payment_setting->bank_image) }}" class="w-100" alt="">
                        </div>
                    </a>

                    <div class="payment_select_modal">
                        <div class="payment_select_modal_head">
                            <h2>{{ __('translate.Bank Payment') }}</h2>
                            <button type="button" class="close_modal_btn">
                                <span>
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 1L1.00081 16" stroke="#FE2C55" stroke-width="1.8"
                                            stroke-linecap="round" />
                                        <path d="M16 16L1.00081 1.00001" stroke="#FE2C55" stroke-width="1.8"
                                            stroke-linecap="round" />
                                    </svg>
                                </span>
                            </button>
                        </div>


                        <ul class="banck_text">
                            {!! clean(nl2br($payment_setting->bank_account_info)) !!}
                        </ul>


                        <form class="payment_select_modal_form mt-0" action="{{ route('payment.bank') }}"
                            method="POST">
                            @csrf

                            @include('tourbooking::front.bookings.customer-info')

                            <div class="payment_select_modal_form_item  mt-0">
                                <div class="payment_select_modal_form_inner tg-checkout-form-input">
                                    <label for="tnx_info"
                                        class="form-label">{{ __('translate.Transaction information') }}*</label>
                                    <textarea class="input" id="tnx_info" rows="3" name="tnx_info"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="tg-btn tg-btn-switch-animation w-100">

                                <span>{{ __('translate.Submit Now') }}</span>
                                <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.1575 4.34302L3.84375 15.6567" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M15.157 11.4142C15.157 11.4142 16.0887 5.2748 15.157 4.34311C14.2253 3.41142 8.08594 4.34314 8.08594 4.34314"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endif


        </div>
    </div>
</div>

@push('js_section')

    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script>
        "use strict";
        $(function() {

            var $form = $(".require-validation");
            $('form.require-validation').on('submit', function(e) {
                var $form = $(".require-validation"),
                    inputSelector = ['input[type=email]', 'input[type=password]',
                        'input[type=text]', 'input[type=file]',
                        'textarea'
                    ].join(', '),
                    $inputs = $form.find('.required').find(inputSelector),
                    $errorMessage = $form.find('div.stripe_error'),
                    valid = true;
                $errorMessage.addClass('d-none');

                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('d-none');
                        e.preventDefault();
                    }
                });

                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }

            });

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $('.stripe_error')
                        .removeClass('d-none')
                        .find('.alert')
                        .text(response.error.message);
                } else {
                    var token = response['id'];
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    $form.get(0).submit();
                }
            }

            $("#razorpay_btn").on("click", function() {
                $(".razorpay-payment-button").click();
            })

        });
    </script>

    @if ($payment_setting->flutterwave_status == 1 && $user)
        <script src="https://checkout.flutterwave.com/v3.js"></script>

        @php
            $payable_amount = $data['total'] * $flutterwave_currency->currency_rate;
            $payable_amount = round($payable_amount, 2);
        @endphp

        <script>
            "use strict";
            $(function() {
                $("#payWithFlutterwave").on("click", function() {

                    var isDemo = "{{ env('APP_MODE') }}"
                    if (isDemo == 'DEMO') {
                        toastr.error('This Is Demo Version. You Can Not Change Anything');
                        return;
                    }

                    FlutterwaveCheckout({
                        public_key: "{{ $payment_setting->flutterwave_public_key }}",
                        tx_ref: "{{ substr(rand(0, time()), 0, 10) }}",
                        amount: {{ $payable_amount }},
                        currency: "{{ $flutterwave_currency->currency_code }}",
                        country: "{{ $flutterwave_currency->country_code }}",
                        payment_options: " ",
                        customer: {
                            email: "{{ $user->email }}",
                            phone_number: "{{ $user->phone }}",
                            name: "{{ $user->name }}",
                        },
                        callback: function(data) {

                            var tnx_id = data.transaction_id;
                            var _token = "{{ csrf_token() }}";
                            $.ajax({
                                type: 'post',
                                data: {
                                    tnx_id,
                                    _token
                                },
                                url: "{{ url('payment/flutterwave/') }}",
                                success: function(response) {

                                    if (response.status == 'success') {
                                        toastr.success(response.message);
                                        window.location.href =
                                            "{{ route('user.dashboard') }}";
                                    } else {
                                        toastr.error(response.message);
                                        window.location.reload();
                                    }
                                },
                                error: function(err) {
                                    toastr.error(
                                        "{{ __('translate.Something went wrong, please try again') }}"
                                    );
                                    window.location.reload();
                                }
                            });
                        },
                        customizations: {
                            title: "{{ $payment_setting->flutterwave_title }}",
                            logo: "{{ asset($payment_setting->flutterwave_logo) }}",
                        },
                    });

                })
            });
        </script>
    @endif




    {{-- start paystack payment --}}

    @if ($payment_setting->paystack_status == 1)
        <script src="https://js.paystack.co/v1/inline.js"></script>

        @php

            $public_key = $payment_setting->paystack_public_key;
            $currency = $paystack_currency->currency_code;
            $currency = strtoupper($currency);

            $ngn_amount = $data['total'] * $paystack_currency->currency_rate;
            $ngn_amount = $ngn_amount * 100;
            $ngn_amount = round($ngn_amount);

        @endphp

        <script>
            "use strict";
            $(function() {
                $("#paystackPayment").on("click", function() {

                    var isDemo = "{{ env('APP_MODE') }}"
                    if (isDemo == 'DEMO') {
                        toastr.error('This Is Demo Version. You Can Not Change Anything');
                        return;
                    }

                    var handler = PaystackPop.setup({
                        key: '{{ $public_key }}',
                        email: '{{ $user->email ?? '' }}',
                        amount: '{{ $ngn_amount }}',
                        currency: "{{ $currency }}",
                        callback: function(response) {
                            let reference = response.reference;
                            let tnx_id = response.transaction;
                            let _token = "{{ csrf_token() }}";
                            $.ajax({
                                type: "POST",
                                data: {
                                    reference,
                                    tnx_id,
                                    _token,
                                    customer_name: $('.form_customer_name').val(),
                                    customer_email: $('.form_customer_email').val(),
                                    customer_phone: $('.form_customer_phone').val(),
                                    customer_address: $('.form_customer_address').val(),
                                },
                                url: "{{ url('payment/paystack') }}",
                                success: function(response) {
                                    if (response.status == 'success') {
                                        toastr.success(response.message);
                                        window.location.href =
                                            "{{ route('user.dashboard') }}";
                                    } else {
                                        toastr.error(response.message);
                                        window.location.reload();
                                    }
                                },
                                error: function(response) {
                                    toastr.error('Server Error');
                                    window.location.reload();
                                }
                            });
                        },
                        onClose: function() {
                            alert('window closed');
                        }
                    });
                    handler.openIframe();

                })
            });
        </script>
    @endif

    {{-- end paystack payment --}}

@endpush
