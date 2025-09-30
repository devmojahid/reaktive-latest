@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <div class="section optech-section-padding pt-100 pb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="payment_box">

                        <div class="payment_box_head">
                            <h5>{{ __('translate.Select Payment Method') }}</h5>
                        </div>

                        <div class="payment_select_item_main">
                            @if ($payment_setting->paypal_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" class="payment_box_item" id="paypal_btn">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->paypal_image) }}" class="w-100"
                                                alt="">
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($payment_setting->stripe_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stripePayment"
                                        class="payment_box_item">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->stripe_image) }}" class="w-100"
                                                alt="">
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($payment_setting->mollie_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" class="payment_box_item" id="mollie_payment_btn">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->mollie_image) }}" class="w-100"
                                                alt="">
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($payment_setting->razorpay_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" class="payment_box_item" id="razorpay_btn">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->razorpay_image) }}" class="w-100"
                                                alt="Instamojo">
                                        </div>
                                    </a>
                                </div>

                                <form action="{{ route('ecommerce.pay-razorpay') }}" method="POST" class="d-none">
                                    @csrf
                                    @php

                                        $payable_amount = $payable_amount * $razorpay_currency->currency_rate;
                                        $payable_amount = round($payable_amount, 2);
                                    @endphp
                                    <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ $payment_setting->razorpay_key }}"
                                        data-currency="{{ $razorpay_currency->currency_code }}" data-amount="{{ $payable_amount * 100 }}"
                                        data-buttontext="{{ __('translate.Pay') }}" data-name="{{ $payment_setting->razorpay_name }}"
                                        data-description="{{ $payment_setting->razorpay_description }}"
                                        data-image="{{ asset($payment_setting->razorpay_image) }}" data-prefill.name="" data-prefill.email=""
                                        data-theme.color="{{ $payment_setting->razorpay_theme_color }}"></script>
                                </form>
                            @endif

                            @if ($payment_setting->instamojo_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" class="payment_box_item" id="instamojoPayment">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->instamojo_image) }}" class="w-100"
                                                alt="Instamojo">
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($payment_setting->paystack_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" class="payment_box_item" id="paystackPayment">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->paystack_image) }}" class="w-100"
                                                alt="Instamojo">
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($payment_setting->flutterwave_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" class="payment_box_item" id="payWithFlutterwave">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->flutterwave_logo) }}" class="w-100"
                                                alt="Instamojo">
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if ($payment_setting->bank_status == 1)
                                <div class="payment_select_item_box">
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#bankModal"
                                        class="payment_box_item">
                                        <div class="payment_select_item_thumb">
                                            <img src="{{ asset($payment_setting->bank_image) }}" class="w-100"
                                                alt="">
                                        </div>
                                    </a>
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1">
                    <div class="optech-checkuot-sidebar-column">
                        <div class="optech-checkuot-sidebar">
                            <h5>{{ __('translate.Your Order') }}</h5>
                            <ul>
                                <li>{{ __('translate.Product') }}<span>{{ __('translate.Subtotal') }}</span></li>

                                @foreach ($carts as $cart)
                                    <li>{{ Str::limit($cart->product->translate?->name, 25) }} - x {{ $cart->quantity }}
                                        <span>{{ currency($cart->product->finalPrice * $cart->quantity) }}</span></li>
                                @endforeach

                                <li>{{ __('translate.Subtotal') }}<span>{{ currency($sub_total) }}</span></li>
                                <li>{{ __('translate.Delivery Fee') }}<span>(+){{ currency($shipping_charge) }}</span>
                                </li>
                                <li>{{ __('translate.Total') }}<span class="total-amount">{{ currency($total) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End section -->

    <!-- Checkout Part Start  -->

    <!-- Modal Start -->

    <!-- stripe-modal  -->
    <div class="modal fade" id="stripePayment" tabindex="-1" aria-labelledby="jobDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-body">
                    <div class="bg-white p-lg-5 rounded-3">
                        <div class="proposal-container">
                            <div class="proposal-header">
                                <h3 class="text-dark-300 text-24 fw-bold">{{ __('translate.Pay via Stripe') }}</h3>
                            </div>
                            <form class="stripe-modal-form require-validation " role="form"
                                action="{{ route('ecommerce.stripe') }}" method="POST" data-cc-on-file="false"
                                data-stripe-publishable-key="{{ $payment_setting->stripe_key }}" id="payment-form">
                                @csrf

                                <div class="d-flex flex-column gap-4">

                                    <div class="proposal-input-container">
                                        <label for="amount"
                                            class="proposal-form-label">{{ __('translate.Card Number') }}*</label>
                                        <input type="text" class="form-control shadow-none card-number"
                                            placeholder="{{ __('translate.Card Number') }}" name="card_number" />
                                    </div>

                                    <div class="proposal-input-container">
                                        <label for="amount"
                                            class="proposal-form-label">{{ __('translate.Expired Month') }}*</label>
                                        <input type="text" class="form-control shadow-none card-expiry-month"
                                            placeholder="{{ __('translate.Expired Month') }}" name="month" />
                                    </div>

                                    <div class="proposal-input-container">
                                        <label for="amount"
                                            class="proposal-form-label">{{ __('translate.Expired Year') }}*</label>
                                        <input type="text" class="form-control shadow-none card-expiry-year"
                                            placeholder="{{ __('translate.Expired Year') }}" name="year" />
                                    </div>

                                    <div class="proposal-input-container">
                                        <label for="amount"
                                            class="proposal-form-label">{{ __('translate.CVC') }}*</label>
                                        <input type="text" class="form-control shadow-none card-cvc"
                                            placeholder="{{ __('translate.CVC') }}" name="cvc" />
                                    </div>

                                    <div class="proposal-input-container stripe_error d-none">
                                        <div class="stripe-modal-form-inner">
                                            <div class='alert-danger alert '>
                                                {{ __('translate.Please provide your valid card information') }}</div>
                                        </div>
                                    </div>


                                    <div class="d-flex gap-4 align-items-center justify-content-end">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            {{ __('translate.Cancel') }}
                                        </button>
                                        <button class="btn btn-primary">
                                            {{ __('translate.Pay Now') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- banck Modal -->
    <div class="modal banck-modal stripe-modal fade" id="bankModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('translate.Bank Payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="payment-modal-item">
                        <h4>{{ __('translate.Amount') }}<span>{{ currency($total) }}</span></h4>
                    </div>
                    <div class="bank-payment-modal-txt">
                        {!! clean(nl2br($bank->account_info)) !!}
                    </div>
                    <form class="payment-modal-from" action="{{ route('ecommerce.bank') }}" method="POST"
                        id="bank_payment_form">
                        @csrf
                        <div class="bank-payment-form-item">
                            <div class="bank-payment-form-inner">
                                <textarea class="form-control" id="exampleFormControlTextarea1" required rows="3"
                                    placeholder="{{ __('translate.Transaction information') }}" name="tnx_info"></textarea>
                            </div>
                        </div>
                        <div class="bank-payment-form-item">
                            <div class="bank-payment-form-inner pt-3">
                                <a class="main-btn btn btn-primary" href="javascript:;" id="bank_payment_btn">
                                    {{ __('translate.Payment Now') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </main>
@endsection
@push('js_section')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script>
        "use strict";
        $(function() {

            $("#stripe_form_btn").on("click", function() {
                $("#payment-form").submit();
            })

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

            $("#paypal_btn").on("click", function() {
                window.location.href = "{{ route('user.pay-via-paypal') }}";
            })

            $("#mollie_payment_btn").on("click", function() {
                window.location.href = "{{ route('ecommerce.pay-via-mollie') }}";
            })

            $("#instamojoPayment").on("click", function() {
                window.location.href = "{{ route('ecommerce.pay-via-instamojo') }}";
            })

            $("#bank_payment_btn").on("click", function() {
                $("#bank_payment_form").submit();
            })


        });
    </script>

    {{-- start paystack payment --}}

    @if ($payment_setting->paystack_status == 1)
        <script src="https://js.paystack.co/v1/inline.js"></script>

        @php
            $public_key = $payment_setting->paystack_public_key;
            $currency = $paystack_currency->currency_code;
            $currency = strtoupper($currency);

            $ngn_amount = $payable_amount * $paystack_currency->currency_rate;
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
                        email: '{{ $user->email }}',
                        amount: '{{ $ngn_amount }}',
                        currency: "{{ $currency }}",
                        callback: function(response) {
                            let reference = response.reference;
                            let tnx_id = response.transaction;
                            let _token = "{{ csrf_token() }}";
                            $.ajax({
                                type: "get",
                                data: {
                                    reference,
                                    tnx_id,
                                    _token
                                },
                                url: "{{ route('ecommerce.pay-via-paystack') }}",
                                success: function(response) {
                                    if (response.status == 'success') {
                                        toastr.success(response.message);
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

    {{-- start flutterwave payment --}}
    @if ($payment_setting->flutterwave_status == 1)
        <script src="https://checkout.flutterwave.com/v3.js"></script>

        @php

            $payable_amount = $payable_amount * $flutterwave_currency->currency_rate;
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
                                url: "{{ route('ecommerce.pay-via-flutterwave') }}",
                                success: function(response) {

                                    if (response.status == 'success') {
                                        toastr.success(response.message);
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

    {{-- end flutterwave payment --}}



@endpush
