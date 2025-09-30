@php
    use Modules\TourBooking\App\Models\ExtraCharge;

    // === Normalize booking JSON fields (age & extras) ===
    $ageQuantities = is_array($booking->age_quantities)
        ? $booking->age_quantities
        : (json_decode($booking->age_quantities ?? '[]', true) ?: []);

    $ageConfig = is_array($booking->age_config)
        ? $booking->age_config
        : (json_decode($booking->age_config ?? '[]', true) ?: []);

    $labelFor = function ($key) use ($ageConfig) {
        return $ageConfig[$key]['label'] ?? ucfirst(str_replace('_', ' ', (string) $key));
    };
    $unitPriceFor = function ($key) use ($ageConfig) {
        $v = $ageConfig[$key]['price'] ?? null;
        return is_numeric($v) ? (float) $v : null;
    };

    $ageRows = collect($ageQuantities)
        ->filter(fn ($q) => (int) $q > 0)
        ->map(function ($qty, $key) use ($labelFor, $unitPriceFor) {
            $price = $unitPriceFor($key);
            return [
                'key'        => $key,
                'label'      => $labelFor($key),
                'qty'        => (int) $qty,
                'unit_price' => $price,
                'line_total' => $price !== null ? ((float) $price) * ((int) $qty) : null,
            ];
        })
        ->values();

    // Extras: acceptă fie [id => qty], fie [id, id, ...]
    $extraRaw = is_array($booking->extra_services)
        ? $booking->extra_services
        : (json_decode($booking->extra_services ?? '[]', true) ?: []);

    $extrasNorm = [];
    if ($extraRaw) {
        // dacă e listă simplă, transformăm în map id => 1
        if (array_is_list($extraRaw)) {
            foreach ($extraRaw as $id) {
                $id = (int) $id;
                if ($id) $extrasNorm[$id] = ($extrasNorm[$id] ?? 0) + 1;
            }
        } else {
            foreach ($extraRaw as $id => $qty) {
                $id = (int) $id; $q = (int) $qty;
                if ($id && $q > 0) $extrasNorm[$id] = $q;
            }
        }
    }

    $extras = collect();
    $extrasTotal = 0;
    if (!empty($extrasNorm)) {
        $models = ExtraCharge::whereIn('id', array_keys($extrasNorm))->get();
        $extras = $models->map(function ($m) use ($extrasNorm, &$extrasTotal) {
            $qty = (int) ($extrasNorm[$m->id] ?? 1);
            $line = $m->price * $qty;
            $extrasTotal += $line;
            return [
                'id'    => $m->id,
                'name'  => $m->name,
                'price' => (float) $m->price,
                'qty'   => $qty,
                'total' => $line,
            ];
        });
    }

    // Totale sigure cu fallback
    $subtotal = $booking->subtotal ?? null;
    $discount = $booking->discount_amount ?? 0;
    $tax      = $booking->tax_amount ?? 0;
    $total    = $booking->total ?? (($subtotal ?? 0) - $discount + $tax);
    $paid     = $booking->paid_amount ?? 0;
    $due      = $booking->due_amount ?? max(0, $total - $paid);

    // Date utile
    $service = $booking->service;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('translate.Invoice') }} #{{ $booking->booking_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* ===== Basic reset ===== */
        *{box-sizing:border-box}
        body{
            margin:0;padding:24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
            color:#111; font-size:14px; line-height:1.45;
        }
        .container{max-width:900px;margin:0 auto;}
        h1,h2,h3,h4,h5{margin:0 0 8px 0}
        small{color:#6b7280}
        .muted{color:#6b7280}
        .right{text-align:right}
        .center{text-align:center}
        .mt-4{margin-top:16px}.mt-6{margin-top:24px}.mt-8{margin-top:32px}
        .mb-0{margin-bottom:0}.mb-2{margin-bottom:8px}.mb-3{margin-bottom:12px}.mb-4{margin-bottom:16px}.mb-6{margin-bottom:24px}
        .p-4{padding:16px}.p-6{padding:24px}
        .row{display:flex;flex-wrap:wrap;margin-left:-12px;margin-right:-12px}
        .col{padding-left:12px;padding-right:12px}
        .col-6{width:50%}.col-4{width:33.333%}.col-8{width:66.666%}.col-12{width:100%}

        .card{border:1px solid #e5e7eb;border-radius:8px}
        .header{display:flex;justify-content:space-between;align-items:flex-start}
        .brand h2{font-weight:700}
        .badge{display:inline-block;border-radius:999px;padding:2px 10px;font-size:12px;line-height:18px}
        .bg-success{background:#e8f5e9;color:#1b5e20}
        .bg-warning{background:#fff7e6;color:#a86008}
        .bg-danger{background:#fdecea;color:#b71c1c}
        .bg-info{background:#eef6ff;color:#0842a8}

        table{width:100%;border-collapse:collapse}
        th,td{padding:10px;border-bottom:1px solid #e5e7eb;vertical-align:top}
        thead th{background:#f8fafc;text-align:left;font-weight:600}
        tfoot th, tfoot td{border-top:2px solid #e5e7eb;border-bottom:none}
        .table-sm th, .table-sm td{padding:8px}
        .totals td{padding:6px 10px}
        .totals .label{text-align:right;color:#374151}
        .totals .value{text-align:right;font-weight:600}

        .hr{height:1px;background:#e5e7eb;border:0;margin:16px 0}

        /* Print styles */
        @media print{
            body{padding:0}
            .no-print{display:none !important}
            .card{border:none}
            a{color:inherit;text-decoration:none}
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header mb-4">
        <div class="brand">
            <h2>{{ config('app.name') }}</h2>
            <div class="muted">{{ $service->serviceType->name ?? '' }}</div>
        </div>
        <div class="right">
            <h3>{{ __('translate.Invoice') }}</h3>
            <div>#{{ $booking->booking_code }}</div>
            <div class="muted">{{ __('translate.Date') }}: {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <div class="card p-6 mb-4">
        <div class="row">
            <div class="col col-6">
                <h4 class="mb-2">{{ __('translate.Billed To') }}</h4>
                <div><strong>{{ $booking->customer_name }}</strong></div>
                @if($booking->customer_email)<div class="muted">{{ $booking->customer_email }}</div>@endif
                @if($booking->customer_phone)<div class="muted">{{ $booking->customer_phone }}</div>@endif
                @if($booking->customer_address)<div class="muted">{{ $booking->customer_address }}</div>@endif
            </div>
            <div class="col col-6 right">
                <h4 class="mb-2">{{ __('translate.Booking Information') }}</h4>
                <div class="mb-1">
                    <strong>{{ __('translate.Booking Status') }}:</strong>
                    @php
                        $bs = $booking->booking_status;
                        $badgeClass = $bs==='confirmed'?'bg-success':($bs==='pending'?'bg-warning':($bs==='cancelled'?'bg-danger':'bg-info'));
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($bs) }}</span>
                </div>
                <div class="mb-1">
                    <strong>{{ __('translate.Payment Status') }}:</strong>
                    @php
                        $ps = $booking->payment_status;
                        $pBadge = $ps==='completed'?'bg-success':($ps==='pending'?'bg-warning':'bg-info');
                    @endphp
                    <span class="badge {{ $pBadge }}">{{ ucfirst($ps) }}</span>
                </div>
                <div><strong>{{ __('translate.Payment Method') }}:</strong> {{ ucfirst($booking->payment_method) }}</div>
            </div>
        </div>

        <div class="hr"></div>

        <div class="row">
            <div class="col col-6">
                <h4 class="mb-2">{{ __('translate.Service Information') }}</h4>
                <div><strong>{{ $service->title }}</strong></div>
                @if($service->location)<div class="muted">{{ $service->location }}</div>@endif
            </div>
            <div class="col col-6 right">
                <h4 class="mb-2">{{ __('translate.Dates') }}</h4>
                <div><strong>{{ __('translate.Check-in Date') }}:</strong> {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</div>
                @if($booking->check_out_date)
                    <div><strong>{{ __('translate.Check-out Date') }}:</strong> {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</div>
                @endif
                @if($booking->check_in_time)
                    <div><strong>{{ __('translate.Check-in Time') }}:</strong> {{ $booking->check_in_time }}</div>
                @endif
                @if($booking->check_out_time)
                    <div><strong>{{ __('translate.Check-out Time') }}:</strong> {{ $booking->check_out_time }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tickets table (age categories if available, else fallback) --}}
    <div class="card p-6 mb-4">
        <h4 class="mb-3">{{ __('translate.Tickets Summary') }}</h4>

        @if($ageRows->isNotEmpty())
            @php $ageLinesTotal = 0; @endphp
            <table class="table-sm">
                <thead>
                <tr>
                    <th>{{ __('translate.Category') }}</th>
                    <th class="right">{{ __('translate.Quantity') }}</th>
                    <th class="right">{{ __('translate.Unit Price') }}</th>
                    <th class="right">{{ __('translate.Total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ageRows as $row)
                    @php
                        $unit = $row['unit_price'];
                        $line = $row['line_total'];
                        if($line !== null) $ageLinesTotal += $line;
                    @endphp
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="right">{{ $row['qty'] }}</td>
                        <td class="right">{{ $unit !== null ? currencyConverter($unit) : '—' }}</td>
                        <td class="right">{{ $line !== null ? currencyConverter($line) : '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
                @if($ageLinesTotal > 0)
                    <tfoot>
                    <tr>
                        <th colspan="3" class="right">{{ __('translate.Subtotal') }}</th>
                        <th class="right">{{ currencyConverter($ageLinesTotal) }}</th>
                    </tr>
                    </tfoot>
                @endif
            </table>
        @else
            <table class="table-sm">
                <thead>
                <tr>
                    <th>{{ __('translate.Category') }}</th>
                    <th class="right">{{ __('translate.Quantity') }}</th>
                    <th class="right">{{ __('translate.Unit Price') }}</th>
                    <th class="right">{{ __('translate.Total') }}</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $adultUnit = $booking->adult_price ?? $service->price_per_person ?? null;
                    $childUnit = $booking->child_price ?? $service->child_price ?? null;
                    $infUnit   = $booking->infant_price ?? $service->infant_price ?? null;
                    $adultLine = ($adultUnit !== null) ? $adultUnit * (int)$booking->adults : null;
                    $childLine = ($childUnit !== null) ? $childUnit * (int)$booking->children : null;
                    $infLine   = ($infUnit   !== null) ? $infUnit   * (int)$booking->infants  : null;
                @endphp
                <tr>
                    <td>{{ __('translate.Adults') }}</td>
                    <td class="right">{{ (int)$booking->adults }}</td>
                    <td class="right">{{ $adultUnit !== null ? currencyConverter($adultUnit) : '—' }}</td>
                    <td class="right">{{ $adultLine !== null ? currencyConverter($adultLine) : '—' }}</td>
                </tr>
                @if((int)$booking->children > 0)
                    <tr>
                        <td>{{ __('translate.Children') }}</td>
                        <td class="right">{{ (int)$booking->children }}</td>
                        <td class="right">{{ $childUnit !== null ? currencyConverter($childUnit) : '—' }}</td>
                        <td class="right">{{ $childLine !== null ? currencyConverter($childLine) : '—' }}</td>
                    </tr>
                @endif
                @if((int)$booking->infants > 0)
                    <tr>
                        <td>{{ __('translate.Infants') }}</td>
                        <td class="right">{{ (int)$booking->infants }}</td>
                        <td class="right">{{ $infUnit !== null ? currencyConverter($infUnit) : '—' }}</td>
                        <td class="right">{{ $infLine !== null ? currencyConverter($infLine) : '—' }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        @endif
    </div>

    {{-- Extras (if any) --}}
    @if($extras->isNotEmpty())
        <div class="card p-6 mb-4">
            <h4 class="mb-3">{{ __('translate.Additional Services') }}</h4>
            <table class="table-sm">
                <thead>
                <tr>
                    <th>{{ __('translate.Service') }}</th>
                    <th class="right">{{ __('translate.Quantity') }}</th>
                    <th class="right">{{ __('translate.Unit Price') }}</th>
                    <th class="right">{{ __('translate.Total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($extras as $ex)
                    <tr>
                        <td>{{ $ex['name'] }}</td>
                        <td class="right">{{ $ex['qty'] }}</td>
                        <td class="right">{{ currencyConverter($ex['price']) }}</td>
                        <td class="right">{{ currencyConverter($ex['total']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3" class="right">{{ __('translate.Subtotal Extras') }}</th>
                    <th class="right">{{ currencyConverter($extrasTotal) }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    @endif

    {{-- Totals --}}
    <div class="card p-6 mb-4">
        <h4 class="mb-3">{{ __('translate.Summary') }}</h4>
        <table class="totals">
            <tbody>
            @if(!is_null($subtotal))
                <tr>
                    <td class="label" style="width:70%">{{ __('translate.Subtotal') }}</td>
                    <td class="value" style="width:30%">{{ currencyConverter($subtotal) }}</td>
                </tr>
            @endif
            @if(($discount ?? 0) > 0)
                <tr>
                    <td class="label">{{ __('translate.Discount') }}</td>
                    <td class="value">- {{ currencyConverter($discount) }}</td>
                </tr>
            @endif
            @if(($tax ?? 0) > 0)
                <tr>
                    <td class="label">{{ __('translate.Tax') }}</td>
                    <td class="value">{{ currencyConverter($tax) }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">{{ __('translate.Total Amount') }}</td>
                <td class="value">{{ currencyConverter($total) }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('translate.Paid Amount') }}</td>
                <td class="value">{{ currencyConverter($paid) }}</td>
            </tr>
            @if(($due ?? 0) > 0)
                <tr>
                    <td class="label">{{ __('translate.Due Amount') }}</td>
                    <td class="value">{{ currencyConverter($due) }}</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    @if($booking->customer_notes)
        <div class="card p-6 mb-4">
            <h4 class="mb-2">{{ __('translate.Notes') }}</h4>
            <div>{{ $booking->customer_notes }}</div>
        </div>
    @endif

    <div class="center muted mt-6 no-print">
        <small>{{ __('translate.Thank you for your booking!') }}</small>
    </div>
</div>
</body>
</html>
