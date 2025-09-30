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
                                $req = request();

                                // Service & availability (dacă nu sunt deja injectate)
                                $serviceId      = (int)($req->input('service_id'));
                                $service        = $service ?? (\Modules\TourBooking\Entities\Service::find($serviceId));
                                $availabilityId = $req->input('availability_id');
                                $availability   = (isset($availability) && $availability) ? $availability : null;

                                if (!$availability && $service && $availabilityId) {
                                    $availability = optional($service->availabilities)->firstWhere('id', (int)$availabilityId);
                                }

                                // Age categories din service (doar cele enabled)
                                $ageCatsRaw = $service
                                    ? (is_array($service->age_categories) ? $service->age_categories : (json_decode($service->age_categories ?? '[]', true) ?: []))
                                    : [];
                                $enabledAgeCats = collect($ageCatsRaw)->filter(fn($c) => !empty($c['enabled']));

                                // Labels & intervale
                                $labelsByAge = $enabledAgeCats->mapWithKeys(function ($cfg, $key) {
                                    return [$key => ($cfg['label'] ?? ucfirst($key))];
                                })->toArray();
                                $rangeByAge = $enabledAgeCats->mapWithKeys(function ($cfg, $key) {
                                    $min = $cfg['min_age'] ?? null;
                                    $max = $cfg['max_age'] ?? null;
                                    if ($min !== null && $max !== null) {
                                        return [$key => ((int)$max >= 120 ? "($min+ years)" : "($min-$max years)")];
                                    }
                                    if ($min !== null && ($max === null || (int)$max === 0)) return [$key => "($min+ years)"];
                                    if ($max !== null) return [$key => "(0-$max years)"];
                                    return [$key => ""];
                                })->toArray();

                                // Prețuri de bază din service
                                $pricesByAge = $enabledAgeCats->mapWithKeys(function ($cfg, $key) {
                                    return [$key => (float)($cfg['price'] ?? 0)];
                                })->toArray();

                                // Override din availability: json age_categories sau special/children legacy
                                if ($availability) {
                                    $avAgeJson = is_array($availability->age_categories)
                                        ? $availability->age_categories
                                        : (json_decode($availability->age_categories ?? '[]', true) ?: []);

                                    if (!empty($avAgeJson)) {
                                        foreach ($avAgeJson as $k => $v) {
                                            if (array_key_exists($k, $pricesByAge)) {
                                                $pricesByAge[$k] = (float)($v ?? 0);
                                            }
                                        }
                                    } else {
                                        if (isset($availability->special_price) && array_key_exists('adult', $pricesByAge)) {
                                            $pricesByAge['adult'] = (float)$availability->special_price;
                                        }
                                        if (isset($availability->per_children_price) && array_key_exists('child', $pricesByAge)) {
                                            $pricesByAge['child'] = (float)$availability->per_children_price;
                                        }
                                    }
                                }

                                // Cantități din noul flux (age_quantities[...])
                                $ageQuantities = $req->input('age_quantities', []);
                                $hasAgeQuantities = is_array($ageQuantities) && count(array_filter($ageQuantities, fn($q)=> (int)$q > 0)) > 0;

                                // Fallback vechi
                                $personQty   = $data['personCount'] ?? (int)$req->input('person', 0);
                                $childrenQty = $data['childCount']  ?? (int)$req->input('children', 0);
                                $personUnit  = $availability->special_price      ?? ($service->price_per_person ?? 0);
                                $childUnit   = $availability->per_children_price ?? ($service->child_price      ?? 0);

                                // Extras
                                if (isset($data['extras'])) {
                                    $extras = collect($data['extras']);
                                } else {
                                    $extrasIds = $req->input('extras', []);
                                    $extras = $service ? $service->extraCharges()->whereIn('id', (array)$extrasIds)->get() : collect();
                                }

                                // Construim liniile & totalurile
                                $ticketLines    = [];
                                $ticketSubtotal = 0.0;

                                if ($hasAgeQuantities) {
                                    foreach ($ageQuantities as $k => $qty) {
                                        $qty = (int)$qty;
                                        if ($qty <= 0) continue;

                                        $unit = (float)($pricesByAge[$k] ?? 0);
                                        $lineTotal = $qty * $unit;

                                        $ticketLines[] = [
                                            'label' => $labelsByAge[$k] ?? ucfirst($k),
                                            'range' => $rangeByAge[$k]  ?? '',
                                            'qty'   => $qty,
                                            'unit'  => $unit,
                                            'total' => $lineTotal,
                                        ];
                                        $ticketSubtotal += $lineTotal;
                                    }
                                } else {
                                    if ($personQty > 0) {
                                        $lineTotal = $personQty * (float)$personUnit;
                                        $ticketLines[] = [
                                            'label' => 'Person',
                                            'range' => '(18+ years)',
                                            'qty'   => $personQty,
                                            'unit'  => (float)$personUnit,
                                            'total' => $lineTotal,
                                        ];
                                        $ticketSubtotal += $lineTotal;
                                    }
                                    if ($childrenQty > 0) {
                                        $lineTotal = $childrenQty * (float)$childUnit;
                                        $ticketLines[] = [
                                            'label' => 'Children',
                                            'range' => '(13-17 years)',
                                            'qty'   => $childrenQty,
                                            'unit'  => (float)$childUnit,
                                            'total' => $lineTotal,
                                        ];
                                        $ticketSubtotal += $lineTotal;
                                    }
                                }

                                $extrasTotal = (float)$extras->sum('price');
                                $grandTotal  = $ticketSubtotal + $extrasTotal;
                            @endphp

                            <div>
                                <div class="tg-tour-about-border-doted mb-15"></div>

                                <div class="tg-tour-about-tickets-wrap mb-15">
                                    <span class="tg-tour-about-sidebar-title">Tickets:</span>

                                    @forelse($ticketLines as $line)
                                        <div class="tg-tour-about-tickets mb-10">
                                            <div class="tg-tour-about-tickets-adult">
                                                <span>{{ $line['label'] }}</span>
                                                @if($line['range'])
                                                    <p class="mb-0">{{ $line['range'] }}</p>
                                                @endif
                                            </div>
                                            <div class="tg-tour-about-tickets-quantity">
                                                {{ $line['qty'] }}
                                                x {{ currency_price($line['unit']) }}
                                                = {{ currency($line['total'], 2) }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted">No tickets selected.</div>
                                    @endforelse
                                </div>

                                @if($extras->count() > 0)
                                    <div class="tg-tour-about-extra mb-10">
                                        <span class="tg-tour-about-sidebar-title mb-10 d-inline-block">Add Extra:</span>
                                        <div class="tg-filter-list">
                                            <ul>
                                                @foreach ($extras as $extra)
                                                    <li>
                                                        <div class="checkbox d-flex">
                                                            <label class="tg-label">{{ $extra->name }}</label>
                                                        </div>
                                                        <span class="quantity">{{ currency($extra->price) }}</span>
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
