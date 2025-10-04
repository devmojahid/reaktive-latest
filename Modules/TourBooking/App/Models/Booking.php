<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_code',
        'service_id',
        'user_id',
        'check_in_date',
        'check_out_date',
        'check_in_time',
        'check_out_time',
        'adults',
        'children',
        'infants',
        'service_price',
        'child_price',
        'adult_price',
        'infant_price',
        'extra_charges',
        'discount_amount',
        'tax_amount',
        'subtotal',
        'total',
        'paid_amount',
        'due_amount',
        'extra_services',
        'coupon_code',
        'payment_method',
        'payment_status',
        'booking_status',
        'customer_notes',
        'admin_notes',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'custom_fields',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
        'is_reviewed',
        'meta_data',
        'is_per_person',
        'pickup_point_id',
        'pickup_charge',
        'pickup_point_name',
        'age_quantities',
        'age_config',
        'age_breakdown',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
        'service_price' => 'decimal:2',
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'infant_price' => 'decimal:2',
        'extra_charges' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'pickup_charge' => 'decimal:2',
        'extra_services' => 'json',
        'custom_fields' => 'json',
        'meta_data' => 'json',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_reviewed' => 'boolean',
        'is_per_person' => 'boolean',
        'age_quantities' => 'array',
        'age_config'     => 'array',
        'age_breakdown'  => 'array',
    ];

    /**
     * Get the service for this booking.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user who made this booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the review for this booking.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Get the pickup point for this booking.
     */
    public function pickupPoint(): BelongsTo
    {
        return $this->belongsTo(PickupPoint::class);
    }

    /**
     * Calculate the duration of stay in days.
     */
    public function getDurationInDaysAttribute(): int
    {
        if (!$this->check_out_date) {
            return 1;
        }

        return $this->check_in_date->diffInDays($this->check_out_date) ?: 1;
    }

    /**
     * Get total guests count.
     */
    public function getTotalGuestsAttribute(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    /**
     * Pending bookings scope.
     */
    public function scopePending($query)
    {
        return $query->where('booking_status', 'pending');
    }

    /**
     * Confirmed bookings scope.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('booking_status', 'confirmed');
    }

    /**
     * Cancelled bookings scope.
     */
    public function scopeCancelled($query)
    {
        return $query->where('booking_status', 'cancelled');
    }

    /**
     * Completed bookings scope.
     */
    public function scopeCompleted($query)
    {
        return $query->where('booking_status', 'completed');
    }

    /**
     * Payment pending scope.
     */
    public function scopePaymentPending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Payment completed scope.
     */
    public function scopePaymentCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Bookings for date range scope.
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            // Start date falls within the range
            $q->whereBetween('check_in_date', [$startDate, $endDate])
              // Or end date falls within the range
              ->orWhereBetween('check_out_date', [$startDate, $endDate])
              // Or booking spans the entire range
              ->orWhere(function($query) use ($startDate, $endDate) {
                  $query->where('check_in_date', '<=', $startDate)
                        ->where('check_out_date', '>=', $endDate);
              });
        });
    }

    /**
     * Bookings for a specific service scope.
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Bookings for a specific user scope.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Generate a unique booking code.
     */
    public static function generateBookingCode(): string
    {
        $prefix = 'BK';
        $uniqueCode = $prefix . strtoupper(substr(uniqid(), -6)) . rand(10, 99);

        // Ensure the code is unique
        while (self::where('booking_code', $uniqueCode)->exists()) {
            $uniqueCode = $prefix . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
        }

        return $uniqueCode;
    }
}
