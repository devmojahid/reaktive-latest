<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class ExtraCharge extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'name',
        'description',
        'price',
        'price_type',
        'is_mandatory',
        'is_tax',
        'tax_percentage',
        'max_quantity',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_tax' => 'boolean',
        'max_quantity' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the service that this extra charge belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Active extra charges scope.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Mandatory extra charges scope.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Tax charges scope.
     */
    public function scopeTax($query)
    {
        return $query->where('is_tax', true);
    }

    /**
     * Per booking charges scope.
     */
    public function scopePerBooking($query)
    {
        return $query->where('price_type', 'per_booking');
    }

    /**
     * Per person charges scope.
     */
    public function scopePerPerson($query)
    {
        return $query->where('price_type', 'per_person');
    }

    /**
     * Get price type formatted as readable text.
     */
    public function getPriceTypeTextAttribute(): string
    {
        return match($this->price_type) {
            'per_booking' => 'Per Booking',
            'per_person' => 'Per Person',
            'per_adult' => 'Per Adult',
            'per_child' => 'Per Child',
            'per_infant' => 'Per Infant',
            'per_night' => 'Per Night',
            'flat' => 'Flat Fee',
            default => 'Unknown',
        };
    }
} 