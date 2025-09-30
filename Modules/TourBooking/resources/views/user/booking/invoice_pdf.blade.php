@php
  $bookingStatusClass = in_array(strtolower((string)$booking->booking_status), ['confirmed','success','completed']) ? 'OK' : strtoupper($booking->booking_status);
  $paymentStatusClass = in_array(strtolower((string)$booking->payment_status), ['success','completed','confirmed']) ? 'OK' : strtoupper($booking->payment_status);
@endphp
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice #{{ $booking->booking_code }}</title>
  <style>
    *{ box-sizing:border-box; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    body{ font-size:12px; color:#111 }
    .wrap{ padding:18px }
    .head{ display:flex; justify-content:space-between; align-items:center; margin-bottom:12px }
    .logo{ height:38px }
    h1{ font-size:18px; margin:0 }
    table{ width:100%; border-collapse:collapse }
    .grid{ display:table; width:100% }
    .col{ display:table-cell; width:50%; vertical-align:top; padding:6px 8px }
    .box{ border:1px solid #ddd; border-radius:6px; padding:8px }
    .mb{ margin-bottom:10px }
    .muted{ color:#555 }
    td{ padding:3px 0 }
    td:first-child{ width:40%; color:#555; padding-right:8px }
    .tot td{ border-top:1px solid #ddd; font-weight:bold; padding-top:6px }
    .right{ text-align:right }
    .chips{ margin-top:6px }
    .chip{ display:inline-block; border:1px solid #ddd; border-radius:999px; padding:2px 8px; margin:2px 4px 0 0; font-size:11px }
  </style>
</head>
<body>
<div class="wrap">
  <div class="head">
    <div>
      @isset($general_setting->logo)
        <img src="{{ public_path($general_setting->logo) }}" class="logo" alt="logo">
      @endisset
    </div>
    <div>
      <h1>Invoice #{{ $booking->booking_code }}</h1>
      <div class="muted">Booking: {{ ucfirst($booking->booking_status) }} / Payment: {{ ucfirst($booking->payment_status) }}</div>
    </div>
  </div>

  <div class="grid mb">
    <div class="col">
      <div class="box">
        <strong>Billed To</strong>
        <table>
          <tr><td>Name</td><td>{{ $booking->customer_name ?? '—' }}</td></tr>
          <tr><td>Phone</td><td>{{ $booking->customer_phone ?? '—' }}</td></tr>
          <tr><td>Email</td><td>{{ $booking->customer_email ?? '—' }}</td></tr>
          <tr><td>Address</td><td>{{ $booking->customer_address ?? '—' }}</td></tr>
        </table>
      </div>
    </div>
    <div class="col">
      <div class="box">
        <strong>Booking Information</strong>
        <table>
          <tr><td>Invoice No</td><td>#{{ $booking->booking_code }}</td></tr>
          <tr><td>Payment Method</td><td>{{ $booking->payment_method ? ucfirst($booking->payment_method) : '—' }}</td></tr>
          <tr><td>Check in Date</td><td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td></tr>
          @if ($booking->check_out_date)
            <tr><td>Check out Date</td><td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</td></tr>
          @endif
        </table>
      </div>
    </div>
  </div>

  <div class="grid mb">
    <div class="col">
      <div class="box">
        <strong>Service Information</strong>
        <table>
          <tr><td>Title</td><td>{{ $booking->service->title ?? '—' }}</td></tr>
          <tr><td>Location</td><td>{{ $booking->service->location ?? '—' }}</td></tr>
          <tr><td>Adults</td><td>{{ (int)$booking->adults }}</td></tr>
          <tr><td>Children</td><td>{{ (int)$booking->children }}</td></tr>
        </table>

        @if(!empty($ageBreakdown))
          <div class="chips">
            @foreach($ageBreakdown as $row)
              <span class="chip">{{ $row['label'] ?? 'Category' }} · {{ (int)($row['qty'] ?? 0) }}</span>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="col">
      <div class="box">
        <strong>Price Details</strong>
        <table>
          @if(!empty($ageBreakdown))
            @foreach($ageBreakdown as $row)
              @php
                $unit = (float)($row['price'] ?? 0);
                $qty  = (int)($row['qty'] ?? 0);
                $line = (float)($row['line'] ?? ($unit * $qty));
              @endphp
              <tr>
                <td>{{ $row['label'] ?? 'Category' }} ({{ number_format($unit,2) }} × {{ $qty }})</td>
                <td class="right">{{ currency($line) }}</td>
              </tr>
            @endforeach
          @else
            @if ($booking->is_per_person == 1)
              <tr><td>Adult Price ({{ number_format((float)$booking->adult_price,2) }} × {{ (int)$booking->adults }})</td><td class="right">{{ currency(((float)$booking->adult_price) * ((int)$booking->adults)) }}</td></tr>
              @if ((int)$booking->children > 0)
                <tr><td>Child Price ({{ number_format((float)$booking->child_price,2) }} × {{ (int)$booking->children }})</td><td class="right">{{ currency(((float)$booking->child_price) * ((int)$booking->children)) }}</td></tr>
              @endif
            @else
              <tr><td>Service Price</td><td class="right">{{ currency((float)$booking->service_price) }}</td></tr>
            @endif
          @endif

          @if ((float)($booking->extra_charges ?? 0) != 0)
            <tr><td>Extra charges</td><td class="right">{{ currency((float)$booking->extra_charges) }}</td></tr>
          @endif
          @if (!empty($booking->tax) && (float)$booking->tax > 0)
            <tr><td>Tax</td><td class="right">{{ currency((float)$booking->tax) }}</td></tr>
          @endif
          <tr class="tot"><td>Total</td><td class="right">{{ currency((float)$booking->total) }}</td></tr>
        </table>
      </div>
    </div>
  </div>

  <div class="muted">Generated on {{ now()->format('d M Y H:i') }}</div>
</div>
</body>
</html>
