<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class ServiceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'locale',
        'title',
        'description',
        'short_description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'included',
        'excluded',
        'amenities',
        'facilities',
        'rules',
        'safety',
        'cancellation_policy',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'included' => 'json',
        'excluded' => 'json',
        'amenities' => 'array',
        'facilities' => 'json',
        'rules' => 'json',
        'safety' => 'json',
        'cancellation_policy' => 'json',
    ];

    /**
     * Get the service that this translation belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
