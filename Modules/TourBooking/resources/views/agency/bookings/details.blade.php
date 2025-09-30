@extends('agency.master_layout')
@section('title')
    <title>{{ __('translate.Booking Details') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Booking Details') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Dashboard') }} >> {{ __('translate.Booking Details') }}</p>
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

                            <div class="row justify-content-center">
                                <div class="col-10 mg-top-30">
                                    <div class="ed-invoice-page-wrapper">
                                        <div class="ed-invoice-main-wrapper">

                                            <div class="ed-invoice-page">
                                                <div class="ed-inv-logo-area">
                                                    <div class="ed-main-logo">
                                                        <img src="{{ asset($general_setting->logo) }}" alt="logo"
                                                            class="ed-logo">
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('agency.tourbooking.bookings.index') }}"
                                                            class="crancy-btn"><i class="fa fa-arrow-left"></i>
                                                            {{ __('translate.Back') }}</a>

                                                        <a href="#" class="crancy-btn crancy-btn__success"
                                                            data-bs-toggle="modal" data-bs-target="#paymentStatusModal">
                                                            <i class="fa fa-check"></i>
                                                            Change payment status
                                                        </a>

                                                        @if ($booking->booking_status == 'pending' || $booking->booking_status == 'success')
                                                            <a href="#" class="crancy-btn crancy-btn__success"
                                                                data-bs-toggle="modal" data-bs-target="#confirmModal">
                                                                <i class="fa fa-check"></i>
                                                                {{ __('translate.Confirm Booking') }}
                                                            </a>
                                                            <a href="#" class="crancy-btn crancy-btn__danger"
                                                                data-bs-toggle="modal" data-bs-target="#cancelModal">
                                                                <i class="fa fa-times"></i>
                                                                {{ __('translate.Cancel Booking') }}
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('agency.tourbooking.bookings.invoice', $booking->id) }}"
                                                            class="crancy-btn" target="_blank">
                                                            <i class="fa fa-file-invoice"></i>
                                                            {{ __('translate.View Invoice') }}
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="ed-inv-billing-info">
                                                    <div class="ed-inv-info">
                                                        <p class="ed-inv-info-title">{{ __('translate.Billed To') }}
                                                        </p>
                                                        <table>
                                                            <tr>
                                                                <td>{{ __('translate.Name') }}:</td>
                                                                <td> {{ $booking->customer_name ?? 'NA' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Phone') }}:</td>
                                                                <td>{{ $booking?->customer_email }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Email') }}:</td>
                                                                <td>{{ $booking?->customer_phone }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Address') }} : </td>
                                                                <td> {{ $booking?->customer_address }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="ed-inv-billing-info">
                                                    <div class="ed-inv-info">
                                                        <p class="ed-inv-info-title">
                                                            {{ __('translate.Booking Information') }}
                                                        </p>
                                                        <table>
                                                            <tr>
                                                                <td>{{ __('translate.Invoice No') }}:</td>
                                                                <td>#{{ $booking->booking_code }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Booking Status') }}:</td>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-{{ $booking->booking_status == 'confirmed' ? 'success' : ($booking->booking_status == 'pending' ? 'warning' : ($booking->booking_status == 'cancelled' ? 'danger' : 'info')) }}">
                                                                        {{ ucfirst($booking->booking_status) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Payment Status') }} : </td>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-{{ $booking->payment_status == 'completed' ? 'success' : 'warning' }}">
                                                                        {{ ucfirst($booking->payment_status) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Payment Method') }} : </td>
                                                                <td>
                                                                    {{ ucfirst($booking->payment_method) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Total Amount') }} : </td>
                                                                <td>
                                                                    {{ currency($booking->total) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Paid Amount') }} : </td>
                                                                <td>
                                                                    {{ currency($booking->paid_amount) }}
                                                                </td>
                                                            </tr>
                                                            @if ($booking->due_amount > 0)
                                                                <tr>
                                                                    <td>{{ __('translate.Due Amount') }} : </td>
                                                                    <td>
                                                                        {{ currency($booking->due_amount) }}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </table>

                                                    </div>
                                                    <div class="ed-inv-info">
                                                        <p class="ed-inv-info-title">
                                                            {{ __('translate.Service Information') }}
                                                        </p>
                                                        <table>
                                                            <tr>
                                                                <td>{{ __('translate.Title') }}:</td>
                                                                <td> {{ $booking->service->title ?? 'NA' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Location') }} : </td>
                                                                <td>{{ $booking?->service?->location }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('translate.Check in Date') }}:</td>
                                                                <td>{{ date('d M Y', strtotime($booking->check_in_date)) }}
                                                                </td>
                                                            </tr>
                                                            @if ($booking->check_out_date)
                                                                <tr>
                                                                    <td>{{ __('translate.Check out Date') }}:</td>
                                                                    <td>{{ date('d M Y', strtotime($booking->check_out_date)) }}
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            @if ($booking->check_in_time)
                                                                <tr>
                                                                    <td>{{ __('translate.Check in Time') }}:</td>
                                                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_time)->format('h:i A') }}
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            @if ($booking->check_out_time)
                                                                <tr>
                                                                    <td>{{ __('translate.Check out Time') }}:</td>
                                                                    <td>{{ \Carbon\Carbon::parse($booking->check_out_time)->format('h:i A') }}
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            <tr>
                                                                <td>{{ __('translate.Adults') }} : </td>
                                                                <td> {{ $booking?->adults }}</td>
                                                            </tr>

                                                            <tr>
                                                                <td>{{ __('translate.Children') }} : </td>
                                                                <td> {{ $booking?->children }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="mb-0">
                                                                    {{ __('translate.Price Details') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>{{ __('translate.Description') }}</th>
                                                                            <th class="text-right">
                                                                                {{ __('translate.Amount') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        @if ($booking->is_per_person == 1)
                                                                            <tr>
                                                                                <td>{{ __('translate.Adult Price') }}
                                                                                    ({{ $booking->adult_price }} X
                                                                                    {{ $booking->adults }}
                                                                                    {{ __('translate.Adults') }})</td>
                                                                                <td class="text-right">
                                                                                    {{ currency($booking->adult_price * $booking->adults) }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>{{ __('translate.Child Price') }}
                                                                                    ({{ $booking->child_price }} X
                                                                                    {{ $booking->children }}
                                                                                    {{ __('translate.Child') }})</td>
                                                                                <td class="text-right">
                                                                                    {{ currency($booking->child_price * $booking->children) }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>{{ __('translate.Extra charges') }}
                                                                                </td>
                                                                                <td class="text-right">
                                                                                    {{ currency($booking->extra_charges) }}
                                                                                </td>
                                                                            </tr>
                                                                        @else
                                                                            <tr>
                                                                                <td>{{ __('translate.Service Price') }}
                                                                                </td>
                                                                                <td class="text-right">
                                                                                    {{ currency($booking->service_price) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endif

                                                                        @if ($booking->infants > 0 && $booking->service->infant_price > 0)
                                                                            <tr>
                                                                                <td>{{ __('translate.Infant Price') }} x
                                                                                    {{ $booking->infants }}
                                                                                    {{ __('translate.Infants') }}</td>
                                                                                <td class="text-right">
                                                                                    {{ currency($booking->service->infant_price * $booking->infants) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endif

                                                                        @if ($booking->tax > 0)
                                                                            <tr>
                                                                                <td>{{ __('translate.Tax') }}
                                                                                    ({{ $booking->tax_percentage }}%)</td>
                                                                                <td class="text-right">
                                                                                    {{ currency($booking->tax) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th>{{ __('translate.Total') }}</th>
                                                                            <th class="text-right">
                                                                                {{ currency($booking->total) }}
                                                                            </th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($extra_services->count() > 0)
                                                    <div class="ed-inv-billing-info mt-5">
                                                        <div class="ed-inv-info">
                                                            <p class="ed-inv-info-title">
                                                                {{ __('translate.Extra Services List') }}
                                                            </p>
                                                            <table>
                                                                @foreach ($extra_services as $key => $extra)
                                                                    <tr>
                                                                        <td class="text-capitalize mr-2">
                                                                            {{ $extra->name }}
                                                                            ({{ Str::title(str_replace('_', ' ', $extra->price_type)) }})
                                                                            -- {{ currency($extra->price) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
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

    <!-- Confirm Booking Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">{{ __('translate.Confirm Booking') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('agency.tourbooking.bookings.confirm', ['id' => $booking->id]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>{{ __('translate.Are you sure you want to confirm this booking?') }}</p>
                        <div class="form-group">
                            <label>{{ __('translate.Confirmation Message') }} ({{ __('translate.Optional') }})</label>
                            <textarea class="form-control" name="confirmation_message" rows="3"
                                placeholder="{{ __('translate.Enter message to send to customer') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="crancy-btn crancy-btn__default"
                            data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                        <button type="submit"
                            class="crancy-btn crancy-btn__success">{{ __('translate.Confirm Booking') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">{{ __('translate.Cancel Booking') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('agency.tourbooking.bookings.cancel', ['id' => $booking->id]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>{{ __('translate.Are you sure you want to cancel this booking?') }}</p>
                        <div class="form-group">
                            <label>{{ __('translate.Cancellation Reason') }} *</label>
                            <textarea class="form-control" name="cancellation_reason" rows="3" required
                                placeholder="{{ __('translate.Enter reason for cancellation') }}"></textarea>
                        </div>
                        <div class="form-group mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="refund" id="refundCheck">
                                <label class="form-check-label" for="refundCheck">
                                    {{ __('translate.Process Refund') }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group mt-3 refund-amount-container d-none">
                            <label>{{ __('translate.Refund Amount') }}</label>
                            <input type="number" class="form-control" name="refund_amount" step="0.01"
                                min="0" max="{{ $booking->paid_amount }}" value="{{ $booking->paid_amount }}">
                            <small class="text-muted">{{ __('translate.Maximum refund amount is') }}
                                {{ currency($booking->paid_amount) }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="crancy-btn crancy-btn__default"
                            data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                        <button type="submit"
                            class="crancy-btn crancy-btn__danger">{{ __('translate.Cancel Booking') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel">{{ __('translate.Add Admin Note') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('agency.tourbooking.bookings.add-note', $booking->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('translate.Note') }} *</label>
                            <textarea class="form-control" name="note" rows="3" required
                                placeholder="{{ __('translate.Enter your note') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="crancy-btn crancy-btn__default"
                            data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                        <button type="submit" class="crancy-btn">{{ __('translate.Add Note') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- paymentStatusModal -->
    <div class="modal fade" id="paymentStatusModal" tabindex="-1" aria-labelledby="paymentStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentStatusModalLabel">Change Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('agency.tourbooking.bookings.payment-status', ['booking' => $booking]) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to change the payment status?</p>
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select class="form-control" name="payment_status" required>
                                <option value="">Select Payment Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="crancy-btn crancy-btn__default"
                            data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                        <button type="submit" class="crancy-btn crancy-btn__success">Change Payment Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('js_section')
    <script>
        (function($) {
            "use strict"
            $(document).ready(function() {
                // Show/hide refund amount based on checkbox
                $('#refundCheck').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('.refund-amount-container').removeClass('d-none');
                    } else {
                        $('.refund-amount-container').addClass('d-none');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
