@extends('layouts.frontend')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('translate.Booking Details') }}: #{{ $booking->booking_code }}</h5>
                            <div>
                                <a href="{{ route('front.tourbooking.my-bookings.invoice', $booking->booking_code) }}"
                                    class="btn btn-sm btn-light me-2">
                                    <i class="bi bi-file-earmark-text"></i> {{ __('translate.View Invoice') }}
                                </a>
                                <a href="{{ route('front.tourbooking.my-bookings.download-invoice', $booking->booking_code) }}"
                                    class="btn btn-sm btn-light">
                                    <i class="bi bi-download"></i> {{ __('translate.Download Invoice') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            // === Extragem configurarea și cantitățile pe categorii, dacă există în booking ===
                            $ageQuantities = is_array($booking->age_quantities)
                                ? $booking->age_quantities
                                : (json_decode($booking->age_quantities ?? '[]', true) ?: []);
                            $ageConfig = is_array($booking->age_config)
                                ? $booking->age_config
                                : (json_decode($booking->age_config ?? '[]', true) ?: []);

                            // mic helper pt eticheta categoriei
                            $labelFor = function ($key) use ($ageConfig) {
                                return $ageConfig[$key]['label'] ?? ucfirst(str_replace('_', ' ', (string) $key));
                            };
                            // preț unitar (dacă e stocat în booking->age_config)
                            $unitPriceFor = function ($key) use ($ageConfig) {
                                $v = $ageConfig[$key]['price'] ?? null;
                                return is_numeric($v) ? (float) $v : null;
                            };
                            // filtrăm doar categoriile cu cantitate > 0
                            $ageRows = collect($ageQuantities)
                                ->filter(fn ($q) => (int) $q > 0)
                                ->map(function ($qty, $key) use ($labelFor, $unitPriceFor) {
                                    $price = $unitPriceFor($key);
                                    return [
                                        'key' => $key,
                                        'label' => $labelFor($key),
                                        'qty' => (int) $qty,
                                        'unit_price' => $price,
                                        'line_total' => $price !== null ? ((float) $price) * ((int) $qty) : null,
                                    ];
                                })
                                ->values();
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">{{ __('translate.Booking Information') }}</h6>
                                <p><strong>{{ __('translate.Booking Status') }}:</strong>
                                    <span
                                        class="badge bg-{{ $booking->booking_status == 'confirmed' ? 'success' : ($booking->booking_status == 'pending' ? 'warning' : ($booking->booking_status == 'cancelled' ? 'danger' : 'info')) }}">
                                        {{ ucfirst($booking->booking_status) }}
                                    </span>
                                </p>
                                <p><strong>{{ __('translate.Payment Status') }}:</strong>
                                    <span
                                        class="badge bg-{{ $booking->payment_status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </p>
                                <p><strong>{{ __('translate.Payment Method') }}:</strong>
                                    {{ ucfirst($booking->payment_method) }}</p>
                                <p><strong>{{ __('translate.Total Amount') }}:</strong>
                                    {{ currencyConverter($booking->total) }}</p>
                                <p><strong>{{ __('translate.Paid Amount') }}:</strong>
                                    {{ currencyConverter($booking->paid_amount) }}</p>
                                @if ($booking->due_amount > 0)
                                    <p><strong>{{ __('translate.Due Amount') }}:</strong>
                                        {{ currencyConverter($booking->due_amount) }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">{{ __('translate.Service Information') }}</h6>
                                <p><strong>{{ __('translate.Service') }}:</strong> {{ $booking->service->title }}</p>
                                <p><strong>{{ __('translate.Type') }}:</strong>
                                    {{ $booking->service->serviceType->name ?? 'N/A' }}</p>

                                {{-- Dacă avem categorii de vârstă salvate, afișăm tabelul pe categorii.
                                     Altfel, rămâne afișarea clasică (adulți/copii/infants) EXACT cum era. --}}
                                @if($ageRows->isNotEmpty())
                                    <div class="mt-2">
                                        <strong>{{ __('translate.Tickets Summary') }}:</strong>
                                        <div class="table-responsive mt-2">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translate.Category') }}</th>
                                                        <th class="text-center">{{ __('translate.Quantity') }}</th>
                                                        <th class="text-end">{{ __('translate.Unit Price') }}</th>
                                                        <th class="text-end">{{ __('translate.Total') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $ageLinesTotal = 0; @endphp
                                                    @foreach($ageRows as $row)
                                                        @php
                                                            $unit = $row['unit_price'];
                                                            $line = $row['line_total'];
                                                            if($line !== null) { $ageLinesTotal += $line; }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $row['label'] }}</td>
                                                            <td class="text-center">{{ $row['qty'] }}</td>
                                                            <td class="text-end">
                                                                {{ $unit !== null ? currencyConverter($unit) : '—' }}
                                                            </td>
                                                            <td class="text-end">
                                                                {{ $line !== null ? currencyConverter($line) : '—' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                @if($ageLinesTotal > 0)
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="3" class="text-end">{{ __('translate.Subtotal') }}</th>
                                                            <th class="text-end">{{ currencyConverter($ageLinesTotal) }}</th>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <p><strong>{{ __('translate.Adults') }}:</strong> {{ $booking->adults }}</p>
                                    @if ($booking->children > 0)
                                        <p><strong>{{ __('translate.Children') }}:</strong> {{ $booking->children }}</p>
                                    @endif
                                    @if ($booking->infants > 0)
                                        <p><strong>{{ __('translate.Infants') }}:</strong> {{ $booking->infants }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if ($booking->customer_notes)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="text-muted">{{ __('translate.Your Notes') }}</h6>
                                    <p>{{ $booking->customer_notes }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted">{{ __('translate.Actions') }}</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('front.tourbooking.my-bookings') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> {{ __('translate.Back to Bookings') }}
                                    </a>

                                    @if ($booking->booking_status == 'pending' || $booking->booking_status == 'confirmed')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#cancelBookingModal">
                                            <i class="bi bi-x-circle"></i> {{ __('translate.Cancel Booking') }}
                                        </button>
                                    @endif

                                    @if ($booking->booking_status == 'completed' && !$booking->is_reviewed)
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#leaveReviewModal">
                                            <i class="bi bi-star"></i> {{ __('translate.Leave a Review') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> <!-- /card-body -->
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    @if ($booking->booking_status == 'pending' || $booking->booking_status == 'confirmed')
        <div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('front.tourbooking.my-bookings.cancel', $booking->booking_code) }}"
                        method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="cancelBookingModalLabel">{{ __('translate.Cancel Booking') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-danger">{{ __('translate.Are you sure you want to cancel this booking?') }}</p>
                            <div class="mb-3">
                                <label for="cancellation_reason"
                                    class="form-label">{{ __('translate.Reason for Cancellation') }}</label>
                                <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('translate.Cancel Booking') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Leave Review Modal -->
    @if ($booking->booking_status == 'completed' && !$booking->is_reviewed)
        <div class="modal fade" id="leaveReviewModal" tabindex="-1" aria-labelledby="leaveReviewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('front.tourbooking.my-bookings.review', $booking->booking_code) }}"
                        method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="leaveReviewModalLabel">{{ __('translate.Leave a Review') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="rating" class="form-label">{{ __('translate.Rating') }}</label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value="5">5 - {{ __('translate.Excellent') }}</option>
                                    <option value="4">4 - {{ __('translate.Very Good') }}</option>
                                    <option value="3">3 - {{ __('translate.Good') }}</option>
                                    <option value="2">2 - {{ __('translate.Fair') }}</option>
                                    <option value="1">1 - {{ __('translate.Poor') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="review_title" class="form-label">{{ __('translate.Title') }}</label>
                                <input type="text" class="form-control" id="review_title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="review_content" class="form-label">{{ __('translate.Review') }}</label>
                                <textarea class="form-control" id="review_content" name="content" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('translate.Submit Review') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
