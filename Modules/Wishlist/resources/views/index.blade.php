@extends('user.master_layout')

@section('title')
    <title>{{ __('translate.Product Wishlist') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Product Wishlist') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Dashboard') }} >> {{ __('translate.Product Wishlist') }}</p>
@endsection

@section('body-content')
    <!-- crancy Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12 mg-top-30">
                    <div class="crancy-body">
                        <!-- Dashboard Inner -->
                        <div class="crancy-dsinner">
                            <div class="ed-watch-page-wrapper">
                                <div class="ed-watch-main-wrapper">
                                    <div class="ed-watch-content-wrapper">
                                        <div class="ed-watch-content-main-wrapper">
                                            <div class="row">
                                                @forelse ($products as $product)
                                                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                                                        <div class="mb-4">
                                                            @include('ecommerce::frontend.partials.product_item')
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12">
                                                        Data Not found.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Dashboard Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End crancy Dashboard -->
@endsection


@push('js_section')
    <script src="{{ asset('global/sweetalert/sweetalert2@11.js') }}"></script>


    <script>
        "use strict";
        $(function() {

            $(".add_to_cart").on("click", function(e) {

                let course_id = $(this).data('course_id');

                $.ajax({
                    type: 'GET',
                    url: "{{ url('add-to-card') }}" + "/" + course_id,
                    success: function(response) {
                        toastr.success(response.message);

                        let total_cart = $('#total_cart').html();
                        total_cart = parseInt(total_cart) + parseInt(1);
                        $('#total_cart').html(total_cart);

                    },
                    error: function(err) {

                        if (err.status == 403) {
                            toastr.error(err.responseJSON.message)
                        } else {
                            toastr.error(`{{ __('translate.Server error occured') }}`)
                        }

                    }
                });

            })


        });

        function removeWishlist(id) {
            Swal.fire({
                title: "{{ __('translate.Are you realy want to delete this item ?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('translate.Yes, Delete It') }}",
                cancelButtonText: "{{ __('translate.Cancel') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#remove_listing_" + id).submit();
                }

            })
        }
    </script>
@endpush
