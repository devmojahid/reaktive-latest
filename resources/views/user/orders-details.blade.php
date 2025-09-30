@extends('user.master_layout')
@section('title')
    <title>{{ __('translate.Order Details') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Order Details') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Dashboard') }} >> {{ __('translate.Order Details') }}</p>
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
                                                                <td>{{ __($order?->address['name']) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Phone') }}:</td>
                                                                <td>{{ __($order?->address['phone']) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Email') }}:</td>
                                                                <td>{{ __($order?->address['email']) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Address') }}: </td>
                                                                <td>{{ __($order?->address['address']) }}</td>
                                                            </tr>
                                                        </table>

                                                    </div>
                                                    <div class="ed-inv-more-info">
                                                        <table>

                                                            <tr>
                                                                <td>{{ __('translate.Payment Status') }}:</td>
                                                                <td>
                                                                    <div class="d-flex justify-content-start">
                                                                        @if ($order->payment_status == 'success')
                                                                            <div class="ed-inv-paid-status ">
                                                                                <span>{{ __('translate.Success') }}</span>
                                                                            </div>
                                                                        @elseif ($order->payment_status == 'rejected')
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
                                                                <td>{{ __('translate.Order Status') }}:</td>
                                                                <td>
                                                                    @if ($order->order_status == 0)
                                                                        <span class="paid un_paid">
                                                                            {{ __('translate.Pending') }}
                                                                        </span>
                                                                    @elseif($order->order_status == 1)
                                                                        <span class="paid">
                                                                            {{ __('translate.Completed') }}
                                                                        </span>
                                                                    @elseif($order->order_status == 2)
                                                                        <span class="paid un_paid">
                                                                            {{ __('translate.Rejected') }}
                                                                        </span>
                                                                    @elseif($order->order_status == 3)
                                                                        <span class="paid">
                                                                            {{ __('translate.Processing') }}
                                                                        </span>
                                                                    @elseif($order->order_status == 4)
                                                                        <span class="paid ">
                                                                            {{ __('translate.Shipped') }}
                                                                        </span>
                                                                    @else
                                                                        <span class="paid ">
                                                                            {{ __('translate.Completed') }}
                                                                        </span>
                                                                    @endif
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
                                                    <p class="ed-inv-table-headline">{{ __('translate.Product List') }} </p>
                                                    <div class="ed-inv-invoice-table-main-wrapper">
                                                        <div class="ed-inv-invoice-table-wrapper">
                                                            <table class="ed-inv-invoice-table">
                                                                <thead>
                                                                    <th>{{ __('translate.Product Name') }}</th>
                                                                    <th>{{ __('translate.Price') }}</th>
                                                                    <th>{{ __('translate.Quantity') }}</th>
                                                                    <th>{{ __('translate.Amount') }}</th>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($order->order_detail as $detail)
                                                                        <!-- single item -->
                                                                        <tr>
                                                                            <td>{{ __($detail->singleProduct->translate->name) }}
                                                                            </td>
                                                                            <td>{{ __(currency($detail->singleProduct->finalPrice)) }}
                                                                            </td>
                                                                            <td>{{ $detail->quantity }}</td>
                                                                            <td>{{ __(currency($detail->price)) }}</td>
                                                                        </tr>
                                                                        <!-- single item -->
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ed-inv-billing-summary d-flex justify-content-md-end  ">
                                                    <div class="ed-inv-summary-wrapper">
                                                        <table>
                                                            <tr>
                                                                <td>{{ __('translate.Subtotal') }}:</td>
                                                                <td>{{ currency($order->subtotal) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Shipping Cost') }}(-):</td>
                                                                <td>{{ currency($order->shipping_charge) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="ed-summry-total-sparetor"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Total') }}:</td>
                                                                <td>{{ currency($order->total) }}</td>
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
    <!-- End crancy Dashboard -->
@endsection
