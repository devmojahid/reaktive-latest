<?php

namespace Modules\TourBooking\App\Http\Controllers\User;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\TourBooking\App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingInvoiceController extends Controller
{
    /** Afișează factura în browser (HTML print-friendly) */
    public function show(Booking $booking)
    {
        $this->authorizeOwner($booking);

        [$ageBreakdown, $ageQuantities, $ageConfig] = $this->normalizeAges($booking);
        $view = $this->invoiceViewName();

        return view($view, [
            'booking'       => $booking,
            'ageBreakdown'  => $ageBreakdown,
            'ageQuantities' => $ageQuantities,
            'ageConfig'     => $ageConfig,
            'forPdf'        => false,
        ]);
    }

    /** Descarcă factura ca PDF */
    public function download(Booking $booking)
    {
        $this->authorizeOwner($booking);

        [$ageBreakdown, $ageQuantities, $ageConfig] = $this->normalizeAges($booking);
        $view = $this->invoiceViewName();

        $pdf = Pdf::loadView($view, [
            'booking'       => $booking,
            'ageBreakdown'  => $ageBreakdown,
            'ageQuantities' => $ageQuantities,
            'ageConfig'     => $ageConfig,
            'forPdf'        => true,
        ]);

        $code = $booking->booking_code ?: ('BK-' . $booking->id);
        return $pdf->download('invoice-' . $code . '.pdf');
    }

    /** Asigură că utilizatorul logat este proprietarul rezervării */
    private function authorizeOwner(Booking $booking): void
    {
        $uid = (int) auth('web')->id();
        abort_unless($uid && $uid === (int) $booking->user_id, 403);
    }

    /** Returnează numele view-ului de factură (cu fallback) */
    private function invoiceViewName(): string
    {
        if (View::exists('tourbooking::user.booking.invoice')) {
            return 'tourbooking::user.booking.invoice';
        }
        if (View::exists('user.booking.invoice')) {
            return 'user.booking.invoice';
        }
        abort(500, "Invoice view not found. Creează fie 'Modules/TourBooking/resources/views/user/booking/invoice.blade.php' (alias: tourbooking::user.booking.invoice), fie 'resources/views/user/booking/invoice.blade.php'.");
    }

    /** Normalizează categoriile de vârstă */
    private function normalizeAges(Booking $booking): array
    {
        $decode = function ($v) {
            if (is_array($v)) return $v;
            if (is_string($v)) {
                $d = json_decode($v, true);
                return json_last_error() === JSON_ERROR_NONE ? ($d ?: []) : [];
            }
            return [];
        };

        $ageBreakdown  = $decode($booking->age_breakdown ?? []);
        $ageQuantities = $decode($booking->age_quantities ?? []);
        $ageConfig     = $decode($booking->age_config ?? []);

        if (empty($ageBreakdown) && !empty($ageQuantities)) {
            foreach ($ageQuantities as $key => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;

                $label = $ageConfig[$key]['label'] ?? ucfirst((string)$key);
                $price = (float)($ageConfig[$key]['price'] ?? 0);

                $ageBreakdown[$key] = [
                    'label' => $label,
                    'qty'   => $qty,
                    'price' => $price,
                    'line'  => $price * $qty,
                ];
            }
        }

        return [$ageBreakdown, $ageQuantities, $ageConfig];
    }
}
