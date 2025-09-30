@extends('layout_inner_page')

@section('title')
    <title>{{ $seo_setting->seo_title }}</title>
    <meta name="title" content="{{ $seo_setting->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($seo_setting->seo_description)) !!}">
@endsection

@section('front-content')
    @include('breadcrumb')

    <!-- cart area -->
    <div class="cart-area pb-100 pt-105">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tg-cart-table-content table-responsive mb-30">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('translate.Product') }}</th>
                                    <th class="price">{{ __('translate.Price') }}</th>
                                    <th>{{ __('translate.Quantity') }}</th>
                                    <th class="subtotal">{{ __('translate.Subtotal') }}</th>
                                    <th>{{ __('translate.Remove') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $subtotal = 0;
                                @endphp
                                @foreach ($carts as $cart)
                                    @php
                                        $itemTotal = $cart->product ? $cart->product->finalPrice * $cart->quantity : 0;
                                        $subtotal += $itemTotal;
                                    @endphp
                                    <tr>
                                        <td class="product-thumbnail">
                                            <a class="thumb" href="{{ route('product.view', $cart->product->slug) }}">
                                                <img src="{{ getImageOrPlaceholder($cart->product->thumbnail_image, '125x135') }}"
                                                    alt="product">
                                            </a>
                                            <a class="texts" href="{{ route('product.view', $cart->product->slug) }}">
                                                {{ __(Str::limit($cart->product->translate?->name, 35)) }}
                                            </a>
                                        </td>
                                        <td class="product-price2">
                                            <span class="amount price_display">
                                                {!! $cart->product->price_display !!}
                                            </span>
                                        </td>
                                        <td class="product-quantity">
                                            <div class="tg-product-details-quantity">
                                                <div class="tg-booking-quantity-item">
                                                    <span class="optech-product-minus decrement"
                                                        data-id="{{ $cart->id }}">
                                                        <svg width="14" height="2" viewBox="0 0 14 2" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1 1H13" stroke="currentColor" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                    <input class="tg-quantity-input number" type="text"
                                                        id="quantity-{{ $cart->id }}" value="{{ $cart->quantity }}">
                                                    <span class="optech-product-plus" data-id="{{ $cart->id }}">
                                                        <svg width="15" height="14" viewBox="0 0 15 14"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.21924 7H13.3836" stroke="currentColor"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path d="M7.30176 13V1" stroke="currentColor" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="product-subtotal price total-price" id="price-{{ $cart->id }}"
                                            data-unit-price="{{ $cart->product ? $cart->product->finalPrice : 0 }}">
                                            {{ currency($itemTotal) }}
                                        </td>
                                        <td class="product-remove">
                                            <i class="fa fa-times delete-cart-item cp" data-id="{{ $cart->id }}"
                                                data-url="{{ route('cart.delete', $cart->id) }}"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-xl-9 col-lg-8 col-md-7">

                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-5">
                            <div class="tg-cart-page-total mb-20">
                                <ul class="mb-20">
                                    <li> {{ __('translate.Subtotal') }} <span
                                            class="sub_total">{{ currency($subtotal) }}</span></li>
                                    <li> {{ __('translate.Total') }} <span
                                            class="total_sale">{{ currency($subtotal) }}</span></li>
                                </ul>
                                <div class="d-flex justify-content-between">
                                    @if ($carts->isNotEmpty())
                                        <a class="tg-btn mb-10 rt-mt-40" data-aos="fade-up"
                                            href="{{ route('checkout.index') }}"
                                            data-text="{{ __('translate.Proceed to Checkout') }}"><span class="btn-wraper">
                                                {{ __('translate.Proceed to Checkout') }}
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- cart area end-->
@endsection

@push('js_section')
    <script src="{{ asset('global/sweetalert/sweetalert2@11.js') }}"></script>
    <script>
        "use strict";

        function updateSubtotal() {
            let subtotal = 0;
            document.querySelectorAll('.total-price').forEach(priceElement => {
                const price = parseFloat(priceElement.innerText.replace(/[^0-9.-]+/g, ''));
                if (!isNaN(price)) {
                    subtotal += price;
                }
            });
            document.querySelector('.sub_total').innerHTML = currency(subtotal);
            document.querySelector('.total_sale').innerHTML = currency(subtotal);
        }

        // Cart Item Button
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".optech-product-minus, .optech-product-plus").forEach(span => {
                span.addEventListener("click", function() {
                    const itemId = this.getAttribute("data-id");
                    const quantityElement = document.getElementById(`quantity-${itemId}`);
                    const priceElement = document.getElementById(`price-${itemId}`);
                    const unitPrice = parseFloat(priceElement.getAttribute('data-unit-price'));
                    let currentQuantity = parseInt(quantityElement.value);

                    if (this.classList.contains("optech-product-minus")) {
                        if (currentQuantity > 1) {
                            currentQuantity--;
                        } else {
                            toastr.error('Quantity must be at least 1');
                            return;
                        }
                    } else if (this.classList.contains("optech-product-plus")) {
                        currentQuantity++;
                    }

                    quantityElement.value = currentQuantity;

                    fetch(@json(route('cart.update')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                id: itemId,
                                quantity: currentQuantity
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const newTotal = (unitPrice * currentQuantity).toFixed(2);
                                priceElement.innerHTML = currency(newTotal);
                                updateSubtotal();
                                toastr.success(data.notification.message);
                            } else {
                                if (data.notification) {
                                    toastr.error(data.notification.message);
                                }
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                        });
                });
            });
        });

        function currency(amount) {
            return new Intl.NumberFormat('{{ app()->getLocale() }}', {
                style: 'currency',
                currency: '{{ config('settings.currency_code', 'USD') }}'
            }).format(amount);
        }

        // Add click event listener to delete buttons
        document.querySelectorAll('.delete-cart-item').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const cartId = this.dataset.id;
                deleteCartItem(cartId);
            });
        });

        function deleteCartItem(id) {
            Swal.fire({
                title: "{{ __('translate.Are you really want to delete this item?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('translate.Yes, Delete It') }}",
                cancelButtonText: "{{ __('translate.Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "{{ __('translate.Deleting..') }}",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ url('cart/cart') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(json => Promise.reject(json));
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Show the notification
                            Swal.close();

                            // Show success notification using toastr
                            if (data.success) {
                                toastr.success(data.notification.messege);
                                // Reload the page after successful deletion
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                toastr.error(data.notification.messege);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Close loading dialog
                            Swal.close();

                            // Show error notification using toastr
                            toastr.error(error.notification ? error.notification.messege :
                                "{{ __('translate.An error occurred while removing the item') }}");
                        });
                }
            });
        }
    </script>
@endpush
