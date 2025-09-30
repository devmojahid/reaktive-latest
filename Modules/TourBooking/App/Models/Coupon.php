<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_amount',
        'discount_percentage',
        'minimum_spend',
        'maximum_discount',
        'usage_limit_per_coupon',
        'usage_limit_per_user',
        'times_used',
        'start_date',
        'end_date',
        'service_types',
        'services',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'minimum_spend' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit_per_coupon' => 'integer',
        'usage_limit_per_user' => 'integer',
        'times_used' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'service_types' => 'json',
        'services' => 'json',
        'status' => 'boolean',
    ];

    /**
     * Check if the coupon is valid.
     */
    public function isValid(): bool
    {
        // Check if coupon is active
        if (!$this->status) {
            return false;
        }
        
        // Check if coupon is expired
        $now = now()->startOfDay();
        if ($this->start_date > $now || $this->end_date < $now) {
            return false;
        }
        
        // Check if coupon has reached usage limit
        if ($this->usage_limit_per_coupon && $this->times_used >= $this->usage_limit_per_coupon) {
            return false;
        }
        
        return true;
    }

    /**
     * Calculate discount amount for the given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->minimum_spend && $subtotal < $this->minimum_spend) {
            return 0;
        }
        
        if ($this->discount_amount) {
            return $this->discount_amount;
        }
        
        if ($this->discount_percentage) {
            $discount = ($subtotal * $this->discount_percentage) / 100;
            
            if ($this->maximum_discount && $discount > $this->maximum_discount) {
                return $this->maximum_discount;
            }
            
            return $discount;
        }
        
        return 0;
    }

    /**
     * Check if the coupon is applicable to a specific service.
     */
    public function isApplicableToService(int $serviceId, int $serviceTypeId): bool
    {
        // If no restrictions, coupon applies to all services
        if (empty($this->service_types) && empty($this->services)) {
            return true;
        }
        
        // Check if the service type is included
        if (!empty($this->service_types)) {
            $serviceTypes = json_decode($this->service_types, true);
            if (in_array($serviceTypeId, $serviceTypes)) {
                return true;
            }
        }
        
        // Check if the specific service is included
        if (!empty($this->services)) {
            $services = json_decode($this->services, true);
            if (in_array($serviceId, $services)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Active coupons scope.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Current coupons scope (not expired).
     */
    public function scopeCurrent($query)
    {
        $now = now()->startOfDay();
        return $query->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now);
    }

    /**
     * Future coupons scope (not started yet).
     */
    public function scopeFuture($query)
    {
        return $query->where('start_date', '>', now()->startOfDay());
    }

    /**
     * Expired coupons scope.
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now()->startOfDay());
    }

    /**
     * Get active and current coupons.
     */
    public function scopeValidNow($query)
    {
        return $query->active()->current();
    }
} 