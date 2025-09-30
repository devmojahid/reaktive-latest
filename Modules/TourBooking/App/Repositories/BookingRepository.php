<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\TourBooking\App\Models\Booking;

final class BookingRepository
{
    /**
     * Get all bookings.
     */
    public function getAll(): Collection
    {
        return Booking::with(['service.translation', 'user'])->orderBy('id', 'desc')->get();
    }

    /**
     * Get paginated bookings.
     */
    public function getPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Booking::with(['service.translation', 'user']);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('booking_status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (!empty($filters['check_in_date'])) {
            $query->where('check_in_date', $filters['check_in_date']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Find booking by ID.
     */
    public function findById(int $id): ?Booking
    {
        return Booking::with(['service.translation', 'service.extraCharges', 'user', 'review'])->find($id);
    }

    /**
     * Find booking by booking code.
     */
    public function findByCode(string $code): ?Booking
    {
        return Booking::with(['service.translation', 'service.extraCharges', 'user', 'review'])
            ->where('booking_code', $code)
            ->first();
    }

    /**
     * Create a new booking.
     */
    public function create(array $data): Booking
    {
        // Generate a unique booking code if not provided
        if (empty($data['booking_code'])) {
            $data['booking_code'] = Booking::generateBookingCode();
        }

        return Booking::create($data);
    }

    /**
     * Update a booking.
     */
    public function update(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    /**
     * Delete a booking.
     */
    public function delete(Booking $booking): bool
    {
        return $booking->delete();
    }

    /**
     * Get bookings for a specific service.
     */
    public function getForService(int $serviceId): Collection
    {
        return Booking::with(['user'])
            ->forService($serviceId)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get bookings for a specific user.
     */
    public function getForUser(int $userId): Collection
    {
        return Booking::with(['service.translation'])
            ->forUser($userId)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get bookings for a specific date range.
     */
    public function getForDateRange(string $startDate, string $endDate): Collection
    {
        return Booking::with(['service.translation', 'user'])
            ->forDateRange($startDate, $endDate)
            ->orderBy('check_in_date')
            ->get();
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Booking $booking, string $status): bool
    {
        $data = ['booking_status' => $status];
        
        if ($status === 'confirmed' && is_null($booking->confirmed_at)) {
            $data['confirmed_at'] = now();
        } elseif ($status === 'cancelled' && is_null($booking->cancelled_at)) {
            $data['cancelled_at'] = now();
        } elseif ($status === 'completed' && is_null($booking->completed_at)) {
            $data['completed_at'] = now();
        }
        
        return $booking->update($data);
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Booking $booking, string $status, float $paidAmount = null): bool
    {
        $data = ['payment_status' => $status];
        
        if (!is_null($paidAmount)) {
            $data['paid_amount'] = $paidAmount;
            $data['due_amount'] = $booking->total - $paidAmount;
        }
        
        return $booking->update($data);
    }

    /**
     * Count bookings by status.
     */
    public function countByStatus(string $status): int
    {
        return Booking::where('booking_status', $status)->count();
    }

    /**
     * Get total revenue.
     */
    public function getTotalRevenue(): float
    {
        return Booking::where('payment_status', 'completed')->sum('paid_amount');
    }

    /**
     * Get revenue for a specific period.
     */
    public function getRevenueForPeriod(string $startDate, string $endDate): float
    {
        return Booking::where('payment_status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('paid_amount');
    }
} 