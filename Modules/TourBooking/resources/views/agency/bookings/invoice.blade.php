<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('translate.Invoice') }} #{{ $booking->booking_code }}</title>
    <style>
        /* Invoice Styling */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background-color: #f9f9f9;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .invoice-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
        }

        .logo-container img {
            max-height: 60px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        .invoice-title p {
            margin: 5px 0 0;
            color: #777;
        }

        .invoice-info {
            padding: 20px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-between;
        }

        .invoice-info-section {
            flex: 1;
        }

        .invoice-info h2 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #333;
        }

        .invoice-body {
            padding: 20px;
        }

        .tour-details {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 3px;
        }

        .tour-details h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }

        .table tfoot th,
        .table tfoot td {
            border-top: 2px solid #ddd;
            font-weight: 600;
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .invoice-footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #f0f0f0;
        }

        .print-button {
            text-align: center;
            margin: 20px 0;
        }

        .print-button button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s;
            margin: 0 5px;
        }

        .print-button button:hover {
            background-color: #45a049;
        }

        .download-button {
            background-color: #2196F3 !important;
        }

        .download-button:hover {
            background-color: #0b7dda !important;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
        }

        .status-pending {
            background-color: #f0ad4e;
        }

        .status-confirmed {
            background-color: #5cb85c;
        }

        .status-cancelled {
            background-color: #d9534f;
        }

        .status-completed {
            background-color: #5bc0de;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                max-width: 100%;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-button">
        <button onclick="window.print()">{{ __('translate.Print Invoice') }}</button>
        <a href="{{ route('agency.tourbooking.bookings.download-invoice', $booking) }}">
            <button class="download-button">{{ __('translate.Download PDF') }}</button>
        </a>
    </div>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="logo-container">
                <img src="{{ asset($general_setting->logo ?? 'uploads/default.png') }}" alt="Company Logo">
                <p style="margin-top: 10px;">{{ $general_setting?->app_name }}</p>
            </div>
            <div class="invoice-title">
                <h1>{{ __('translate.INVOICE') }}</h1>
                <p>{{ __('translate.Booking Code') }}: #{{ $booking->booking_code }}</p>
                <p>{{ __('translate.Date') }}: {{ date('d M Y', strtotime($booking->created_at)) }}</p>
                <p>
                    <span class="status-badge status-{{ $booking->booking_status }}">
                        {{ __('translate.' . ucfirst($booking->booking_status)) }}
                    </span>
                </p>
            </div>
        </div>

        <div class="invoice-info">
            <div class="invoice-info-section">
                <h2>{{ __('translate.From') }}:</h2>
                <p>{{ $general_setting?->app_name }}</p>
                <p>{{ $general_setting?->contact_message_mail }}</p>
            </div>
            <div class="invoice-info-section">
                <h2>{{ __('translate.To') }}:</h2>
                <p><strong>{{ $booking->customer_name }}</strong></p>
                <p>{{ $booking->customer_email }}</p>
                <p>{{ $booking->customer_phone }}</p>
                @if ($booking->customer_address)
                    <p>{{ $booking->customer_address }}, {{ $booking->customer_city ?? '' }}</p>
                    <p>{{ $booking->customer_country ?? '' }}</p>
                @endif
            </div>
        </div>

        <div class="invoice-body">
            <div class="tour-details">
                <h3>{{ $booking->service->translation->title ?? $booking->service->title }}</h3>
                <p>{{ __('translate.Tour Type') }}:
                    {{ $booking->service->serviceType->translation->name ?? $booking->service->serviceType->name }}</p>
                <p>{{ __('translate.Location') }}: {{ $booking->service->location }}</p>
                <p>{{ __('translate.Duration') }}: {{ $booking->service->duration }}</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('translate.Description') }}</th>
                        <th>{{ __('translate.Quantity') }}</th>
                        <th>{{ __('translate.Unit Price') }}</th>
                        <th class="text-right">{{ __('translate.Amount') }}</th>
                    </tr>
                </thead>
                <tbody>

                    @if ($booking->is_per_person == 1)

                        <tr>
                            <td>{{ __('translate.Adult Price') }}</td>
                            <td>{{ $booking->adults }}</td>
                            <td>{{ currency($booking->adult_price) }}</td>
                            <td class="text-right">{{ currency($booking->adult_price * $booking->adults) }}</td>
                        </tr>

                        @if ($booking->children > 0 && $booking->child_price > 0)
                            <tr>
                                <td>{{ __('translate.Child Price') }}</td>
                                <td>{{ $booking->children }}</td>
                                <td>{{ currency($booking->child_price) }}</td>
                                <td class="text-right">{{ currency($booking->child_price * $booking->children) }}
                                </td>
                            </tr>
                        @endif

                        @if ($booking->infants > 0 && $booking->service->infant_price > 0)
                            <tr>
                                <td>{{ __('translate.Infant Price') }}</td>
                                <td>{{ $booking->infants }}</td>
                                <td>{{ currency($booking->infant_price) }}</td>
                                <td class="text-right">{{ currency($booking->infant_price * $booking->infants) }}
                                </td>
                            </tr>
                        @endif

                        @if ($booking->extra_charges > 0)
                            <tr>
                                <td>{{ __('translate.Extra Charges') }}</td>
                                <td></td>
                                <td>{{ currency($booking->extra_charges) }}</td>
                                <td class="text-right">{{ currency($booking->extra_charges) }}
                                </td>
                            </tr>
                        @endif
                    @else
                        <tr>
                            <td>{{ __('translate.Service Price') }}</td>
                            <td></td>
                            <td>{{ currency($booking->service_price) }}</td>
                            <td class="text-right">{{ currency($booking->service_price) }}</td>
                        </tr>
                    @endif

                </tbody>
                <tfoot>
                    @if ($booking->discount > 0)
                        <tr>
                            <td colspan="3">{{ __('translate.Subtotal') }}</td>
                            <td class="text-right">
                                {{ currency($booking->total_amount + $booking->discount - $booking->tax) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">{{ __('translate.Discount') }}</td>
                            <td class="text-right">-{{ currency($booking->discount) }}</td>
                        </tr>
                    @endif

                    @if ($booking->tax > 0)
                        <tr>
                            <td colspan="3">{{ __('translate.Tax') }} ({{ $booking->tax_percentage }}%)</td>
                            <td class="text-right">{{ currency($booking->tax) }}</td>
                        </tr>
                    @endif

                    <tr>
                        <th colspan="3">{{ __('translate.Total') }}</th>
                        <th class="text-right">{{ currency($booking->total) }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="payment-info">
                <h4>{{ __('translate.Payment Information') }}</h4>
                <p><strong>{{ __('translate.Payment Method') }}:</strong> {{ ucfirst($booking->payment_method) }}</p>
                <p><strong>{{ __('translate.Payment Status') }}:</strong>
                    {{ $booking->payment_status }}
                </p>
            </div>

            @if (!empty($booking->special_requirements))
                <div class="additional-info"
                    style="margin-top: 20px; padding: 15px; border: 1px dashed #ddd; border-radius: 3px;">
                    <h4>{{ __('translate.Special Requirements') }}</h4>
                    <p>{{ $booking->special_requirements }}</p>
                </div>
            @endif
        </div>

        <div class="invoice-footer">
            <p>{{ __('translate.Thank you for your business!') }}</p>
            <p>{{ __('translate.This is a computer-generated invoice, no signature required.') }}</p>
        </div>
    </div>
</body>

</html>
