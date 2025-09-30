@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Booking Details') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Booking Details') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Dashboard') }} >> {{ __('translate.Booking Details') }}</p>
@endsection

@section('body-content')
@php
    // ---------- Normalize JSON/array fields ----------
    $decode = function ($v) {
        if (is_array($v)) return $v;
        if (is_string($v)) {
            $d = json_decode($v, true);
            return json_last_error() === JSON_ERROR_NONE ? ($d ?: []) : [];
        }
        return [];
    };

    $ageBreakdown = $decode($booking->age_breakdown ?? []);
    $ageQuantities = $decode($booking->age_quantities ?? []);
    $ageConfig     = $decode($booking->age_config ?? []);

    if (empty($ageBreakdown) && !empty($ageQuantities)) {
        foreach ($ageQuantities as $k => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) continue;
            $label = $ageConfig[$k]['label'] ?? ucfirst((string)$k);
            $price = (float)($ageConfig[$k]['price'] ?? 0);
            $ageBreakdown[$k] = [
                'label' => $label,
                'qty'   => $qty,
                'price' => $price,
                'line'  => $price * $qty,
            ];
        }
    }

    // ---------- Badges ----------
    $bookingStatusClass = match (strtolower((string)$booking->booking_status)) {
        'confirmed','success','completed' => 'success',
        'pending'                         => 'warning',
        'cancelled'                       => 'danger',
        default                           => 'info',
    };

    $paymentStatusClass = in_array(strtolower((string)$booking->payment_status), ['success','completed','confirmed'])
        ? 'success'
        : (strtolower((string)$booking->payment_status) === 'cancelled' ? 'danger' : 'warning');
@endphp

<style>
  /* ===== Actions header buttons (brand color) ===== */
  .btn-action{ border-radius:999px; padding:.42rem .95rem; font-weight:700; }
  .btn-orange{ background:#ff4200 !important; border-color:#ff4200 !important; color:#fff !important; }
  .btn-orange:hover,
  .btn-orange:focus{ background:#e63b00 !important; border-color:#e63b00 !important; color:#fff !important; }
  .btn-orange:active{ background:#d23600 !important; border-color:#d23600 !important; color:#fff !important; }
  .btn-orange i{ color:#fff !important; }

  .page-actions .btn-action{ border-color:#e9ecef; }
  .head-logo{ height:32px; width:auto; border-radius:6px; }
  .booking-code{ font-weight:700; font-size:1.05rem; letter-spacing:.3px; }

  /* ===== Quick stats ===== */
  .stat-cards { display:grid; gap:.75rem; grid-template-columns: repeat(3,minmax(0,1fr)); }
  .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.75rem; padding:.9rem 1rem; background:#fff }
  .stat-card .label { font-size:.85rem; color:#6c757d; }
  .stat-card .value { font-weight:700; font-size:1.05rem; }

  /* ===== Info blocks – symmetric, top aligned ===== */
  .ed-inv-billing-info{
    display:grid;
    grid-template-columns: repeat(3, minmax(0,1fr));
    gap:18px;
    align-items:flex-start;
  }
  @media (max-width: 1199.98px){
    .ed-inv-billing-info{ grid-template-columns: 1fr;  align-items: start !important; /* aliniere sus pentru toate item-urile */}
  }
  .ed-inv-info{
    background:#fff; border:1px solid #eef1f5; border-radius:14px; padding:16px 18px; height:100%;
  }
  .ed-inv-info table{ width:100%; }
  .ed-inv-info table td:first-child{ color:#6c757d; padding-right:12px; white-space:nowrap; }
  .ed-inv-info .ed-inv-info-title{ font-weight:800; color:#0d1730; margin-bottom:10px; }
  .ed-inv-billing-info .ed-inv-info{
  align-self: start !important;
  }

  /* Guests breakdown */
  .section-title { font-size:.95rem; font-weight:700; color:#343a40; margin-bottom:.5rem; }
  .chip{ display:inline-flex; align-items:center; padding:.35rem .7rem; border-radius:999px; border:1px solid #e8ecf3; background:#fff; margin:.2rem .35rem .2rem 0; font-weight:600; color:#2a3247; box-shadow:0 1px 0 rgba(16,24,40,.04); }

  .table > :not(caption) > * > * { vertical-align: middle; }
</style>

<section class="crancy-adashboard crancy-show">
    <div class="container container__bscreen">
        <div class="row">
            <div class="col-12">
                <div class="crancy-body">
                    <div class="crancy-dsinner">

                        <div class="row justify-content-center">
                            <div class="col-12 col-xxl-10 mg-top-30">
                                <div class="ed-invoice-page-wrapper">
                                    <div class="ed-invoice-main-wrapper">
                                        <div class="ed-invoice-page">

{{-- ===== Header: back + actions ===== --}}
<div class="page-head mb-2">
  <div class="page-head-top d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.tourbooking.bookings.index') }}" class="btn btn-sm btn-orange btn-action">
        <i class="fa fa-arrow-left me-1"></i>{{ __('translate.Back') }}
      </a>
    </div>

    <div class="page-actions d-flex flex-wrap gap-2">
      <a href="{{ route('admin.tourbooking.bookings.invoice', $booking->id) }}" target="_blank"
         class="btn btn-sm btn-orange btn-action">
        <i class="fa fa-file-invoice me-1"></i>{{ __('translate.View Invoice') }}
      </a>

      <a href="{{ route('admin.tourbooking.bookings.download-invoice', $booking->id) }}"
         class="btn btn-sm btn-orange btn-action">
        <i class="fa fa-download me-1"></i>{{ __('translate.Download Invoice') }}
      </a>

      <a href="#" class="btn btn-sm btn-orange btn-action" data-bs-toggle="modal" data-bs-target="#paymentStatusModal">
        <i class="fa fa-credit-card me-1"></i>{{ __('translate.Change Payment Status') }}
      </a>

      @if (in_array(strtolower((string)$booking->booking_status), ['pending','success']))
        <a href="#" class="btn btn-sm btn-success btn-action" data-bs-toggle="modal" data-bs-target="#confirmModal">
          <i class="fa fa-check me-1"></i>{{ __('translate.Confirm Booking') }}
        </a>
        <a href="#" class="btn btn-sm btn-orange btn-action" data-bs-toggle="modal" data-bs-target="#cancelModal">
          <i class="fa fa-times me-1"></i>{{ __('translate.Cancel Booking') }}
        </a>
      @endif
    </div>
  </div>

  <div class="page-head-main d-flex align-items-center gap-3 mt-3">
    @isset($general_setting->logo)
       <img src="{{ asset($general_setting->logo) }}" alt="logo" class="head-logo"> 
    @endisset

    <div class="d-flex flex-wrap align-items-center gap-2">
      <span class="booking-code">#{{ $booking->booking_code }}</span>
      <span class="badge bg-{{ $bookingStatusClass }}">{{ ucfirst($booking->booking_status) }}</span>
      <span class="badge bg-{{ $paymentStatusClass }}">{{ ucfirst($booking->payment_status) }}</span>
    </div>
  </div>
</div>

{{-- ===== Quick stats ===== --}}
<div class="stat-cards my-3">
  <div class="stat-card">
      <div class="label">{{ __('translate.Total Amount') }}</div>
      <div class="value">{{ currency((float)$booking->total) }}</div>
  </div>
  <div class="stat-card">
      <div class="label">{{ __('translate.Paid Amount') }}</div>
      <div class="value">{{ currency((float)$booking->paid_amount) }}</div>
  </div>
  <div class="stat-card">
      <div class="label">{{ __('translate.Due Amount') }}</div>
      <div class="value">
          {{ (float)($booking->due_amount ?? 0) > 0 ? currency((float)$booking->due_amount) : '—' }}
      </div>
  </div>
</div>

{{-- ===== Info blocks – grid, aligned top & symmetric ===== --}}
<div class="ed-inv-billing-info">
  <div class="ed-inv-info">
      <p class="ed-inv-info-title">{{ __('translate.Billed To') }}</p>
      <table>
          <tr><td>{{ __('translate.Name') }}:</td><td>{{ $booking->customer_name ?? '—' }}</td></tr>
          <tr><td>{{ __('translate.Phone') }}:</td><td>{{ $booking->customer_phone ?? '—' }}</td></tr>
          <tr><td>{{ __('translate.Email') }}:</td><td>{{ $booking->customer_email ?? '—' }}</td></tr>
          <tr><td>{{ __('translate.Address') }}:</td><td>{{ $booking->customer_address ?? '—' }}</td></tr>
      </table>
  </div>

  <div class="ed-inv-info">
      <p class="ed-inv-info-title">{{ __('translate.Booking Information') }}</p>
      <table>
          <tr><td>{{ __('translate.Invoice No') }}:</td><td>#{{ $booking->booking_code }}</td></tr>
          <tr><td>{{ __('translate.Payment Method') }}:</td><td>{{ $booking->payment_method ? ucfirst($booking->payment_method) : '—' }}</td></tr>
          <tr><td>{{ __('translate.Check in Date') }}:</td><td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td></tr>
          @if ($booking->check_out_date)
              <tr><td>{{ __('translate.Check out Date') }}:</td><td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</td></tr>
          @endif
          @if ($booking->check_in_time)
              <tr><td>{{ __('translate.Check in Time') }}:</td><td>{{ \Carbon\Carbon::parse($booking->check_in_time)->format('h:i A') }}</td></tr>
          @endif
          @if ($booking->check_out_time)
              <tr><td>{{ __('translate.Check out Time') }}:</td><td>{{ \Carbon\Carbon::parse($booking->check_out_time)->format('h:i A') }}</td></tr>
          @endif
      </table>
  </div>

  <div class="ed-inv-info">
      <p class="ed-inv-info-title">{{ __('translate.Service Information') }}</p>
      <table>
          <tr><td>{{ __('translate.Title') }}:</td><td>{{ $booking->service->title ?? '—' }}</td></tr>
          <tr><td>{{ __('translate.Location') }}:</td><td>{{ $booking->service->location ?? '—' }}</td></tr>
          <tr><td>{{ __('translate.Adults') }}:</td><td>{{ (int)$booking->adults }}</td></tr>
          <tr><td>{{ __('translate.Children') }}:</td><td>{{ (int)$booking->children }}</td></tr>
      </table>

      @if(!empty($ageBreakdown))
          <div class="mt-3">
              <div class="section-title">{{ __('translate.Guests breakdown') }}</div>
              @foreach($ageBreakdown as $row)
                  <span class="chip">
                      {{ $row['label'] ?? 'Category' }} · {{ (int)($row['qty'] ?? 0) }}
                  </span>
              @endforeach
          </div>
      @endif
  </div>
</div>

{{-- ===== Notes (optional) ===== --}}
@if (!empty($booking->customer_notes))
  <div class="mt-3">
      <div class="section-title">{{ __('translate.Your Notes') }}</div>
      <div class="p-3 bg-light rounded">{{ $booking->customer_notes }}</div>
  </div>
@endif

{{-- ===== Price details ===== --}}
<div class="row mt-4">
  <div class="col-12">
      <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">{{ __('translate.Price Details') }}</h5>
          </div>
          <div class="card-body">
              <div class="table-responsive">
                  <table class="table align-middle">
                      <thead class="table-light">
                          <tr>
                              <th style="width:70%">{{ __('translate.Description') }}</th>
                              <th class="text-end">{{ __('translate.Amount') }}</th>
                          </tr>
                      </thead>
                      <tbody>
                          @if(!empty($ageBreakdown))
                              @foreach($ageBreakdown as $row)
                                  @php
                                      $unit = (float)($row['price'] ?? 0);
                                      $qty  = (int)($row['qty'] ?? 0);
                                      $line = (float)($row['line'] ?? ($unit * $qty));
                                  @endphp
                                  <tr>
                                      <td>{{ $row['label'] ?? 'Category' }} ({{ number_format($unit, 2) }} × {{ $qty }})</td>
                                      <td class="text-end">{{ currency($line) }}</td>
                                  </tr>
                              @endforeach
                          @else
                              @if ($booking->is_per_person == 1)
                                  <tr>
                                      <td>{{ __('translate.Adult Price') }} ({{ number_format((float)$booking->adult_price, 2) }} × {{ (int)$booking->adults }} {{ __('translate.Adults') }})</td>
                                      <td class="text-end">{{ currency(((float)$booking->adult_price) * ((int)$booking->adults)) }}</td>
                                  </tr>
                                  @if ((int)$booking->children > 0)
                                      <tr>
                                          <td>{{ __('translate.Child Price') }} ({{ number_format((float)$booking->child_price, 2) }} × {{ (int)$booking->children }} {{ __('translate.Children') }})</td>
                                          <td class="text-end">{{ currency(((float)$booking->child_price) * ((int)$booking->children)) }}</td>
                                      </tr>
                                  @endif
                              @else
                                  <tr>
                                      <td>{{ __('translate.Service Price') }}</td>
                                      <td class="text-end">{{ currency((float)$booking->service_price) }}</td>
                                  </tr>
                              @endif
                          @endif

                          @if ((float)($booking->extra_charges ?? 0) != 0)
                              <tr>
                                  <td>{{ __('translate.Extra charges') }}</td>
                                  <td class="text-end">{{ currency((float)$booking->extra_charges) }}</td>
                              </tr>
                          @endif

                          @if (!empty($booking->tax) && (float)$booking->tax > 0)
                              <tr>
                                  <td>{{ __('translate.Tax') }} @if(!empty($booking->tax_percentage)) ({{ (float)$booking->tax_percentage }}%) @endif</td>
                                  <td class="text-end">{{ currency((float)$booking->tax) }}</td>
                              </tr>
                          @endif
                      </tbody>
                      <tfoot class="table-light">
                          <tr>
                              <th>{{ __('translate.Total') }}</th>
                              <th class="text-end">{{ currency((float)$booking->total) }}</th>
                          </tr>
                      </tfoot>
                  </table>
              </div>
          </div>
      </div>
  </div>
</div>

{{-- ===== Extra services list ===== --}}
@if(isset($extra_services) && $extra_services->count() > 0)
  <div class="ed-inv-billing-info mt-4">
      <div class="ed-inv-info">
          <p class="ed-inv-info-title">{{ __('translate.Extra Services List') }}</p>
          <table>
              @foreach ($extra_services as $extra)
                  <tr>
                      <td class="text-capitalize">
                          {{ $extra->name }}
                          ({{ \Illuminate\Support\Str::title(str_replace('_', ' ', $extra->price_type)) }})
                          — {{ currency($extra->price) }}
                      </td>
                  </tr>
              @endforeach
          </table>
      </div>
  </div>
@endif

                                        </div> {{-- /ed-invoice-page --}}
                                    </div>
                                </div>
                            </div>
                        </div>

{{-- ===== Modals (admin routes) ===== --}}
@if (in_array(strtolower((string)$booking->booking_status), ['pending','success']))
  <!-- Confirm Booking Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="confirmModalLabel">{{ __('translate.Confirm Booking') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('admin.tourbooking.bookings.confirm', ['id' => $booking->id]) }}" method="POST">
                  @csrf
                  <div class="modal-body">
                      <p>{{ __('translate.Are you sure you want to confirm this booking?') }}</p>
                      <div class="form-group">
                          <label>{{ __('translate.Confirmation Message') }} ({{ __('translate.Optional') }})</label>
                          <textarea class="form-control" name="confirmation_message" rows="3" placeholder="{{ __('translate.Enter message to send to customer') }}"></textarea>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="crancy-btn crancy-btn__default" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                      <button type="submit" class="crancy-btn crancy-btn__success">{{ __('translate.Confirm Booking') }}</button>
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
              <form action="{{ route('admin.tourbooking.bookings.cancel', ['id' => $booking->id]) }}" method="POST">
                  @csrf
                  <div class="modal-body">
                      <p>{{ __('translate.Are you sure you want to cancel this booking?') }}</p>
                      <div class="form-group">
                          <label>{{ __('translate.Cancellation Reason') }} *</label>
                          <textarea class="form-control" name="cancellation_reason" rows="3" required placeholder="{{ __('translate.Enter reason for cancellation') }}"></textarea>
                      </div>
                      <div class="form-group mt-3">
                          <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="refund" id="refundCheck">
                              <label class="form-check-label" for="refundCheck">{{ __('translate.Process Refund') }}</label>
                          </div>
                      </div>
                      <div class="form-group mt-3 refund-amount-container d-none">
                          <label>{{ __('translate.Refund Amount') }}</label>
                          <input type="number" class="form-control" name="refund_amount" step="0.01" min="0" max="{{ $booking->paid_amount }}" value="{{ $booking->paid_amount }}">
                          <small class="text-muted">{{ __('translate.Maximum refund amount is') }} {{ currency((float)$booking->paid_amount) }}</small>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="crancy-btn crancy-btn__default" data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                      <button type="submit" class="crancy-btn crancy-btn__danger">{{ __('translate.Cancel Booking') }}</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
@endif

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNoteModalLabel">{{ __('translate.Add Admin Note') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.tourbooking.bookings.add-note', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('translate.Note') }} *</label>
                        <textarea class="form-control" name="note" rows="3" required placeholder="{{ __('translate.Enter your note') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="crancy-btn crancy-btn__default" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                    <button type="submit" class="crancy-btn">{{ __('translate.Add Note') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Payment Status Modal -->
<div class="modal fade" id="paymentStatusModal" tabindex="-1" aria-labelledby="paymentStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentStatusModalLabel">{{ __('translate.Change Payment Status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.tourbooking.bookings.payment-status', ['booking' => $booking]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>{{ __('translate.Are you sure you want to change the payment status?') }}</p>
                    <div class="form-group">
                        <label>{{ __('translate.Payment Status') }}</label>
                        <select class="form-control" name="payment_status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="crancy-btn crancy-btn__default" data-bs-dismiss="modal">{{ __('translate.Cancel') }}</button>
                    <button type="submit" class="crancy-btn crancy-btn__success">{{ __('translate.Change Payment Status') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- ===== end modals ===== --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js_section')
<script>
(function ($) {
    "use strict";
    $(document).ready(function () {
        $('#refundCheck').on('change', function () {
            $('.refund-amount-container').toggleClass('d-none', !$(this).is(':checked'));
        });
    });
})(jQuery);
</script>
@endpush
