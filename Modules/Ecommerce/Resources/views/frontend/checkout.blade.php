@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- checkout area -->
    <section class="checkout-area pb-100 pt-125">
        <div class="container">
            <form action="{{ route('checkout.process-to-payment') }}" method="GET">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="tg-checkout-form-wrapper mr-50">
                            <h2 class="tg-checkout-form-title mb-30">{{ __('translate.Billing Details') }}</h2>
                            <div class="row gx-24">
                                <div class="tg-checkout-form-input mb-25">
                                    <label>{{ __('translate.Full Name') }}</label>
                                    <input class="input" type="text" value="{{ auth()->user()->name ?? '' }}"
                                        name="name" placeholder="Full Name">
                                </div>

                                <div class="tg-checkout-form-input mb-25">
                                    <label>{{ __('translate.Email') }}</label>
                                    <input class="input" type="email" value="{{ auth()->user()->email ?? '' }}"
                                        name="email" placeholder="Email">
                                </div>

                                <div class="tg-checkout-form-input mb-25">
                                    <label>{{ __('translate.WhatsApp Number') }}</label>
                                    <input class="input" type="text" value="{{ auth()->user()->phone ?? '' }}"
                                        name="phone" placeholder="WhatsApp Phone">
                                </div>

                                <div class="tg-checkout-form-input mb-25 dropdown">
                                    <label>{{ __('translate.Shipping Method') }}</label>
                                    <select class="input" name="shipping_method_id" class="form-select">
                                        <option value="" selected disabled>{{ __('translate.Select One') }}</option>
                                        @foreach ($methods as $method)
                                            <option value="{{ $method->id }}">{{ $method->name }} -
                                                {{ currency($method->price) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tg-checkout-form-input mb-25">
                                    <label>{{ __('translate.Full Address') }}</label>
                                    <input class="input" class="house-number" name="address" type="text"
                                        placeholder="{{ __('translate.House number and Street name') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="tg-blog-sidebar top-sticky mb-30">
                            <div class="tg-blog-sidebar-box mb-30">
                                <h2 class="tg-checkout-form-title tg-checkout-form-title-3 mb-15">Your Order</h2>
                                <div class="tg-checkout-order-table table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="product-name">Product</th>
                                                <th class="product-total text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($carts as $cart)
                                                <tr class="cart_item first">
                                                    <td class="product-name">
                                                        {{ Str::limit($cart->product->translate?->name, 35) }}
                                                        <b> X </b>
                                                        {{ $cart->quantity }}
                                                    </td>
                                                    <td class="product-total">
                                                        <span
                                                            class="amount">{{ currency($cart->product->finalPrice * $cart->quantity) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>

                                            <tr class="cart-subtotal">
                                                <th>{{ __('translate.Subtotal') }}</th>
                                                <td class="sub_total">
                                                    <span class="amount sub_total">{{ currency($sub_total) }}</span>
                                                    <input type="hidden" name="subtotal" value="{{ $sub_total }}">
                                                </td>
                                            </tr>
                                            <tr class="cart-subtotal">
                                                <th>{{ __('translate.Shipping') }}</th>
                                                <td class="shipping_cost">
                                                    <span class="amount shipping_charge">(+){{ currency(0) }}</span>
                                                    <input type="hidden" name="shipping_charge" value="">
                                                </td>
                                            </tr>
                                            <tr class="cart-subtotal">
                                                <th>{{ __('translate.Total') }}</th>
                                                <td class="total">
                                                    <span class="amount total-amount">{{ currency($sub_total) }}</span>
                                                    <input type="hidden" name="total" value="">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="tg-checkout-form-btn">
                                <button type="submit" class="tg-btn w-100">{{ __('translate.Place Your Order') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- checkout area end-->
@endsection

@push('js_section')
    <script>
        $(document).ready(function() {
            // Function to parse currency string to number
            function parseCurrency(currencyStr) {
                return parseFloat(currencyStr.replace(/[^0-9.-]+/g, '')); // Removing non-numeric characters
            }

            // Function to format number into currency format (e.g., $10.00)
            function formatCurrency(amount) {
                return '$' + amount.toFixed(2);
            }

            // Function to update prices and hidden form fields
            function updatePrices() {
                // Get the subtotal value
                const subTotal = parseCurrency($('.sub_total span').text());

                // Get the shipping cost from the displayed value
                const shippingCost = parseCurrency($('.shipping_cost span').text().replace('(+)', '').trim());

                // Calculate the total price
                const total = subTotal + shippingCost;

                // Update the total span with the formatted total price
                $('.total span').text(formatCurrency(total));

                // If you are showing this price for Stripe payment, update it
                $('.stripe_price_here').text(formatCurrency(total));

                // Update the hidden form inputs for subtotal, shipping cost, and total
                $('input[name="subtotal"]').val(subTotal);
                $('input[name="shipping_charge"]').val(shippingCost);
                $('input[name="total"]').val(total);
            }

            // Event listener for when the shipping method is changed
            $('select[name="shipping_method_id"]').on('change', function() {
                // Get the selected option's shipping cost (splitting the price part)
                const selectedOption = $(this).find('option:selected');
                const priceText = selectedOption.text().split('-')[1].trim();
                const shippingCost = parseCurrency(priceText);

                // Update the shipping cost display and the input field
                $('.shipping_cost span').text('(+)' + formatCurrency(shippingCost));

                // Recalculate and update all prices
                updatePrices();
            });

            // Optional: If you want to initially set the values correctly when the page loads, you can call updatePrices()
            updatePrices();
        });
    </script>
@endpush
