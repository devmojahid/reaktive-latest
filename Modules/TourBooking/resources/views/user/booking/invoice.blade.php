<?php
/** @var object|null $s */
$s = Cache::get('setting'); // vine din GlobalSettingController::set_cache_setting()
?>

{{-- Modules/TourBooking/resources/views/user/booking/invoice.blade.php --}}
@php
    use Carbon\Carbon;

    // helper sigur pentru dată
    $fmtDate = function ($d, $f='d M Y') {
        try { return $d ? Carbon::parse($d)->format($f) : '—'; } catch (\Throwable $e) { return '—'; }
    };

    // helper sigur pentru currency (dacă nu există global currency())
    if (!function_exists('__fmt_currency')) {
        function __fmt_currency($n) {
            return function_exists('currency') ? currency((float)$n) : number_format((float)$n, 2).' €';
        }
    }

    // ==== Billed From (supplier) din setări ====
    // folosim cheile: invoice_company_name, *_address_line1, *_address_line2, *_zip, *_city, *_state, *_country,
    // *_vat_id, *_reg_no, *_eori, *_iban, *_bank_name, *_email, *_phone
    $supplier = (object)[
        'name'           => $s->invoice_company_name       ?? ($general_setting->site_name ?? config('app.name', '—')),
        'address_line1'  => $s->invoice_company_address_line1 ?? null,
        'address_line2'  => $s->invoice_company_address_line2 ?? null,
        'zip'            => $s->invoice_company_zip        ?? null,
        'city'           => $s->invoice_company_city       ?? null,
        'state'          => $s->invoice_company_state      ?? null,
        'country'        => $s->invoice_company_country    ?? null,
        'vat'            => $s->invoice_company_vat_id     ?? null,
        'reg'            => $s->invoice_company_reg_no     ?? null,
        'eori'           => $s->invoice_company_eori       ?? null,
        'iban'           => $s->invoice_company_iban       ?? null,
        'bank'           => $s->invoice_company_bank_name  ?? null,
        'email'          => $s->invoice_company_email      ?? ($general_setting->email ?? null),
        'phone'          => $s->invoice_company_phone      ?? ($general_setting->phone ?? null),
        'website'        => $general_setting->site_url      ?? null,
    ];
@endphp

@if(!($forPdf ?? false))
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $booking->booking_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
@endif

<style>
    *{ box-sizing:border-box; }
    body{ margin:0; font-family:ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#0f172a; }
    .wrap{ max-width:1000px; margin:24px auto; padding:0 16px; }
    .card{ border:1px solid #eef1f5; border-radius:14px; background:#fff; }
    .pad{ padding:18px 20px; }
    .row{ display:flex; flex-wrap:wrap; gap:16px; }
    .muted{ color:#64748b; }
    h1{ font-size:20px; margin:0 0 6px 0; }
    h2{ font-size:16px; margin:0 0 10px 0; }
    table{ width:100%; border-collapse:collapse; }
    th, td{ padding:10px 8px; text-align:left; }
    thead th{ background:#f8fafc; border-bottom:1px solid #e2e8f0; font-weight:700; }
    tfoot th, tfoot td{ background:#f8fafc; border-top:1px solid #e2e8f0; font-weight:700; }
    .right{ text-align:right; }
    .badge{ display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .bg-success{ background:#e8f8ee; color:#166534; }
    .bg-warning{ background:#fff7ed; color:#9a3412; }
    .bg-danger{ background:#fee2e2; color:#991b1b; }
    .brand{ color:#ff4200; }
    .hr{ height:1px; background:#e2e8f0; margin:14px 0; }
    .chips span{ display:inline-block; border:1px solid #e2e8f0; border-radius:999px; padding:4px 10px; margin:2px 6px 2px 0; font-weight:600; }
    .top-actions{ display:flex; gap:10px; justify-content:flex-end; margin-bottom:12px;}
    .btn{ display:inline-block; padding:8px 14px; border-radius:10px; text-decoration:none; font-weight:700; }
    .btn-outline{ border:1px solid #e2e8f0; color:#0f172a; }
    .btn-brand{ background:#ff4200; color:#fff; }
    .logo{ height:32px; width:auto; border-radius:6px; }

    /* ===== 3 coloane: Web vs PDF ===== */
    @if(!($forPdf ?? false))
      .three-col{ display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:16px; }
      .col-stack{ display:flex; flex-direction:column; gap:16px; }
      @media (max-width: 960px){ .three-col{ grid-template-columns:1fr; } }
    @else
      /* Dompdf nu suportă CSS Grid => tabel simplu cu 3 celule */
      .three-col-pdf{ width:100%; border-collapse:separate; border-spacing:16px 0; table-layout:fixed; }
      .three-col-pdf td{ vertical-align:top; width:33.33%; }
      .col-stack{ display:block; }
      .col-stack .card{ margin-bottom:16px; }
    @endif
</style>

@if(!($forPdf ?? false))
</head>
<body>
@endif

<div class="wrap">

    @if(!($forPdf ?? false))
    {{-- acțiuni on-screen (nu apar în PDF) --}}
    <div class="top-actions">
        <a href="{{ route('user.bookings.details', $booking->id) }}" class="btn btn-outline">← Back</a>
        <a href="{{ route('user.bookings.invoice.download', $booking->id) }}" class="btn btn-brand">Download PDF</a>
    </div>
    @endif

    {{-- Header --}}
    <div class="row" style="align-items:center; margin-bottom:8px;">
        <div style="flex:1 1 auto">
            <h1>Invoice <span class="brand">#{{ $booking->booking_code }}</span></h1>
            <div class="muted">Date: {{ $fmtDate($booking->created_at) }}</div>
        </div>
        <div>
            @isset($general_setting->logo)
                <img class="logo" src="{{ asset($general_setting->logo) }}" alt="logo">
            @endisset
        </div>
    </div>

    {{-- ===== 3 COL LAYOUT cu ordinea cerută ===== --}}
    @if(!($forPdf ?? false))
      <div class="three-col">
        {{-- Col 1: Billed From --}}
        <div class="col-stack">
            <div class="card pad">
                <h2>Billed From</h2>
                <div class="muted">Company</div>
                <div><strong>{{ $supplier->name ?: '—' }}</strong></div>

                <div class="muted" style="margin-top:8px;">Address</div>
                <div>
                    @php $hasAddr = false; @endphp
                    @if(!empty($supplier->address_line1)) {{ $supplier->address_line1 }}<br>@php $hasAddr = true; @endphp @endif
                    @if(!empty($supplier->address_line2)) {{ $supplier->address_line2 }}<br>@php $hasAddr = true; @endphp @endif
                    @php
                        $cityLine  = trim(collect([$supplier->zip, $supplier->city])->filter()->implode(' '));
                        $stateLine = trim(collect([$supplier->state, $supplier->country])->filter()->implode(', '));
                    @endphp
                    @if($cityLine) {{ $cityLine }}<br>@php $hasAddr = true; @endphp @endif
                    @if($stateLine) {{ $stateLine }}@php $hasAddr = true; @endphp @endif
                    @if(!$hasAddr) — @endif
                </div>

                <div class="muted" style="margin-top:8px;">VAT ID</div>
                <div>{{ $supplier->vat ?? '—' }}</div>

                <div class="muted" style="margin-top:8px;">Registration No.</div>
                <div>{{ $supplier->reg ?? '—' }}</div>

                <div class="muted" style="margin-top:8px;">EORI</div>
                <div>{{ $supplier->eori ?? '—' }}</div>

                <div class="muted" style="margin-top:8px;">IBAN / Bank</div>
                <div>
                    {{ $supplier->iban ?? '—' }}
                    @if(!empty($supplier->bank)) <span class="muted"> · {{ $supplier->bank }}</span> @endif
                </div>

                <div class="muted" style="margin-top:8px;">Email / Phone</div>
                <div>
                    {{ $supplier->email ?? '—' }}
                    @if(!empty($supplier->phone)) <span class="muted"> · {{ $supplier->phone }}</span> @endif
                </div>

                @if(!empty($supplier->website))
                    <div class="muted" style="margin-top:8px;">Website</div>
                    <div>{{ $supplier->website }}</div>
                @endif
            </div>
        </div>

        {{-- Col 2: Summary + Booking Info --}}
        <div class="col-stack">
            <div class="card pad">
                <h2>Summary</h2>
                <div class="row" style="gap:10px;">
                    <div>
                        <div class="muted">Booking status</div>
                        @php
                            $b = strtolower((string)$booking->booking_status);
                            $bcls = in_array($b,['confirmed','success','completed']) ? 'bg-success' : ($b==='pending'?'bg-warning': 'bg-danger');
                        @endphp
                        <span class="badge {{ $bcls }}">{{ ucfirst($booking->booking_status) }}</span>
                    </div>
                    <div>
                        <div class="muted">Payment status</div>
                        @php
                            $p = strtolower((string)$booking->payment_status);
                            $pcls = in_array($p,['confirmed','success','completed']) ? 'bg-success' : ($p==='pending'?'bg-warning': 'bg-danger');
                        @endphp
                        <span class="badge {{ $pcls }}">{{ ucfirst($booking->payment_status) }}</span>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="row" style="gap:18px;">
                    <div><div class="muted">Total</div><strong>{{ __fmt_currency($booking->total) }}</strong></div>
                    <div><div class="muted">Paid</div><strong>{{ __fmt_currency($booking->paid_amount) }}</strong></div>
                    <div><div class="muted">Due</div><strong>{{ (float)$booking->due_amount > 0 ? __fmt_currency($booking->due_amount) : '—' }}</strong></div>
                </div>
            </div>

            <div class="card pad">
                <h2>Booking Info</h2>
                <div class="muted">Invoice No</div>
                <div>#{{ $booking->booking_code }}</div>

                <div class="muted" style="margin-top:8px;">Payment Method</div>
                <div>{{ $booking->payment_method ? ucfirst($booking->payment_method) : '—' }}</div>
            </div>
        </div>

        {{-- Col 3: Billed To + Service Information --}}
        <div class="col-stack">
            <div class="card pad">
                <h2>Billed To</h2>
                <div class="muted">Name</div>
                <div><strong>{{ $booking->customer_name ?? '—' }}</strong></div>
                <div class="muted" style="margin-top:8px;">Email</div>
                <div>{{ $booking->customer_email ?? '—' }}</div>
                <div class="muted" style="margin-top:8px;">Phone</div>
                <div>{{ $booking->customer_phone ?? '—' }}</div>
                <div class="muted" style="margin-top:8px;">Address</div>
                <div>{{ $booking->customer_address ?? '—' }}</div>
            </div>

            <div class="card pad">
                <h2>Service Information</h2>
                <div class="row" style="gap:30px;">
                    <div style="min-width:220px;">
                        <div class="muted">Title</div>
                        <div><strong>{{ $booking->service->title ?? '—' }}</strong></div>

                        <div class="muted" style="margin-top:8px;">Location</div>
                        <div>{{ $booking->service->location ?? '—' }}</div>

                        @if (!empty($booking->pickup_point_id))
                        <div class="muted" style="margin-top:8px;">Pickup Point</div>
                        <div>{{ $booking->pickup_point_name ?? 'Selected' }}</div>
                        @endif

                        <div class="muted" style="margin-top:8px;">Guests</div>
                        <div>Adults: {{ (int)$booking->adults }} &nbsp;·&nbsp; Children: {{ (int)$booking->children }}</div>
                    </div>

                    @if(!empty($ageBreakdown ?? []))
                        <div style="flex:1 1 auto;">
                            <div class="muted">Guests breakdown</div>
                            <div class="chips" style="margin-top:6px;">
                                @foreach($ageBreakdown as $row)
                                    <span>{{ $row['label'] ?? 'Category' }} · {{ (int)($row['qty'] ?? 0) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
      </div>
    @else
      {{-- ===== PDF fallback (tabel 3 coloane) ===== --}}
      <table class="three-col-pdf">
        <tr>
          <td>
            <div class="col-stack">
              {{-- Billed From --}}
              <div class="card pad">
                  <h2>Billed From</h2>
                  <div class="muted">Company</div>
                  <div><strong>{{ $supplier->name ?: '—' }}</strong></div>

                  <div class="muted" style="margin-top:8px;">Address</div>
                  <div>
                      @php $hasAddr = false; @endphp
                      @if(!empty($supplier->address_line1)) {{ $supplier->address_line1 }}<br>@php $hasAddr = true; @endphp @endif
                      @if(!empty($supplier->address_line2)) {{ $supplier->address_line2 }}<br>@php $hasAddr = true; @endphp @endif
                      @php
                          $cityLine  = trim(collect([$supplier->zip, $supplier->city])->filter()->implode(' '));
                          $stateLine = trim(collect([$supplier->state, $supplier->country])->filter()->implode(', '));
                      @endphp
                      @if($cityLine) {{ $cityLine }}<br>@php $hasAddr = true; @endphp @endif
                      @if($stateLine) {{ $stateLine }}@php $hasAddr = true; @endphp @endif
                      @if(!$hasAddr) — @endif
                  </div>

                  <div class="muted" style="margin-top:8px;">VAT ID</div>
                  <div>{{ $supplier->vat ?? '—' }}</div>

                  <div class="muted" style="margin-top:8px;">Registration No.</div>
                  <div>{{ $supplier->reg ?? '—' }}</div>

                  <div class="muted" style="margin-top:8px;">EORI</div>
                  <div>{{ $supplier->eori ?? '—' }}</div>

                  <div class="muted" style="margin-top:8px;">IBAN / Bank</div>
                  <div>
                      {{ $supplier->iban ?? '—' }}
                      @if(!empty($supplier->bank)) <span class="muted"> · {{ $supplier->bank }}</span> @endif
                  </div>

                  <div class="muted" style="margin-top:8px;">Email / Phone</div>
                  <div>
                      {{ $supplier->email ?? '—' }}
                      @if(!empty($supplier->phone)) <span class="muted"> · {{ $supplier->phone }}</span> @endif
                  </div>

                  @if(!empty($supplier->website))
                      <div class="muted" style="margin-top:8px;">Website</div>
                      <div>{{ $supplier->website }}</div>
                  @endif
              </div>
            </div>
          </td>

          <td>
            <div class="col-stack">
              {{-- Summary --}}
              <div class="card pad">
                  <h2>Summary</h2>
                  <div class="row" style="gap:10px;">
                      <div>
                          <div class="muted">Booking status</div>
                          @php
                              $b = strtolower((string)$booking->booking_status);
                              $bcls = in_array($b,['confirmed','success','completed']) ? 'bg-success' : ($b==='pending'?'bg-warning': 'bg-danger');
                          @endphp
                          <span class="badge {{ $bcls }}">{{ ucfirst($booking->booking_status) }}</span>
                      </div>
                      <div>
                          <div class="muted">Payment status</div>
                          @php
                              $p = strtolower((string)$booking->payment_status);
                              $pcls = in_array($p,['confirmed','success','completed']) ? 'bg-success' : ($p==='pending'?'bg-warning': 'bg-danger');
                          @endphp
                          <span class="badge {{ $pcls }}">{{ ucfirst($booking->payment_status) }}</span>
                      </div>
                  </div>
                  <div class="hr"></div>
                  <div class="row" style="gap:18px;">
                      <div><div class="muted">Total</div><strong>{{ __fmt_currency($booking->total) }}</strong></div>
                      <div><div class="muted">Paid</div><strong>{{ __fmt_currency($booking->paid_amount) }}</strong></div>
                      <div><div class="muted">Due</div><strong>{{ (float)$booking->due_amount > 0 ? __fmt_currency($booking->due_amount) : '—' }}</strong></div>
                  </div>
              </div>

              {{-- Booking Info --}}
              <div class="card pad">
                  <h2>Booking Info</h2>
                  <div class="muted">Invoice No</div>
                  <div>#{{ $booking->booking_code }}</div>

                  <div class="muted" style="margin-top:8px;">Payment Method</div>
                  <div>{{ $booking->payment_method ? ucfirst($booking->payment_method) : '—' }}</div>
              </div>
            </div>
          </td>

          <td>
            <div class="col-stack">
              {{-- Billed To --}}
              <div class="card pad">
                  <h2>Billed To</h2>
                  <div class="muted">Name</div>
                  <div><strong>{{ $booking->customer_name ?? '—' }}</strong></div>
                  <div class="muted" style="margin-top:8px;">Email</div>
                  <div>{{ $booking->customer_email ?? '—' }}</div>
                  <div class="muted" style="margin-top:8px;">Phone</div>
                  <div>{{ $booking->customer_phone ?? '—' }}</div>
                  <div class="muted" style="margin-top:8px;">Address</div>
                  <div>{{ $booking->customer_address ?? '—' }}</div>
              </div>

              {{-- Service Information --}}
              <div class="card pad">
                  <h2>Service Information</h2>
                  <div class="row" style="gap:30px;">
                      <div style="min-width:220px;">
                          <div class="muted">Title</div>
                          <div><strong>{{ $booking->service->title ?? '—' }}</strong></div>

                          <div class="muted" style="margin-top:8px;">Location</div>
                          <div>{{ $booking->service->location ?? '—' }}</div>

                          @if (!empty($booking->pickup_point_id))
                          <div class="muted" style="margin-top:8px;">Pickup Point</div>
                          <div>{{ $booking->pickup_point_name ?? 'Selected' }}</div>
                          @endif

                          <div class="muted" style="margin-top:8px;">Guests</div>
                          <div>Adults: {{ (int)$booking->adults }} &nbsp;·&nbsp; Children: {{ (int)$booking->children }}</div>
                      </div>

                      @if(!empty($ageBreakdown ?? []))
                          <div style="flex:1 1 auto;">
                              <div class="muted">Guests breakdown</div>
                              <div class="chips" style="margin-top:6px;">
                                  @foreach($ageBreakdown as $row)
                                      <span>{{ $row['label'] ?? 'Category' }} · {{ (int)($row['qty'] ?? 0) }}</span>
                                  @endforeach
                              </div>
                          </div>
                      @endif
                  </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
    @endif

    {{-- Price table (rămâne sub coloane) --}}
    <div class="card pad" style="margin-top:16px;">
        <h2>Price Details</h2>
        <table>
            <thead>
            <tr>
                <th>Description</th>
                <th class="right">Amount</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($ageBreakdown ?? []))
                @foreach($ageBreakdown as $row)
                    @php
                        $unit = (float)($row['price'] ?? 0);
                        $qty  = (int)($row['qty'] ?? 0);
                        $line = (float)($row['line'] ?? ($unit * $qty));
                    @endphp
                    <tr>
                        <td>{{ $row['label'] ?? 'Category' }} ({{ number_format($unit,2) }} × {{ $qty }})</td>
                        <td class="right">{{ __fmt_currency($line) }}</td>
                    </tr>
                @endforeach
            @else
                @if ($booking->is_per_person == 1)
                    <tr>
                        <td>Adult Price ({{ number_format((float)$booking->adult_price,2) }} × {{ (int)$booking->adults }} Adults)</td>
                        <td class="right">{{ __fmt_currency(((float)$booking->adult_price) * ((int)$booking->adults)) }}</td>
                    </tr>
                    @if((int)$booking->children > 0)
                        <tr>
                            <td>Child Price ({{ number_format((float)$booking->child_price,2) }} × {{ (int)$booking->children }} Children)</td>
                            <td class="right">{{ __fmt_currency(((float)$booking->child_price) * ((int)$booking->children)) }}</td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td>Service Price</td>
                        <td class="right">{{ __fmt_currency((float)$booking->service_price) }}</td>
                    </tr>
                @endif
            @endif

            @if ((float)($booking->extra_charges ?? 0) != 0)
                <tr>
                    <td>Extra charges</td>
                    <td class="right">{{ __fmt_currency((float)$booking->extra_charges) }}</td>
                </tr>
            @endif

            {{-- Pickup Point Charges --}}
            @if (!empty($booking->pickup_point_id) && (float)($booking->pickup_charge ?? 0) > 0)
                <tr>
                    <td>Pickup Point: {{ $booking->pickup_point_name ?? 'Pickup Service' }}</td>
                    <td class="right">{{ __fmt_currency((float)$booking->pickup_charge) }}</td>
                </tr>
            @endif

            @if (!empty($booking->tax) && (float)$booking->tax > 0)
                <tr>
                    <td>Tax @if(!empty($booking->tax_percentage)) ({{ (float)$booking->tax_percentage }}%) @endif</td>
                    <td class="right">{{ __fmt_currency((float)$booking->tax) }}</td>
                </tr>
            @endif
            </tbody>
            <tfoot>
            <tr>
                <th>Total</th>
                <th class="right">{{ __fmt_currency((float)$booking->total) }}</th>
            </tr>
            </tfoot>
        </table>
    </div>

    @if(!empty($booking->admin_notes))
        <div class="card pad" style="margin-top:16px;">
            <h2>Admin Notes</h2>
            <div class="muted">{{ $booking->admin_notes }}</div>
        </div>
    @endif

</div>

@if(!($forPdf ?? false))
</body>
</html>
@endif
