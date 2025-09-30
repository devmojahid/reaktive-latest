<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class TourItinerary extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'title',
        'day_number',
        'description',
        'location',
        'duration',
        'meal_included',
        'image',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_number' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Get the service (tour) that this itinerary belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Order by day number scope.
     */
    public function scopeOrderByDay($query)
    {
        return $query->orderBy('day_number');
    }

    /**
     * Order by display order scope.
     */
    public function scopeOrderByDisplay($query)
    {
        return $query->orderBy('display_order');
    }
} 