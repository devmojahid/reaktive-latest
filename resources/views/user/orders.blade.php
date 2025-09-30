@extends('user.master_layout')
@section('title')
    <title>{{ __('translate.Order List') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Order List') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Dashboard') }} >> {{ __('translate.Order List') }}</p>
@endsection

@section('body-content')
    <!-- crancy Dashboard -->
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">

                            <div class="crancy-table crancy-table--v3 mg-top-30">

                                <div class="crancy-customer-filter">
                                    <div
                                        class="crancy-customer-filter__single crancy-customer-filter__single--csearch d-flex items-center justify-between create_new_btn_box">
                                        <div
                                            class="crancy-header__form crancy-header__form--customer create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Order List') }}</h4>

                                        </div>
                                    </div>
                                </div>

                                <!-- crancy Table -->
                                <div id="crancy-table__main_wrapper" class=" dt-bootstrap5 no-footer">

                                    <table class="crancy-table__main crancy-table__main-v3  no-footer" id="dataTable">
                                        <!-- crancy Table Head -->
                                        <thead class="crancy-table__head">
                                            <tr>

                                                @if (Route::is('user.transactions'))
                                                    <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                        {{ __('translate.Transactions ID') }}
                                                    </th>
                                                @endif

                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Order ID') }}
                                                </th>

                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Date') }}
                                                </th>

                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Total Amount') }}
                                                </th>

                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Payment') }}
                                                </th>

                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Order Status') }}
                                                </th>

                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">
                                                    {{ __('translate.Action') }}
                                                </th>
                                            </tr>
                                        </thead>

                                        <!-- crancy Table Body -->
                                        <tbody class="crancy-table__body">
                                            @foreach ($orders as $index => $order)
                                                <tr class="odd">

                                                    @if (Route::is('user.transactions'))
                                                        <td class="crancy-table__column-2 crancy-table__data-2">
                                                            <a href="javascript:void(0)">
                                                                {{ $order->transaction_id }}
                                                            </a>
                                                        </td>
                                                    @endif

                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <h4 class="crancy-table__product-title">
                                                            #{{ $order?->order_id }}</h4>
                                                    </td>

                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <h4 class="crancy-table__product-title">
                                                            {{ $order->created_at->format('d M, Y') }}</h4>
                                                    </td>

                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <h4 class="crancy-table__product-title">
                                                            {{ currency($order->total) }}
                                                        </h4>
                                                    </td>

                                                    @if (Route::is('user.orders'))
                                                        <td>
                                                            @if ($order->payment_status == 1)
                                                                <span class="paid_btn">
                                                                    {{ __('translate.PAID') }}
                                                                </span>
                                                            @else
                                                                <span class="paid_btn unpaid_btn">
                                                                    {{ __('translate.UNPAID') }}
                                                                </span>
                                                            @endif

                                                        </td>
                                                    @endif

                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        @if ($order->order_status == 0)
                                                            <span class="pending_status">
                                                                {{ __('translate.Pending') }}
                                                            </span>
                                                        @elseif($order->order_status == 1)
                                                            <span class="pending_status completed_status">
                                                                {{ __('translate.Completed') }}
                                                            </span>
                                                        @elseif($order->order_status == 2)
                                                            <span class="pending_status ">
                                                                {{ __('translate.Rejected') }}
                                                            </span>
                                                        @elseif($order->order_status == 3)
                                                            <span class="pending_status completed_status">
                                                                {{ __('translate.Processing') }}
                                                            </span>
                                                        @elseif($order->order_status == 4)
                                                            <span class="pending_status completed_status">
                                                                {{ __('translate.Shipped') }}
                                                            </span>
                                                        @else
                                                            <span class="pending_status completed_status">
                                                                {{ __('translate.Completed') }}
                                                            </span>
                                                        @endif

                                                    </td>

                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <a href="{{ route('user.order_show', $order->order_id) }}"
                                                            class="crancy-btn"><i class="fas fa-eye"></i>
                                                            {{ __('translate.Details') }}</a>
                                                    </td>

                                                </tr>
                                            @endforeach

                                        </tbody>
                                        <!-- End crancy Table Body -->
                                    </table>
                                </div>
                                <!-- End crancy Table -->
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- End crancy Dashboard -->
@endsection
