<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\TourBooking\App\Models\Booking;
use Modules\TourBooking\App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

final class BookingController extends Controller
{


    public function index(): View
    {
        $bookings = Booking::with(['service:id,title,location'])
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->get();
        return view('tourbooking::user.booking.index', compact('bookings'));
    }

    public function details(Request $request): View
    {
        $booking = Booking::with(['service.translation', 'user'])
            ->where('user_id', auth()->user()->id)
            ->findOrFail($request->id);

        return view('tourbooking::user.booking.details', compact('booking'));
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(Request $request, $id): RedirectResponse
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::user()->id)
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

        // Notification logic can be added here

        return redirect()->route('user.bookings.index')->with('success', 'Your booking has been cancelled.');
    }
}
