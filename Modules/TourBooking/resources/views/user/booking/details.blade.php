@extends('user.master_layout')
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

    // Arătăm invoice doar când e confirmat + plata ok
    $canShowInvoice = in_array(strtolower((string)$booking->booking_status), ['confirmed','success','completed'], true)
        && in_array(strtolower((string)$booking->payment_status), ['success','completed','confirmed'], true);
@endphp

<style>
  .btn-action{ border-radius:12px; padding:.5rem 1rem; font-weight:700; }
  .btn-orange{ background:#ff4200 !important; border-color:#ff4200 !important; color:#fff !important; }
  .btn-orange:hover,.btn-orange:focus{ background:#e63b00 !important; border-color:#e63b00 !important; color:#fff !important; }
  .btn-ghost{ background:#fff; border-color:#e6e6e6; color:#1f2937; }
  .btn-ghost:hover{ background:#f7f7f7; border-color:#dcdcdc; color:#111827; }
  .btn-outline-danger{ --bs-btn-hover-bg:#dc3545; --bs-btn-hover-color:#fff; }

  .head-logo{ height:32px; width:auto; border-radius:6px; }
  .booking-code{ font-weight:700; font-size:1.05rem; letter-spacing:.3px; }

  .stat-cards { display:grid; gap:.75rem; grid-template-columns: repeat(3,minmax(0,1fr)); }
  .stat-card { border:1px solid rgba(0,0,0,.06); border-radius:.75rem; padding:.9rem 1rem; background:#fff }
  .stat-card .label { font-size:.85rem; color:#6c757d; }
  .stat-card .value { font-weight:700; font-size:1.05rem; }

  .ed-inv-billing-info{ display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:18px; align-items:flex-start; }
  @media (max-width: 1199.98px){ .ed-inv-billing-info{ grid-template-columns: 1fr; } }
  .ed-inv-info{ background:#fff; border:1px solid #eef1f5; border-radius:14px; padding:16px 18px; height:100%; }
  .ed-inv-info table{ width:100%; }
  .ed-inv-info table td:first-child{ color:#6c757d; padding-right:12px; white-space:nowrap; }
  .ed-inv-info .ed-inv-info-title{ font-weight:800; color:#0d1730; margin-bottom:10px; }
  .ed-inv-billing-info .ed-inv-info{ align-self:start !important; }

  .section-title { font-size:.95rem; font-weight:700; color:#343a40; margin-bottom:.5rem; }
  .chip{ display:inline-flex; align-items:center; padding:.35rem .7rem; border-radius:999px; border:1px solid #e8ecf3; background:#fff; margin:.2rem .35rem .2rem 0; font-weight:600; color:#2a3247; box-shadow:0 1px 0 rgba(16,24,40,.04); }

  .table > :not(caption) > * > * { vertical-align: middle; }

  /* === Footer action bar (Bottom) === */
  .btn-booking{
    height:48px;
    padding:0 22px;
    border-radius:12px;
    font-weight:700;
    display:inline-flex !important;
    align-items:center;
    gap:.5rem;
    line-height:1;
    width:auto !important;       /* împiedică întinderea/deformarea */
  }
  .page-footer-actions{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:.75rem;
    flex-wrap:wrap;              /* responsive pe ecrane mici */
  }
  .page-footer-actions .left-actions,
  .page-footer-actions .right-actions{
    display:flex;
    align-items:center;
    gap:.5rem;
    flex-wrap:wrap;
  }
  .btn-invoice{ background:#ff4200 !important; border-color:#ff4200 !important; color:#fff !important; }
  .btn-invoice:hover,.btn-invoice:focus{ background:#e63b00 !important; border-color:#e63b00 !important; color:#fff !important; }
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

                      {{-- ===== Header (logo + status) ===== --}}
                      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                        <div class="d-flex align-items-center gap-3">
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

                      {{-- ===== Info blocks ===== --}}
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
                          </table>
                        </div>

                        <div class="ed-inv-info">
                          <p class="ed-inv-info-title">{{ __('translate.Service Information') }}</p>
                          <table>
                            <tr><td>{{ __('translate.Title') }}:</td><td>{{ $booking->service->title ?? '—' }}</td></tr>
                            <tr><td>{{ __('translate.Location') }}:</td><td>{{ $booking->service->location ?? '—' }}</td></tr>
                            @if (!empty($booking->pickup_point_id))
                                <tr><td>{{ __('translate.Pickup Point') }}:</td><td>{{ $booking->pickup_point_name ?? 'Selected' }}</td></tr>
                            @endif
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

                                    {{-- Pickup Point Charges --}}
                                    @if (!empty($booking->pickup_point_id) && (float)($booking->pickup_charge ?? 0) > 0)
                                      <tr>
                                        <td>{{ __('translate.Pickup Point') }}: {{ $booking->pickup_point_name ?? 'Pickup Service' }}</td>
                                        <td class="text-end">{{ currency((float)$booking->pickup_charge) }}</td>
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

                      {{-- ===== Admin notes / cancellation reason ===== --}}
                      @if ($booking->admin_notes)
                        <div class="row mb-4 mt-3">
                          <div class="col-md-12">
                            <h6 class="text-muted">{{ __('translate.Admin note for you') }}</h6>
                            <p>{{ $booking->admin_notes }}</p>
                          </div>
                        </div>
                      @endif

                      @if ($booking->cancellation_reason)
                        <div class="row mb-4">
                          <div class="col-md-12">
                            <h6 class="text-muted">{{ __('translate.Cancellation reason') }}</h6>
                            <p>{{ $booking->cancellation_reason }}</p>
                          </div>
                        </div>
                      @endif

                      {{-- ===== ACTION BAR @ END: Left (Back/Cancel) | Right (Invoice) ===== --}}
                      <hr class="mt-2">
                      <div class="page-footer-actions">
                        <div class="left-actions">
                          <a class="btn btn-ghost btn-booking" href="{{ route('user.bookings.index') }}">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('translate.Back to Bookings') }}
                          </a>
                          @if (in_array($booking->booking_status, ['pending','confirmed','success']))
                            <button type="button"
                                    class="btn btn-outline-danger btn-booking"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelBookingModal">
                              <i class="bi bi-x-circle me-1"></i>{{ __('translate.Cancel Booking') }}
                            </button>
                          @endif
                        </div>

                        <div class="right-actions">
                          @if ($canShowInvoice)
                            <a href="{{ route('user.bookings.invoice', $booking->id) }}"
                               target="_blank" class="btn btn-invoice btn-booking">
                              <i class="fa fa-file-invoice me-1"></i>{{ __('translate.View Invoice') }}
                            </a>
                            <a href="{{ route('user.bookings.invoice.download', $booking->id) }}"
                               class="btn btn-invoice btn-booking">
                              <i class="fa fa-download me-1"></i>{{ __('translate.Download Invoice') }}
                            </a>
                          @endif
                        </div>
                      </div>

                    </div> {{-- /ed-invoice-page --}}
                  </div>
                </div>
              </div>
            </div>

            {{-- ===== Cancel Booking Modal (USER) ===== --}}
            @if (in_array($booking->booking_status, ['pending','confirmed','success']))
              <div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="{{ route('user.bookings.cancel', ['id' => $booking->id]) }}" method="POST">
                      @csrf
                      <div class="modal-header">
                        <h5 class="modal-title" id="cancelBookingModalLabel">{{ __('translate.Cancel Booking') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p class="text-danger">{{ __('translate.Are you sure you want to cancel this booking?') }}</p>
                        <div class="mb-3">
                          <label for="cancellation_reason" class="form-label">{{ __('translate.Reason for Cancellation') }}</label>
                          <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('translate.Cancel Booking') }}</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            @endif

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
