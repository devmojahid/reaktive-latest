<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\View\View;
use Modules\TourBooking\App\Models\Availability;
use Modules\TourBooking\App\Models\Booking;
use Modules\TourBooking\App\Models\Coupon;
use Modules\TourBooking\App\Models\ExtraCharge;
use Modules\TourBooking\App\Models\Review;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\PickupPoint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Modules\Currency\App\Models\Currency;
use Modules\PaymentGateway\App\Models\PaymentGateway;

final class FrontBookingController extends Controller
{
    /**
     * Display the booking form for a service.
     */
    public function bookingCheckoutView(Request $request)
    {
        // Store intended URL if coming from booking process
        if ($request->has('intended_from') && $request->intended_from === 'booking') {
            $intendedUrl = url()->current() . '?' . http_build_query($request->except(['intended_from']));
            session(['url.intended' => $intendedUrl]);
            
            // Debug log
            Log::info('Storing intended URL for booking flow', [
                'intended_url' => $intendedUrl,
                'session_id' => session()->getId()
            ]);
        }

        // ---- Payment settings (nemodificat conceptual) -----------------------
        $payment_data = PaymentGateway::all();
        foreach ($payment_data as $data_item) {
            $payment_setting[$data_item->key] = $data_item->value;
        }
        $payment_setting = (object) ($payment_setting ?? []);

        $razorpay_currency    = Currency::findOrFail($payment_setting->razorpay_currency_id);
        $flutterwave_currency = Currency::findOrFail($payment_setting->flutterwave_currency_id);
        $paystack_currency    = Currency::findOrFail($payment_setting->paystack_currency_id);

        $auth_user = Auth::guard('web')->user();

        /** @var Service $service */
        $service = Service::where('id', $request->service_id)
            ->where('status', true)
            ->with('availabilities')
            ->firstOrFail();

        // data este obligatorie pentru calculul corect pe availability
        $date = $request->input('check_in_date');
        if (!$date) {
            return back()->with(['message' => __('translate.Please select a date'), 'alert-type' => 'error']);
        }

        // availability: by id (dacă vine) sau by date (fără să condiționăm de is_available)
        $availability = null;
        if ($request->filled('availability_id')) {
            $availability = Availability::find($request->availability_id);
            if (!$availability || $availability->service_id !== $service->id) {
                $availability = null;
            }
        }
        if (!$availability) {
            $availability = Availability::where('service_id', $service->id)
                ->whereDate('date', $date)
                ->first();
        }

        // === CANTITĂȚI: preferăm age_quantities; fallback la person/children ===
        $qty = [
            'adult'  => 0,
            'child'  => 0,
            'baby'   => 0,
            'infant' => 0,
        ];

        $usedAgePricing = false;
        if (is_array($request->age_quantities)) {
            foreach ($qty as $k => $_) {
                $qty[$k] = max(0, (int)($request->age_quantities[$k] ?? 0));
            }
            $usedAgePricing = array_sum($qty) > 0;
        }

        if (!$usedAgePricing) {
            // UI vechi
            $qty['adult'] = max(0, (int)$request->person);
            $qty['child'] = max(0, (int)$request->children);
        }

        // === Verificare locuri pe availability (dacă există limită) ============
        if ($availability && $availability->available_spots !== null) {
            $totalGuests = array_sum($qty);
            if ($totalGuests > (int)$availability->available_spots) {
                $notify_message = trans('translate.Not enough available spots for the selected date');
                return back()->with(['message' => $notify_message, 'alert-type' => 'error']);
            }
        }

        // === Extra-uri selectate ===============================================
        $extraCharges = ExtraCharge::select('id', 'name', 'price', 'price_type')
            ->whereIn('id', $request->extras ?? [])
            ->where('status', true)
            ->get();

        // === Pickup Point Charges =============================================
        $pickupCharge = 0.0;
        $pickupPointName = null;
        if ($request->filled('pickup_point_id')) {
            $pickupPoint = PickupPoint::find($request->pickup_point_id);
            if ($pickupPoint && $pickupPoint->service_id === $service->id) {
                $pickupCharge = $pickupPoint->calculateExtraCharge($qty);
                $pickupPointName = $pickupPoint->name;
            }
        }

        // === PREȚURI unitare pe categorii (folosim Service model methods) ==
        $unit = $service->effectivePriceSetForDate($date);
        


        // === Liniile de comandă & total ========================================
        $lines = [];
        $total = 0.0;

        if ($service->is_per_person) {
            foreach (['adult','child','baby','infant'] as $k) {
                $count = (int)$qty[$k];
                if ($count <= 0) continue;

                $price = (float)($unit[$k] ?? 0);
                $line  = $count * $price;
                $total += $line;

                $lines[] = [
                    'label'    => ucfirst($k),
                    'key'      => $k,  // Store original age category key
                    'qty'      => $count,
                    'unit'     => $price,
                    'subtotal' => $line,
                    'is_extra' => false,
                ];
            }
        } else {
            $fixed = (float)($service->discount_price ?? $service->full_price ?? 0);
            $lines[] = [
                'label'    => $service->translation->title ?? 'Service',
                'key'      => 'service',
                'qty'      => 1,
                'unit'     => $fixed,
                'subtotal' => $fixed,
                'is_extra' => false,
            ];
            $total += $fixed;
        }

        // Extra-uri
        foreach ($extraCharges as $e) {
            $price = (float)$e->price;
            $lines[] = [
                'label'    => $e->name,
                'key'      => 'extra_' . $e->id,
                'qty'      => 1,
                'unit'     => $price,
                'subtotal' => $price,
                'is_extra' => true,
            ];
            $total += $price;
        }

        // Pickup Point Charge
        if ($pickupCharge > 0 && $pickupPointName) {
            $lines[] = [
                'label'    => 'Pickup: ' . $pickupPointName,
                'key'      => 'pickup_charge',
                'qty'      => 1,
                'unit'     => $pickupCharge,
                'subtotal' => $pickupCharge,
                'is_extra' => true,
            ];
            $total += $pickupCharge;
        }

        // Compat vechi (person/children)
        $personCount = $qty['adult'] ?? 0;
        $childCount  = $qty['child'] ?? 0;
        $personPrice = $personCount * (float)($unit['adult'] ?? 0);
        $childPrice  = $childCount  * (float)($unit['child'] ?? 0);

        $data = [
            'service'       => $service,
            'extras'        => $extraCharges,
            'lines'         => $lines,
            'total'         => $total,

            // compatibilitate cu view-ul existent
            'personCount'   => $personCount,
            'childCount'    => $childCount,
            'personPrice'   => $personPrice,
            'childPrice'    => $childPrice,

            'ageQuantities' => $qty,
        ];

        // === Create age config for booking storage ===
        $ageConfig = [];
        $ageBreakdown = [];
        
        foreach ($lines as $line) {
            if (!($line['is_extra'] ?? false) && isset($line['key'])) {
                $key = $line['key']; // Use original age category key
                $ageConfig[$key] = [
                    'label' => $line['label'],
                    'price' => $line['unit'],
                ];
                $ageBreakdown[$key] = [
                    'label' => $line['label'],
                    'qty'   => $line['qty'],
                    'price' => $line['unit'],
                    'line'  => $line['subtotal'],
                ];
            }
        }

        // === Sesiune (fără dublări) ============================================
        session()->forget('payment_cart');
        session()->put('payment_cart', [
            'service_id'      => $service->id,
            'check_in_date'   => $date,
            'check_out_date'  => $request->check_out_date,
            'check_in_time'   => $request->check_in_time == 'on' ? $request->check_in_time_hidden : null,
            'check_out_time'  => $request->check_out_time == 'on' ? $request->check_out_time_hidden : null,
            'person_count'    => 0,
            'child_count'     => 0,
            'age_quantities'  => $qty,
            'age_config'      => $ageConfig,
            'age_breakdown'   => $ageBreakdown,
            'total'           => $total,
            'extra_charges'   => $extraCharges->sum('price'),
            'extra_services'  => $extraCharges->pluck('id')->all(),
            'availability_id' => $availability?->id,
            'pickup_point_id' => $request->pickup_point_id,
            'pickup_charge'   => $pickupCharge,
            'pickup_point_name' => $pickupPointName,
        ]);

        return view('tourbooking::front.bookings.checkout-view', [
            'service'              => $service,
            'data'                 => $data,
            'payment_setting'      => $payment_setting,
            'razorpay_currency'    => $razorpay_currency,
            'flutterwave_currency' => $flutterwave_currency,
            'paystack_currency'    => $paystack_currency,
            'user'                 => $auth_user,
            'availability'         => $availability,
        ]);
    }

    /**
     * Process a new booking.
     */
    public function processBooking(Request $request, string $slug): RedirectResponse
    {
        $service = Service::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'extra_services' => 'nullable|array',
            'coupon_code' => 'nullable|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'payment_method' => 'required|string|in:paypal,stripe,bank_transfer',
        ]);

        // Verify availability
        $this->verifyServiceAvailability($service, $validated['check_in_date'], $validated['check_out_date'] ?? null);

        // Calculate prices
        $priceDetails = $this->calculateBookingPrice(
            $service,
            (int) $validated['adults'],
            (int) ($validated['children'] ?? 0),
            (int) ($validated['infants'] ?? 0),
            $validated['extra_services'] ?? [],
            $validated['coupon_code'] ?? null
        );

        // Create booking data
        $bookingData = [
            'service_id' => $service->id,
            'booking_code' => Booking::generateBookingCode(),
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'infants' => $validated['infants'] ?? 0,
            'service_price' => $service->discounted_price,
            'child_price' => $service->child_price,
            'infant_price' => $service->infant_price,
            'extra_charges' => $priceDetails['extra_charges'],
            'discount_amount' => $priceDetails['discount_amount'],
            'tax_amount' => $priceDetails['tax_amount'],
            'subtotal' => $priceDetails['subtotal'],
            'total' => $priceDetails['total'],
            'paid_amount' => 0,
            'due_amount' => $priceDetails['total'],
            'extra_services' => $validated['extra_services'] ?? [],
            'coupon_code' => $validated['coupon_code'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_notes' => $validated['customer_notes'] ?? null,
        ];

        if (Auth::check()) {
            $bookingData['user_id'] = Auth::id();
        }

        $booking = Booking::create($bookingData);

        switch ($validated['payment_method']) {
            case 'paypal':
                return redirect()->route('front.tourbooking.payment.paypal', $booking->booking_code);
            case 'stripe':
                return redirect()->route('front.tourbooking.payment.stripe', $booking->booking_code);
            case 'bank_transfer':
            default:
                return redirect()->route('front.tourbooking.confirm-booking', $booking->booking_code);
        }
    }

    /**
     * Display the booking confirmation page.
     */
    public function confirmBooking(string $code): View
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['service', 'service.media', 'user'])
            ->firstOrFail();

        return view('tourbooking::front.bookings.confirm', compact('booking'));
    }

    /**
     * Display the booking success page.
     */
    public function bookingSuccess(string $code): View
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['service', 'user'])
            ->firstOrFail();

        return view('tourbooking::front.bookings.success', compact('booking'));
    }

    /**
     * Display the booking cancel page.
     */
    public function bookingCancel(string $code): View
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['service', 'user'])
            ->firstOrFail();

        return view('tourbooking::front.bookings.cancel', compact('booking'));
    }

    /**
     * Check availability for a service.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        try {
            $this->verifyServiceAvailability($service, $validated['check_in_date'], $validated['check_out_date'] ?? null);

            $priceDetails = $this->calculateBookingPrice(
                $service,
                (int) $validated['adults'],
                (int) ($validated['children'] ?? 0),
                (int) ($validated['infants'] ?? 0)
            );

            return response()->json([
                'available' => true,
                'message' => 'Service is available for the selected dates.',
                'pricing' => $priceDetails,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Validate a coupon code.
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'check_in_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $validated['coupon_code'])
            ->where('status', true)
            ->where(function ($query) {
                $query->where('expires_at', '>=', now())
                    ->orWhereNull('expires_at');
            })
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired coupon code.',
            ], 422);
        }

        if ($coupon->service_id && $coupon->service_id != $validated['service_id']) {
            return response()->json([
                'valid' => false,
                'message' => 'This coupon is not valid for the selected service.',
            ], 422);
        }

        if ($coupon->usage_limit && $coupon->times_used >= $coupon->usage_limit) {
            return response()->json([
                'valid' => false,
                'message' => 'This coupon has reached its usage limit.',
            ], 422);
        }

        $subtotal = (float) $validated['subtotal'];
        $discountAmount = 0;

        if ($coupon->discount_type == 'percentage') {
            $discountAmount = $subtotal * ($coupon->discount_value / 100);
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            $discountAmount = $coupon->discount_value;
            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }
        }

        return response()->json([
            'valid' => true,
            'message' => 'Coupon applied successfully.',
            'discount_amount' => $discountAmount,
            'coupon_data' => $coupon,
        ]);
    }

    /**
     * Display user's bookings.
     */
    public function myBookings(): View
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['service', 'service.thumbnail'])
            ->latest()
            ->paginate(10);

        return view('tourbooking::front.bookings', compact('bookings'));
    }

    /**
     * Display a specific booking's details.
     */
    public function bookingDetails(string $code): View
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->with(['service', 'service.media', 'review'])
            ->firstOrFail();

        return view('tourbooking::front.bookings.details', compact('booking'));
    }

    /**
     * Display an invoice for the booking.
     */
    public function invoice(string $code): View
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->with(['service', 'service.serviceType'])
            ->firstOrFail();

        return view('tourbooking::front.bookings.invoice', compact('booking'));
    }

    /**
     * Generate a PDF invoice for the booking.
     */
    public function downloadInvoicePdf(string $code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->with(['service', 'service.serviceType'])
            ->firstOrFail();

        $pdf = PDF::loadView('tourbooking::front.bookings.invoice', compact('booking'))
            ->setPaper('a4')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10);

        $filename = 'invoice-' . $booking->booking_code . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(Request $request, string $code): RedirectResponse
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->where('booking_status', '!=', 'cancelled')
            ->where('booking_status', '!=', 'completed')
            ->firstOrFail();

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'booking_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()->route('front.tourbooking.my-bookings')
            ->with('success', 'Your booking has been cancelled.');
    }

    /**
     * Submit a review for a completed booking.
     */
    public function leaveReview(Request $request, string $code): RedirectResponse
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', Auth::id())
            ->where('booking_status', 'completed')
            ->where('is_reviewed', false)
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
            'title' => 'required|string|max:100',
        ]);

        $review = Review::create([
            'service_id' => $booking->service_id,
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'content' => $validated['review_text'],
            'status' => false, // Pending approval
        ]);

        $booking->update(['is_reviewed' => true]);

        return redirect()->route('front.tourbooking.my-bookings')
            ->with('success', 'Your review has been submitted and is pending approval.');
    }

    /**
     * Verify service availability for the selected date.
     */
    private function verifyServiceAvailability(Service $service, string $checkInDate, ?string $checkOutDate = null): bool
    {
        $checkInDate = \Carbon\Carbon::parse($checkInDate);
        $checkOutDate = $checkOutDate ? \Carbon\Carbon::parse($checkOutDate) : $checkInDate;

        $hasAvailabilityRecords = $service->availabilities()->exists();

        if ($hasAvailabilityRecords) {
            $availability = $service->availabilities()
                ->where('date', $checkInDate->format('Y-m-d'))
                ->where('is_available', true)
                ->first();

            if (!$availability) {
                throw new \Exception('The service is not available for the selected date.');
            }

            if ($availability->available_spots !== null) {
                $existingBookingsCount = Booking::where('service_id', $service->id)
                    ->where('booking_status', '!=', 'cancelled')
                    ->whereDate('check_in_date', $checkInDate)
                    ->sum('adults')
                    + Booking::where('service_id', $service->id)
                    ->where('booking_status', '!=', 'cancelled')
                    ->whereDate('check_in_date', $checkInDate)
                    ->sum('children');

                if ($existingBookingsCount >= $availability->available_spots) {
                    throw new \Exception('Not enough spots available for the selected date.');
                }
            }
        }

        $conflictingBookings = Booking::where('service_id', $service->id)
            ->where('booking_status', '!=', 'cancelled')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                    ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('check_in_date', '<=', $checkInDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                    });
            })
            ->exists();

        if ($conflictingBookings) {
            throw new \Exception('The service is already booked for the selected dates.');
        }

        return true;
    }

    /**
     * Calculate booking price details.
     */
    private function calculateBookingPrice(
        Service $service,
        int $adults,
        int $children = 0,
        int $infants = 0,
        array $extraServices = [],
        ?string $couponCode = null
    ): array {
        $basePrice = 0;

        if ($service->price_per_person) {
            $basePrice = ($adults * $service->discounted_price)
                + ($children * ($service->child_price ?? 0))
                + ($infants * ($service->infant_price ?? 0));
        } else {
            $basePrice = $service->discounted_price;
        }

        $extraChargesAmount = 0;

        if (!empty($extraServices)) {
            $extraChargesIds = array_keys($extraServices);
            $extraCharges = ExtraCharge::whereIn('id', $extraChargesIds)
                ->where('service_id', $service->id)
                ->get();

            foreach ($extraCharges as $charge) {
                $quantity = $extraServices[$charge->id] ?? 1;
                $extraChargesAmount += $charge->price * $quantity;
            }
        }

        $subtotal = $basePrice + $extraChargesAmount;

        $discountAmount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('status', true)
                ->where(function ($query) {
                    $query->where('expires_at', '>=', now())
                        ->orWhereNull('expires_at');
                })
                ->first();

            if ($coupon && (!$coupon->service_id || $coupon->service_id == $service->id)) {
                if ($coupon->discount_type == 'percentage') {
                    $discountAmount = $subtotal * ($coupon->discount_value / 100);
                    if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                        $discountAmount = $coupon->max_discount_amount;
                    }
                } else {
                    $discountAmount = $coupon->discount_value;
                    if ($discountAmount > $subtotal) {
                        $discountAmount = $subtotal;
                    }
                }
            }
        }

        $taxAmount = 0;
        $taxPercentage = config('tourbooking.tax_percentage', 0);

        if ($taxPercentage > 0) {
            $taxAmount = ($subtotal - $discountAmount) * ($taxPercentage / 100);
        }

        $total = $subtotal - $discountAmount + $taxAmount;

        return [
            'base_price' => $basePrice,
            'extra_charges' => $extraChargesAmount,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ];
    }

    /* =======================
     * Helpers pentru pricing
     * ======================= */

    /** returnează prețurile unitare pe categorii pentru ziua selectată */
    private function computeCategoryPrices(Service $service, ?Availability $availability): array
    {
        $keys = ['adult','child','baby','infant'];
        $out  = array_fill_keys($keys, 0.0);

        // Normalize JSON → array
        $svcCats = $this->parseCats($service->age_categories ?? null);
        $avCats  = $this->parseCats($availability?->age_categories ?? null);

        $avHasActive  = $this->hasActive($avCats);
        $svcHasActive = $this->hasActive($svcCats);

        // baza din service
        $baseAdult  = $this->firstNumeric([$service->price_per_person, $service->discount_price, $service->full_price, 0]);
        $baseChild  = $this->firstNumeric([$service->child_price, $baseAdult]);
        $baseInfant = $this->firstNumeric([$service->infant_price, $baseAdult]);
        $baseMap    = ['adult'=>$baseAdult,'child'=>$baseChild,'baby'=>$baseChild,'infant'=>$baseInfant];

        // A) availability age-groups prioritar; completează din service, apoi bază
        if ($avHasActive) {
            foreach ($keys as $k) {
                if (!empty($avCats[$k]['enabled']) && $this->isNum($avCats[$k]['price'])) {
                    $out[$k] = (float)$avCats[$k]['price'];
                } elseif (!empty($svcCats[$k]['enabled']) && $this->isNum($svcCats[$k]['price'])) {
                    $out[$k] = (float)$svcCats[$k]['price'];
                } else {
                    // LEGACY doar când nu avem age-groups active deloc; aici mergem pe bază
                    $out[$k] = (float)$baseMap[$k];
                }
            }
            return $out;
        }

        // B) service age-groups
        if ($svcHasActive) {
            foreach ($keys as $k) {
                if (!empty($svcCats[$k]['enabled']) && $this->isNum($svcCats[$k]['price'])) {
                    $out[$k] = (float)$svcCats[$k]['price'];
                } else {
                    $out[$k] = (float)$baseMap[$k];
                }
            }
            return $out;
        }

        // C) legacy availability (special_price/per_children_price), altfel baza
        $legacyAdult = $availability?->special_price;
        $legacyChild = $availability?->per_children_price;

        $out['adult'] = $this->isNum($legacyAdult) ? (float)$legacyAdult : (float)$baseAdult;
        $out['child'] = $this->isNum($legacyChild) ? (float)$legacyChild : (float)$baseChild;
        $out['baby']  = (float)$baseChild;
        $out['infant']= (float)$baseInfant;

        return $out;
    }

    private function parseCats($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        } elseif (!is_array($value)) {
            $value = [];
        }
        $keys = ['adult','child','baby','infant'];
        $out = [];
        foreach ($keys as $k) {
            $row = $value[$k] ?? [];
            $out[$k] = [
                'enabled' => (bool)($row['enabled'] ?? false),
                'price'   => $row['price'] ?? null,
            ];
        }
        return $out;
    }

    private function hasActive(array $cats): bool
    {
        foreach ($cats as $row) {
            if (!empty($row['enabled']) && $this->isNum($row['price'])) return true;
        }
        return false;
    }

    private function isNum($v): bool
    {
        return $v !== null && $v !== '' && is_numeric($v);
    }

    private function firstNumeric(array $candidates): float
    {
        foreach ($candidates as $c) {
            if ($this->isNum($c)) return (float)$c;
        }
        return 0.0;
    }
}
