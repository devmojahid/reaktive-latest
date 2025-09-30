@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Order Details') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Order Details') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Orders') }} >> {{ __('translate.All Orders') }}</p>
@endsection

@section('body-content')
    <!-- crancy Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <!-- Dashboard Inner -->
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <div class="ed-invoice-page-wrapper">
                                        <div class="ed-invoice-main-wrapper">
                                            <div class="ed-invoice-page">
                                                <div class="ed-inv-logo-area">
                                                    <div class="ed-main-logo">
                                                        <img src="{{ asset($general_setting->logo) }}" alt="logo"
                                                            class="ed-logo">
                                                    </div>
                                                    <div>
                                                    </div>
                                                </div>
                                                <div class="ed-inv-billing-info">
                                                    <div class="ed-inv-info">
                                                        <p class="ed-inv-info-title">{{ __('translate.Billed To') }}</p>
                                                        <table>
                                                            <tr>
                                                                <td>{{ __('translate.Name') }}:</td>
                                                                <td> {{ $order->address['name'] }} </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Phone') }}:</td>
                                                                <td>{{ $order->address['phone'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Email') }}:</td>
                                                                <td>{{ $order->address['email'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Address') }}:</td>
                                                                <td>{{ $order->address['address'] }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="ed-inv-more-info">
                                                        <table>


                                                            <tr>
                                                                <td>{{ __('translate.Payment Status') }}:</td>
                                                                <td>
                                                                    <div class="d-flex justify-content-start">
                                                                        @if ($order->payment_status == \App\Constants\Status::APPROVED)
                                                                            <div class="ed-inv-paid-status ">
                                                                                <span>{{ __('translate.Success') }}</span>
                                                                            </div>
                                                                        @elseif ($order->payment_status == \App\Constants\Status::REJECTED)
                                                                            <div class="ed-inv-paid-status rejected">
                                                                                <span>{{ __('translate.Rejected') }}</span>
                                                                            </div>
                                                                        @else
                                                                            <div class="ed-inv-paid-status pending ">
                                                                                <span>{{ __('translate.Pending') }}</span>
                                                                            </div>
                                                                        @endif

                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Invoice No') }}:</td>
                                                                <td>#{{ $order->order_id }}</td>
                                                            </tr>

                                                            <tr>
                                                                <td>{{ __('translate.Created at') }}:</td>
                                                                <td>{{ $order->created_at->format('d M, Y') }}</td>
                                                            </tr>

                                                            <tr>
                                                                <td>{{ __('translate.Gateway') }}:</td>
                                                                <td>{{ html_decode($order->payment_method) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Transaction') }}:</td>
                                                                <td>{!! clean($order->transaction_id) !!}</td>
                                                            </tr>



                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="ed-inv-table-content">

                                                    <p class="ed-inv-table-headline">{{ __('translate.Product List') }}
                                                    </p>
                                                    <div class="ed-inv-invoice-table-main-wrapper">
                                                        <div class="ed-inv-invoice-table-wrapper">
                                                            <table class="ed-inv-invoice-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>
                                                                            {{ __('translate.SN') }}
                                                                        </th>

                                                                        <th>
                                                                            {{ __('translate.Product Name') }}
                                                                        </th>

                                                                        <th>
                                                                            {{ __('translate.Quantity') }}
                                                                        </th>

                                                                        <th>
                                                                            {{ __('translate.Price') }}
                                                                        </th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($order->order_detail as $detail)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ __($detail->singleProduct->translate->name) }}
                                                                            </td>
                                                                            <td>{{ $detail->quantity }}</td>
                                                                            <td>{{ __(currency($detail->price)) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="ed-inv-billing-summary d-flex justify-content-md-between align-items-center  ">

                                                    <div>
                                                        <form action="{{ route('admin.order.updateStatus', $order->id) }}"
                                                            method="POST" class="mt-2">
                                                            @csrf
                                                            @method('PATCH')

                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                <label for="paymentMethod"
                                                                    class="form-label">{{ __('translate.Payment Status') }}</label>
                                                                <select class="form-select crancy__item-input"
                                                                    name="payment_status">
                                                                    <option value="0"
                                                                        {{ $order->payment_status == \App\Constants\Status::PENDING ? 'selected' : '' }}>
                                                                        {{ __('translate.Pending') }}
                                                                    </option>
                                                                    <option value="1"
                                                                        {{ $order->payment_status == \App\Constants\Status::APPROVED ? 'selected' : '' }}>
                                                                        {{ __('translate.Approved') }}
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ $order->payment_status == \App\Constants\Status::REJECTED ? 'selected' : '' }}>
                                                                        {{ __('translate.Rejected') }}
                                                                    </option>


                                                                </select>
                                                            </div>


                                                            <div class="crancy__item-form--group">
                                                                <label for="paymentMethod"
                                                                    class="form-label">{{ __('translate.Order Status') }}</label>
                                                                <select class="form-select crancy__item-input"
                                                                    name="order_status">
                                                                    <option value="0"
                                                                        {{ $order->order_status == \App\Constants\Status::PENDING ? 'selected' : '' }}>
                                                                        {{ __('translate.Pending') }}
                                                                    </option>
                                                                    <option value="3"
                                                                        {{ $order->order_status == \App\Constants\Status::PROCESSING ? 'selected' : '' }}>
                                                                        {{ __('translate.Processing') }}
                                                                    </option>
                                                                    <option value="4"
                                                                        {{ $order->order_status == \App\Constants\Status::SHIPPED ? 'selected' : '' }}>
                                                                        {{ __('translate.Shipped') }}
                                                                    </option>
                                                                    <option value="5"
                                                                        {{ $order->order_status == \App\Constants\Status::APPROVED ? 'selected' : '' }}>
                                                                        {{ __('translate.Completed') }}
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ $order->order_status == \App\Constants\Status::REJECTED ? 'selected' : '' }}>
                                                                        {{ __('translate.Rejected') }}
                                                                    </option>


                                                                </select>
                                                            </div>


                                                            <button class="crancy-btn mg-top-25"
                                                                type="submit">{{ __('translate.Order Update') }}</button>


                                                            <button class="crancy-btn mg-top-25 delete_danger_btn"
                                                                type="button"
                                                                onclick="itemDeleteConfrimation({{ $order->id }})"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal">{{ __('translate.Order Delete') }}</button>



                                                        </form>
                                                    </div>


                                                    <div class="ed-inv-summary-wrapper">
                                                        <table>

                                                            <tr>
                                                                <td>{{ __('translate.Subtotal') }}:</td>
                                                                <td> <span>{{ currency($order->subtotal) }}</td>
                                                            </tr>

                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="ed-summry-total-sparetor"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Shipping Charge') }}:</td>
                                                                <td> <span>{{ currency($order->shipping_charge) }}</span>
                                                                </td>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="ed-summry-total-sparetor"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Total') }}:</td>
                                                                <td><span>{{ currency($order->total) }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
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


    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('translate.Delete Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('translate.Are you realy want to delete this item?') }}</p>
                </div>
                <div class="modal-footer">
                    <form action="" id="item_delect_confirmation" class="delet_modal_form" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('translate.Yes, Delete') }}</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js_section')
    <script>
        "use strict"

        function itemDeleteConfrimation(id) {
            $("#item_delect_confirmation").attr("action", '{{ url('admin/ecommerce/order/order-delete/') }}' + "/" + id)
        }
    </script>
@endpush
