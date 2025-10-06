@extends('layout_inner_page')

@section('title')
    <title>{{ __('translate.Booking Checkout') }}</title>
@endsection

@section('front-content')
    @include('breadcrumb', ['breadcrumb_title' => __('translate.Booking Checkout')])

    <!-- checkout area -->
    <section class="checkout-area pb-100 pt-125">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="tg-checkout-form-wrapper mr-50">
                        <h2 class="tg-checkout-form-title mb-30">{{ __('translate.Billing Details') }}</h2>
                        <div class="row gx-24">
                            <div class="tg-checkout-form-input mb-25">
                                <label>{{ __('translate.Customer name') }}</label>
                                <input id="customer_name" class="input" type="text"
                                    value="{{ auth()->user()->name ?? '' }}" name="customer_name"
                                    placeholder="Customer name">
                            </div>

                            <div class="tg-checkout-form-input mb-25">
                                <label>{{ __('translate.Customer email') }}</label>
                                <input id="customer_email" class="input" type="email"
                                    value="{{ auth()->user()->email ?? '' }}" name="customer_email"
                                    placeholder="Customer email">
                            </div>

                            <div class="tg-checkout-form-input mb-25">
                                <label>{{ __('translate.Customer phone') }}</label>
                                <input id="customer_phone" class="input" type="text"
                                    value="{{ auth()->user()->phone ?? '' }}" name="customer_phone"
                                    placeholder="Customer phone">
                            </div>
                            <div class="tg-checkout-form-input mb-25">
                                <label>{{ __('translate.Customer address') }}</label>
                                <input id="customer_address" class="input" value="{{ auth()->user()->address ?? '' }}"
                                    class="house-number" name="customer_address" type="text"
                                    placeholder="{{ __('translate.House number and Street name') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================= ORDER SUMMARY (age-based + fallback) ========================= --}}
                <div class="col-lg-4">
                    <div class="tg-blog-sidebar top-sticky mb-30">
                        <div class="tg-blog-sidebar-box mb-30">
                            <h2 class="tg-checkout-form-title tg-checkout-form-title-3 mb-15">Your Order</h2>

                            @php
                                // Use the properly computed data from the controller instead of recalculating
                                $ticketLines = $data['lines'] ?? [];
                                $grandTotal = $data['total'] ?? 0;
                                $extras = $data['extras'] ?? collect();
                                $extrasTotal = (float)$extras->sum('price');
                            @endphp

                            <div>
                                <div class="tg-tour-about-border-doted mb-15"></div>

                                <div class="tg-tour-about-tickets-wrap mb-15">
                                    <span class="tg-tour-about-sidebar-title">Tickets:</span>

                                    @forelse($ticketLines as $line)
                                        @if(!($line['is_extra'] ?? false))
                                        <div class="tg-tour-about-tickets mb-10">
                                            <div class="tg-tour-about-tickets-adult">
                                                <span>{{ $line['label'] }}</span>
                                            </div>
                                            <div class="tg-tour-about-tickets-quantity">
                                                {{ $line['qty'] }}
                                                    x {{ currency($line['unit']) }}
                                                    = {{ currency($line['subtotal']) }}
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="text-muted">No tickets selected.</div>
                                    @endforelse
                                </div>

                                @php
                                    $extraLines = collect($ticketLines)->filter(fn($line) => $line['is_extra'] ?? false);
                                    dump($extraLines);
                                @endphp
                                @if($extraLines->count() > 0)
                                    <div class="tg-tour-about-extra mb-10">
                                        <span class="tg-tour-about-sidebar-title mb-10 d-inline-block">Add Extra:</span>
                                        <div class="tg-filter-list">
                                            <ul>
                                                @foreach ($extraLines as $line)
                                                    <li>
                                                        <div class="checkbox d-flex">
                                                            <label class="tg-label">{{ $line['label'] }}</label>
                                                        </div>
                                                        <span class="quantity">{{ currency($line['subtotal']) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                <div class="tg-tour-about-border-doted mb-15"></div>
                                <div class="tg-tour-about-coast d-flex align-items-center flex-wrap justify-content-between">
                                    <span class="tg-tour-about-sidebar-title d-inline-block">Total Cost:</span>
                                    <h5 class="total-price">{{ currency($grandTotal, 2) }}</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Metode de plată --}}
                        @include('tourbooking::front.bookings.payment')
                    </div>
                </div>
                {{-- ======================= /ORDER SUMMARY ======================= --}}
            </div>
        </div>
        </div>
    </section>
    <!-- checkout area end-->
@endsection

@push('js_section')
    <script>
        $(document).ready(function() {
            // helperi pentru diverse layout-uri care folosesc subtotal/shipping/total
            function parseCurrency(currencyStr) {
                return parseFloat((currencyStr || '').replace(/[^0-9.-]+/g, '')) || 0;
            }
            function formatCurrency(amount) {
                return '$' + Number(amount || 0).toFixed(2);
            }
            function updatePrices() {
                const subTotal = parseCurrency($('.sub_total span').text());
                const shippingCost = parseCurrency($('.shipping_cost span').text().replace('(+)', '').trim());
                const total = subTotal + shippingCost;
                $('.total span').text(formatCurrency(total));
                $('.stripe_price_here').text(formatCurrency(total));
                $('input[name="subtotal"]').val(subTotal);
                $('input[name="shipping_charge"]').val(shippingCost);
                $('input[name="total"]').val(total);
            }
            $('select[name="shipping_method_id"]').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const priceText = (selectedOption.text().split('-')[1] || '').trim();
                const shippingCost = parseCurrency(priceText);
                $('.shipping_cost span').text('(+)' + formatCurrency(shippingCost));
                updatePrices();
            });
            updatePrices();

            // propagă detaliile de facturare către inputurile ascunse din formularul de plată (dacă există)
            $('#customer_name').on('keyup', function() {
                $('.form_customer_name').val($(this).val());
            });
            $('#customer_email').on('change', function() {
                $('.form_customer_email').val($(this).val());
            });
            $('#customer_phone').on('change', function() {
                $('.form_customer_phone').val($(this).val());
            });
            $('#customer_address').on('change', function() {
                $('.form_customer_address').val($(this).val());
            });
        });
    </script>
@endpush
