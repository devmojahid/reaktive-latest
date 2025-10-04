<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class PickupPoint extends Model
{
    use HasFactory;

    /**
     * Attributes assignable in mass.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'extra_charge',
        'charge_type',
        'is_default',
        'status',
        'notes',
    ];

    /**
     * Type casts.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude'     => 'decimal:8',
        'longitude'    => 'decimal:8',
        'extra_charge' => 'decimal:2',
        'is_default'   => 'boolean',
        'status'       => 'boolean',
    ];

    /* =========================================================
     |  Relationships
     * =======================================================*/

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /* =========================================================
     |  Scopes
     * =======================================================*/

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeWithExtraCharge($query)
    {
        return $query->whereNotNull('extra_charge')->where('extra_charge', '>', 0);
    }

    public function scopeFree($query)
    {
        return $query->where(function($q) {
            $q->whereNull('extra_charge')->orWhere('extra_charge', 0);
        });
    }

    /* =========================================================
     |  Helpers
     * =======================================================*/

    /**
     * Check if this pickup point has extra charges.
     */
    public function hasExtraCharge(): bool
    {
        return $this->extra_charge !== null && $this->extra_charge > 0;
    }

    /**
     * Get formatted extra charge.
     */
    public function getFormattedExtraChargeAttribute(): string
    {
        if (!$this->hasExtraCharge()) {
            return __('translate.Free');
        }

        $charge = currency($this->extra_charge);
        
        return match($this->charge_type) {
            'per_person' => $charge . ' / ' . __('translate.Person'),
            'per_adult'  => $charge . ' / ' . __('translate.Adult'),
            'per_child'  => $charge . ' / ' . __('translate.Child'),
            default      => $charge,
        };
    }

    /**
     * Calculate distance from given coordinates (in kilometers).
     */
    public function distanceFrom(float $lat, float $lng): float
    {
        $earthRadius = 6371; // km

        $deltaLat = deg2rad($lat - $this->latitude);
        $deltaLng = deg2rad($lng - $this->longitude);

        $a = sin($deltaLat/2) * sin($deltaLat/2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) *
             sin($deltaLng/2) * sin($deltaLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Get coordinates as array.
     */
    public function getCoordinatesAttribute(): array
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    /**
     * Calculate extra charge based on charge type and quantities.
     */
    public function calculateExtraCharge(array $quantities = []): float
    {
        if (!$this->hasExtraCharge()) {
            return 0.0;
        }

        return match($this->charge_type) {
            'per_person' => $this->extra_charge * array_sum($quantities),
            'per_adult'  => $this->extra_charge * ($quantities['adult'] ?? 0),
            'per_child'  => $this->extra_charge * (($quantities['child'] ?? 0) + ($quantities['baby'] ?? 0) + ($quantities['infant'] ?? 0)),
            default      => $this->extra_charge, // flat rate
        };
    }
}
